<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Charge;
use App\Models\ProductStockInventory;
use Illuminate\Support\Collection;

/**
 * Builds the shopping cart with authoritative server-side pricing.
 *
 * Every price shown to the customer (cart, checkout, order placement and
 * the AJAX quantity/remove recalculations) flows through this service so the
 * numbers can never drift apart.
 */
class CartService
{
    /**
     * Resolve the stock inventory row for a product + optional variant.
     *
     * Note: tbl_product_stock_inventory.prod_variant_id is always a real
     * variant id (never NULL), so a "no variant" lookup must NOT use
     * whereNull() - it would silently miss the row and prices would collapse
     * to zero.
     */
    public static function stockFor(int $productId, $variantId = null): ?ProductStockInventory
    {
        $query = ProductStockInventory::where('prod_id', $productId);

        if (!empty($variantId)) {
            $query->where('prod_variant_id', $variantId);
        }

        return $query->orderBy('id')->first();
    }

    /**
     * Calculate the per-unit selling price and GST details from stock.
     *
     * @return array{unit_price: float, gst_rate: float, gst_type: string, cgst_rate: float, sgst_rate: float}
     */
    public static function priceContextFor(int $productId, $variantId = null, $product = null): array
    {
        $stock = static::stockFor($productId, $variantId);

        if ($stock) {
            return [
                'unit_price' => (float) ($stock->discounted_unit_price ?? $stock->unit_price ?? 0),
                'gst_rate'   => (float) ($stock->gst_rate ?? 18),
                'gst_type'   => (string) ($stock->gst_type ?? 'inclusive'),
                'cgst_rate'  => (float) ($stock->cgst_rate ?? ($stock->gst_rate / 2)),
                'sgst_rate'  => (float) ($stock->sgst_rate ?? ($stock->gst_rate / 2)),
            ];
        }

        if ($product) {
            $price = (float) ($product->discounted_price ?? $product->p_price ?? 0);

            return [
                'unit_price' => $price,
                'gst_rate'   => 18,
                'gst_type'   => 'inclusive',
                'cgst_rate'  => 9,
                'sgst_rate'  => 9,
            ];
        }

        return [
            'unit_price' => 0,
            'gst_rate'   => 18,
            'gst_type'   => 'inclusive',
            'cgst_rate'  => 9,
            'sgst_rate'  => 9,
        ];
    }

    /**
     * Enrich every cart row with authoritative pricing.
     *
     * @param  int  $custId
     * @return Collection<int, CartItem>
     */
    public static function pricedLines(int $custId): Collection
    {
        $items = CartItem::where('cust_id', $custId)
            ->with(['product.category', 'variant'])
            ->get();

        $customMeta = session('cart_customizations', []);

        foreach ($items as $item) {
            $context = static::priceContextFor($item->product_id, $item->variant_id, $item->product);

            $metaKey = $item->product_id . '|' . ($item->variant_id ?: 'none');
            $surcharge = isset($customMeta[$metaKey]) ? (float) ($customMeta[$metaKey]['price'] ?? 0) : 0;
            $option = $customMeta[$metaKey]['option'] ?? null;
            $text = $customMeta[$metaKey]['text'] ?? null;

            // A customization surcharge is added to the displayed unit price
            // and then taxed alongside the base price.
            $displayUnit = $context['unit_price'] + $surcharge;

            $pricing = PricingService::itemPricing(
                $displayUnit,
                $context['gst_rate'],
                $context['gst_type'],
                $item->quantity
            );

            $item->display_unit = $displayUnit; // stored selling unit price + surcharge
            $item->price = $pricing['unit_price_incl_gst']; // per-unit, GST inclusive
            $item->base_price = $pricing['base_unit_price'];
            $item->line_total = $pricing['line_total_incl_gst'];
            $item->gst_rate = $pricing['gst_rate'];
            $item->gst_type = $pricing['gst_type'];
            $item->cgst_rate = $pricing['cgst_rate'];
            $item->sgst_rate = $pricing['sgst_rate'];
            $item->line_cgst = $pricing['line_cgst'];
            $item->line_sgst = $pricing['line_sgst'];
            $item->line_gst = $pricing['line_gst'];
            $item->line_base = $pricing['line_base'];
            $item->surcharge = $surcharge;
            $item->customization_option = $option;
            $item->customization_text = $text;
            $item->stock = static::stockFor($item->product_id, $item->variant_id);
        }

        return $items;
    }

    /**
     * Calculate the full charge breakdown for the current cart.
     *
     * @param  float  $subtotalInclusive  sum of item prices including GST
     * @param  float  $subtotalBase       sum of item base prices (before GST)
     * @return Collection<int, Charge>    charges enriched with pricing fields
     */
    public static function charges(float $subtotalInclusive, float $subtotalBase): Collection
    {
        $charges = Charge::where('charge_status', 1)->get();

        return $charges->map(function (Charge $charge) use ($subtotalInclusive, $subtotalBase) {
            $waived = PricingService::isChargeWaived($charge, $subtotalInclusive);
            $base = $waived ? 0.0 : PricingService::chargeBaseAmount($charge, $subtotalBase, $subtotalInclusive);

            $pricing = PricingService::chargePricing(
                $base,
                $charge->gst_rate,
                $charge->gst_type,
                $charge->cgst_rate,
                $charge->sgst_rate
            );

            $charge->base_amount = $pricing['base_amount'];
            $charge->gst_rate = $pricing['gst_rate'];
            $charge->gst_type = $pricing['gst_type'];
            $charge->cgst_rate = $pricing['cgst_rate'];
            $charge->sgst_rate = $pricing['sgst_rate'];
            $charge->cgst_amount = $pricing['cgst_amount'];
            $charge->sgst_amount = $pricing['sgst_amount'];
            $charge->gst_amount = $pricing['gst_amount'];
            // Display amount is the GST-inclusive total (matches historical invoices).
            $charge->calculated_amount = $pricing['total_amount_incl_gst'];
            $charge->total_amount = $pricing['total_amount_incl_gst'];
            $charge->applied = !$waived;

            return $charge;
        });
    }

    /**
     * Build the complete, authoritative cart summary.
     *
     * @return array{
     *     items: Collection,
     *     items_qty: int,
     *     subtotal_inclusive: float,
     *     subtotal_base: float,
     *     product_cgst: float,
     *     product_sgst: float,
     *     product_gst: float,
     *     charges: Collection,
     *     charges_total: float,
     *     charges_gst: float,
     *     grand_total: float,
     * }
     */
    public static function build(int $custId): array
    {
        $items = static::pricedLines($custId);

        $subtotalInclusive = round($items->sum('line_total'), 2);
        $subtotalBase = round($items->sum('line_base'), 2);
        $productCgst = round($items->sum('line_cgst'), 2);
        $productSgst = round($items->sum('line_sgst'), 2);
        $productGst = round($items->sum('line_gst'), 2);

        $charges = static::charges($subtotalInclusive, $subtotalBase);

        $chargesTotal = round($charges->sum('total_amount'), 2);
        $chargesGst = round($charges->sum('gst_amount'), 2);

        return [
            'items'             => $items,
            'items_qty'         => (int) $items->sum('quantity'),
            'subtotal_inclusive'=> $subtotalInclusive,
            'subtotal_base'     => $subtotalBase,
            'product_cgst'      => $productCgst,
            'product_sgst'      => $productSgst,
            'product_gst'       => $productGst,
            'charges'           => $charges,
            'charges_total'     => $chargesTotal,
            'charges_gst'       => $chargesGst,
            'grand_total'       => round($subtotalInclusive + $chargesTotal, 2),
        ];
    }
}
