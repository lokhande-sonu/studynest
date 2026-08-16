<?php

namespace App\Services;

use App\Models\Charge;

/**
 * Central pricing / GST calculation service.
 *
 * Both the web and the mobile API use this class so that every surface
 * (cart, checkout, order placement, invoices, order details) produces the
 * same numbers.
 *
 * Conventions:
 *  - "base" / "exclusive" amounts exclude GST.
 *  - "inclusive" amounts include GST.
 *  - Per-line amounts are already multiplied by quantity.
 */
class PricingService
{
    /**
     * Build the pricing breakdown for a single order line.
     *
     * @param  float  $unitPrice  selling unit price as stored (inclusive or exclusive depending on $gstType)
     * @param  float  $gstRate    GST rate in % (e.g. 18)
     * @param  string $gstType    'inclusive' | 'exclusive'
     * @param  int    $qty
     * @return array{
     *     base_unit_price: float,
     *     gst_rate: float,
     *     gst_type: string,
     *     cgst_rate: float,
     *     sgst_rate: float,
     *     unit_price_incl_gst: float,
     *     cgst_per_unit: float,
     *     sgst_per_unit: float,
     *     gst_per_unit: float,
     *     line_base: float,
     *     line_cgst: float,
     *     line_sgst: float,
     *     line_gst: float,
     *     line_total_incl_gst: float,
     * }
     */
    public static function itemPricing(float $unitPrice, float $gstRate, string $gstType, int $qty): array
    {
        $qty = max(1, (int) $qty);
        $gstRate = (float) $gstRate;
        $unitPrice = round((float) $unitPrice, 2);

        $cgstRate = $gstRate / 2;
        $sgstRate = $gstRate - $cgstRate;

        if ($gstType === 'exclusive') {
            $baseUnit = $unitPrice;
            $cgstPerUnit = round($unitPrice * $cgstRate / 100, 2);
            $sgstPerUnit = round($unitPrice * $sgstRate / 100, 2);
            $gstPerUnit = round($cgstPerUnit + $sgstPerUnit, 2);
            $unitIncl = round($unitPrice + $gstPerUnit, 2);
        } else {
            // Inclusive: back out the base from the GST-inclusive price.
            $baseUnit = round($unitPrice / (1 + ($gstRate / 100)), 2);
            $gstPerUnit = round($unitPrice - $baseUnit, 2);
            $cgstPerUnit = round($gstPerUnit / 2, 2);
            $sgstPerUnit = round($gstPerUnit - $cgstPerUnit, 2);
            $unitIncl = $unitPrice;
        }

        return [
            'base_unit_price'    => $baseUnit,
            'gst_rate'           => $gstRate,
            'gst_type'           => $gstType,
            'cgst_rate'          => round($cgstRate, 2),
            'sgst_rate'          => round($sgstRate, 2),
            'unit_price_incl_gst'=> $unitIncl,
            'cgst_per_unit'      => $cgstPerUnit,
            'sgst_per_unit'      => $sgstPerUnit,
            'gst_per_unit'       => $gstPerUnit,
            'line_base'          => round($baseUnit * $qty, 2),
            'line_cgst'          => round($cgstPerUnit * $qty, 2),
            'line_sgst'          => round($sgstPerUnit * $qty, 2),
            'line_gst'           => round(($cgstPerUnit + $sgstPerUnit) * $qty, 2),
            'line_total_incl_gst'=> round($unitIncl * $qty, 2),
        ];
    }

    /**
     * Price a single charge (delivery / processing / ...) and apply GST.
     *
     * @param  float      $baseAmount  charge value before GST
     * @param  float|null $gstRate
     * @param  string|null $gstType
     * @param  float|null $cgstRate
     * @param  float|null $sgstRate
     * @return array{
     *     base_amount: float,
     *     gst_rate: float,
     *     gst_type: string,
     *     cgst_rate: float,
     *     sgst_rate: float,
     *     cgst_amount: float,
     *     sgst_amount: float,
     *     gst_amount: float,
     *     total_amount_incl_gst: float,
     * }
     */
    public static function chargePricing(
        float $baseAmount,
        ?float $gstRate = 0,
        ?string $gstType = 'inclusive',
        ?float $cgstRate = null,
        ?float $sgstRate = null
    ): array {
        $baseAmount = round((float) $baseAmount, 2);
        $gstRate = (float) ($gstRate ?? 0);
        $gstType = ($gstType === 'exclusive') ? 'exclusive' : 'inclusive';

        if ($gstRate <= 0) {
            return [
                'base_amount'          => $baseAmount,
                'gst_rate'             => 0,
                'gst_type'             => $gstType,
                'cgst_rate'            => 0,
                'sgst_rate'            => 0,
                'cgst_amount'          => 0,
                'sgst_amount'          => 0,
                'gst_amount'           => 0,
                'total_amount_incl_gst'=> $baseAmount,
            ];
        }

        $cgstRate = ($cgstRate !== null) ? (float) $cgstRate : $gstRate / 2;
        $sgstRate = ($sgstRate !== null) ? (float) $sgstRate : $gstRate - $cgstRate;

        if ($gstType === 'exclusive') {
            $cgstAmount = round($baseAmount * $cgstRate / 100, 2);
            $sgstAmount = round($baseAmount * $sgstRate / 100, 2);
            $total = round($baseAmount + $cgstAmount + $sgstAmount, 2);
        } else {
            // Inclusive: the stored value already contains GST, back it out.
            $baseBeforeGst = round($baseAmount / (1 + ($gstRate / 100)), 2);
            $gstAmount = round($baseAmount - $baseBeforeGst, 2);
            $cgstAmount = round($gstAmount / 2, 2);
            $sgstAmount = round($gstAmount - $cgstAmount, 2);
            $total = $baseAmount;
        }

        return [
            'base_amount'          => $baseAmount,
            'gst_rate'             => $gstRate,
            'gst_type'             => $gstType,
            'cgst_rate'            => round($cgstRate, 2),
            'sgst_rate'            => round($sgstRate, 2),
            'cgst_amount'          => $cgstAmount,
            'sgst_amount'          => $sgstAmount,
            'gst_amount'           => round($cgstAmount + $sgstAmount, 2),
            'total_amount_incl_gst'=> $total,
        ];
    }

    /**
     * Compute the base (pre-GST) amount for a charge type.
     */
    public static function chargeBaseAmount(Charge $charge, float $subTotalBase, float $subTotalInclusive): float
    {
        $type = $charge->charge_type;

        if ($type === 'fixed') {
            return (float) $charge->charge_value;
        }

        if ($type === 'percentage') {
            return round($subTotalInclusive * ((float) $charge->charge_value) / 100, 2);
        }

        return 0.0;
    }

    /**
     * Whether a charge should be waived because the cart exceeds the
     * configured free-delivery threshold.
     */
    public static function isChargeWaived(Charge $charge, float $subTotalInclusive): bool
    {
        $min = (float) ($charge->minimum_cart_value ?? 0);

        return $min > 0 && $subTotalInclusive >= $min;
    }

    /**
     * Aggregate totals for a set of already-priced lines + charges.
     *
     * @param  array<int, array>  $pricedLines  output of PricingService::itemPricing()
     * @param  array<int, array>  $pricedCharges output of PricingService::chargePricing() (already filtered/waived)
     * @return array{
     *     subtotal_base: float,
     *     subtotal_inclusive: float,
     *     product_cgst: float,
     *     product_sgst: float,
     *     product_gst: float,
     *     charges_base: float,
     *     charges_gst: float,
     *     charges_total: float,
     *     grand_total: float,
     * }
     */
    public static function totals(array $pricedLines, array $pricedCharges = []): array
    {
        $subtotalBase = 0.0;
        $subtotalInclusive = 0.0;
        $productCgst = 0.0;
        $productSgst = 0.0;
        $productGst = 0.0;

        foreach ($pricedLines as $line) {
            $subtotalBase += $line['line_base'];
            $subtotalInclusive += $line['line_total_incl_gst'];
            $productCgst += $line['line_cgst'];
            $productSgst += $line['line_sgst'];
            $productGst += $line['line_gst'];
        }

        $chargesBase = 0.0;
        $chargesGst = 0.0;
        $chargesTotal = 0.0;

        foreach ($pricedCharges as $charge) {
            $chargesBase += $charge['base_amount'];
            $chargesGst += $charge['gst_amount'];
            $chargesTotal += $charge['total_amount_incl_gst'];
        }

        return [
            'subtotal_base'     => round($subtotalBase, 2),
            'subtotal_inclusive'=> round($subtotalInclusive, 2),
            'product_cgst'      => round($productCgst, 2),
            'product_sgst'      => round($productSgst, 2),
            'product_gst'       => round($productGst, 2),
            'charges_base'      => round($chargesBase, 2),
            'charges_gst'       => round($chargesGst, 2),
            'charges_total'     => round($chargesTotal, 2),
            'grand_total'       => round($subtotalInclusive + $chargesTotal, 2),
        ];
    }
}
