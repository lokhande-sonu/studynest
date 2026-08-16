<?php

namespace Tests\Feature;

use App\Enums\OrderPaymentMode;
use App\Enums\OrderStatus;
use App\Models\CartItem;
use App\Models\Charge;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStockInventory;
use App\Models\ProductVariant;
use App\Services\PricingService;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PlaceOrderTest extends TestCase
{
    use \Tests\CreatesApplication;

    private Customer $customer;
    private Product $product;
    private ProductVariant $variant;
    private ProductStockInventory $stock;
    private ProductCategory $category;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
        DB::beginTransaction();

        $this->seedCharges();

        $this->category = ProductCategory::create([
            'cat_name' => 'ZZZPO_Cat_' . uniqid(),
            'cat_status' => 1,
            'cat_created_at' => now(),
        ]);

        $this->customer = Customer::create([
            'cust_name' => 'Test Customer',
            'cust_email' => 'po' . uniqid() . '@example.com',
            'cust_mobile' => '9' . substr(str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT), 0, 9),
            'cust_password' => bcrypt('password'),
            'cust_address' => 'Old Address',
            'cust_city' => 'Old City',
            'cust_state' => 'Old State',
            'cust_country' => 'India',
            'cust_pincode' => 110001,
            'cust_gender' => 'Other',
            'cust_status' => 1,
            'cust_created_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function seedCharges(): void
    {
        // Mirror the production charge config: an inactive processing charge
        // and an active fixed delivery charge with free-delivery threshold.
        Charge::create([
            'charge_name' => 'Processing Charge',
            'charge_type' => 'percentage',
            'charge_value' => 1,
            'gst_rate' => 10,
            'gst_type' => 'exclusive',
            'cgst_rate' => 5,
            'sgst_rate' => 5,
            'minimum_cart_value' => 0,
            'charge_status' => 0,
            'charge_created_at' => now(),
        ]);

        Charge::create([
            'charge_name' => 'Delivery Charge',
            'charge_type' => 'fixed',
            'charge_value' => 10,
            'gst_rate' => 18,
            'gst_type' => 'exclusive',
            'cgst_rate' => 9,
            'sgst_rate' => 9,
            'minimum_cart_value' => 2000,
            'charge_status' => 1,
            'charge_created_at' => now(),
        ]);
    }

    private function addProduct(float $price, int $stockQty, string $name): void
    {
        $this->product = Product::create([
            'p_cat_id' => $this->category->cat_id,
            'p_name' => $name,
            'p_tag' => 'tag-' . $name,
            'p_short_desc' => 'short ' . $name,
            'p_full_desc' => 'full ' . $name,
            'p_status' => 1,
            'p_created_at' => now(),
        ]);

        $this->variant = ProductVariant::create([
            'product_id' => $this->product->p_id,
            'prod_variant' => 'Default',
        ]);

        $this->stock = ProductStockInventory::create([
            'prod_sku' => 'SKU_' . uniqid(),
            'prod_id' => $this->product->p_id,
            'prod_variant_id' => $this->variant->prod_variant_id,
            'available_stock' => $stockQty,
            'unit_price' => $price,
            'discounted_unit_price' => $price,
            'gst_rate' => 18,
            'gst_type' => 'inclusive',
            'cgst_rate' => 9,
            'sgst_rate' => 9,
            'status' => 1,
        ]);
    }

    private function addToCart(int $qty): void
    {
        CartItem::create([
            'cust_id' => $this->customer->cust_id,
            'product_id' => $this->product->p_id,
            'variant_id' => $this->variant->prod_variant_id,
            'quantity' => $qty,
        ]);
    }

    private function payload(): array
    {
        return [
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => $this->customer->cust_email,
            'phone' => $this->customer->cust_mobile,
            'address' => '123 Test Street, Test Nagar, New Delhi',
            'city' => 'New Delhi',
            'state' => 'Delhi',
            'pincode' => '110001',
            'gst_number' => '',
            'payment_method' => 'cod',
        ];
    }

    private function expectedChargeTotals(float $subtotalInclusive, float $subtotalBase): array
    {
        $totals = [];
        foreach (Charge::where('charge_status', 1)->get() as $charge) {
            $waived = PricingService::isChargeWaived($charge, $subtotalInclusive);
            $base = $waived ? 0.0 : PricingService::chargeBaseAmount($charge, $subtotalBase, $subtotalInclusive);
            $totals[] = [
                'waived' => $waived,
                'total' => PricingService::chargePricing($base, $charge->gst_rate, $charge->gst_type, $charge->cgst_rate, $charge->sgst_rate),
            ];
        }
        return $totals;
    }

    public function test_cod_order_persists_customer_and_calculates_server_side_totals(): void
    {
        $this->addProduct(100, 5, 'PlaceOrder Product');
        $this->addToCart(2);

        $response = $this->actingAs($this->customer)->post('/checkout/place-order', $this->payload());

        $response->assertRedirect();

        // ---- Server-side totals ----
        $lines = PricingService::itemPricing(100, 18, 'inclusive', 2); // 200.00 inclusive
        $this->assertSame(200.00, $lines['line_total_incl_gst']);

        $chargeTotals = $this->expectedChargeTotals(200.00, $lines['line_base']);
        $expectedChargesTotal = round(collect($chargeTotals)->sum(fn ($c) => $c['total']['total_amount_incl_gst']), 2);
        $expectedGrandTotal = round(200.00 + $expectedChargesTotal, 2);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();
        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::CONFIRMED, (int) $order->order_status);
        $this->assertSame(OrderPaymentMode::COD, (int) $order->order_payment_mode);
        $this->assertSame(2, (int) $order->order_items_qty);
        $this->assertSame($expectedGrandTotal, (float) $order->order_total_amt);

        // Charges JSON: delivery charge applied with GST-inclusive amount
        $charges = collect($order->order_charges);
        $delivery = $charges->firstWhere('charge_id', Charge::where('charge_status', 1)->first()->charge_id);
        $this->assertNotFalse($delivery);
        $this->assertTrue($delivery['applied']);
        $this->assertSame($chargeTotals[0]['total']['total_amount_incl_gst'], (float) $delivery['calculated_amount']);

        // Product GST + charge GST == order_gst_amount
        $chargeGst = collect($chargeTotals)->sum(fn ($c) => $c['total']['gst_amount']);
        $this->assertSame(round($lines['line_gst'] + $chargeGst, 2), (float) $order->order_gst_amount);

        // ---- Customer data persisted ----
        $this->customer->refresh();
        $this->assertSame('Test Customer', $this->customer->cust_name);
        $this->assertSame($this->payload()['phone'], $this->customer->cust_mobile);
        $this->assertStringContainsString($this->payload()['address'], $this->customer->cust_address);
        $this->assertSame('New Delhi', $this->customer->cust_city);
        $this->assertSame('Delhi', $this->customer->cust_state);
        $this->assertSame('India', $this->customer->cust_country);
        $this->assertSame(110001, (int) $this->customer->cust_pincode);

        // ---- Stock decremented ----
        $this->stock->refresh();
        $this->assertSame(3, (int) $this->stock->available_stock);

        // ---- order_items table written ----
        $orderItem = OrderItem::where('order_id', $order->order_id)->first();
        $this->assertNotNull($orderItem);
        $this->assertSame(2, (int) $orderItem->quantity);
        $this->assertSame(100.00, (float) $orderItem->price);
        $this->assertSame(200.00, (float) $orderItem->total_price);

        // ---- Cart cleared ----
        $this->assertSame(0, CartItem::where('cust_id', $this->customer->cust_id)->count());
    }

    public function test_delivery_charge_waived_above_free_delivery_threshold(): void
    {
        $this->addProduct(1200, 5, 'PlaceOrder Big Product');
        $this->addToCart(2); // subtotal 2400 (incl GST) >= 2000

        $response = $this->actingAs($this->customer)->post('/checkout/place-order', $this->payload());
        $response->assertRedirect();

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();
        $this->assertNotNull($order);
        $this->assertSame(2400.00, (float) $order->order_total_amt);

        $charges = collect($order->order_charges);
        $delivery = $charges->firstWhere('charge_type', '!=', 'tax');
        $this->assertNotFalse($delivery);
        $this->assertFalse($delivery['applied']);
        $this->assertSame(0.00, (float) $delivery['calculated_amount']);
    }

    public function test_insufficient_stock_blocks_order_and_keeps_cart(): void
    {
        $this->addProduct(100, 1, 'PlaceOrder Scarce Product');
        $this->addToCart(2);

        $response = $this->actingAs($this->customer)->post('/checkout/place-order', $this->payload());

        $response->assertSessionHas('error');

        $this->assertSame(0, Order::where('order_placed_cust_id', $this->customer->cust_id)->count());
        $this->assertSame(1, CartItem::where('cust_id', $this->customer->cust_id)->count());
        $this->stock->refresh();
        $this->assertSame(1, (int) $this->stock->available_stock);
    }

    public function test_empty_cart_blocks_order(): void
    {
        $this->addProduct(100, 5, 'PlaceOrder No Cart Product');

        $response = $this->actingAs($this->customer)->post('/checkout/place-order', $this->payload());

        $response->assertSessionHas('error');
        $this->assertSame(0, Order::where('order_placed_cust_id', $this->customer->cust_id)->count());
    }

    public function test_unauthenticated_user_cannot_place_order(): void
    {
        $this->addProduct(100, 5, 'PlaceOrder Guest Product');
        $this->addToCart(1);

        $before = Order::count();
        $response = $this->post('/checkout/place-order', $this->payload());

        $response->assertRedirect();
        $this->assertSame($before, Order::count());
    }
}
