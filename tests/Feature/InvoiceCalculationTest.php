<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStockInventory;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Charge;

/**
 * Feature tests for invoice calculation (Requirement 2).
 *
 * Covers GST inclusive/exclusive calculations, price sourcing from
 * ProductStockInventory, and delivery charge computation.
 */
class InvoiceCalculationTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    //  Helpers
    // ---------------------------------------------------------------

    private function createCustomer(): Customer
    {
        return Customer::create([
            'cust_name'     => 'Test Customer',
            'cust_email'    => 'test@example.com',
            'cust_mobile'   => '9876543210',
            'cust_password' => Hash::make('password'),
            'cust_status'   => 1,
        ]);
    }

    private function createCategory(): ProductCategory
    {
        return ProductCategory::create([
            'cat_name'   => 'Test Category',
            'cat_status' => 1,
        ]);
    }

    private function createProduct(ProductCategory $cat): Product
    {
        return Product::create([
            'p_name'         => 'Test Product',
            'p_cat_id'       => $cat->cat_id,
            'p_status'       => 1,
            'p_gender'       => 'Unisex',
            'is_all_schools' => 1,
            'is_all_classes' => 1,
            'is_all_subjects'=> 1,
        ]);
    }

    private function createStock(Product $product, array $overrides = []): ProductStockInventory
    {
        return ProductStockInventory::create(array_merge([
            'prod_sku'              => 'GST-SKU-' . $product->p_id,
            'prod_id'               => $product->p_id,
            'prod_variant_id'       => null,
            'available_stock'       => 100,
            'unit_price'            => 100.00,
            'discounted_unit_price' => null,
            'gst_rate'              => 18.00,
            'gst_type'              => 'inclusive',
            'cgst_rate'             => 9.00,
            'sgst_rate'             => 9.00,
            'status'                => 1,
        ], $overrides));
    }

    private function addItemToCart(Customer $customer, Product $product, int $quantity = 1): CartItem
    {
        return CartItem::create([
            'cust_id'     => $customer->cust_id,
            'product_id'  => $product->p_id,
            'variant_id'  => null,
            'quantity'    => $quantity,
        ]);
    }

    private function validOrderData(): array
    {
        return [
            'first_name'     => 'Test',
            'last_name'      => 'Customer',
            'email'          => 'test@example.com',
            'phone'          => '9876543210',
            'address'        => '123 Test Street, Test Area',
            'city'           => 'Mumbai',
            'state'          => 'Maharashtra',
            'pincode'        => '400001',
            'payment_method' => 'cod',
        ];
    }

    /**
     * Place a COD order for the given customer and return the created Order.
     */
    private function placeCodOrder(Customer $customer, array $extraData = []): Order
    {
        $this->actingAs($customer);

        $response = $this->post('/checkout/place-order', array_merge(
            $this->validOrderData(),
            $extraData
        ));

        // For COD, the controller redirects to thank-you page
        $response->assertRedirect();

        $order = Order::where('order_placed_cust_id', $customer->cust_id)->first();
        $this->assertNotNull($order, 'Order should have been created');

        return $order;
    }

    // ---------------------------------------------------------------
    //  GST Inclusive Calculation Tests
    // ---------------------------------------------------------------

    /**
     * When gst_type is 'inclusive', the displayed unit_price already
     * includes GST.  The base price per unit should be:
     *   base = unit_price / (1 + gst_rate / 100)
     *
     * Example: ₹200 unit_price, 18% inclusive GST
     *   base = 200 / 1.18 ≈ 169.49
     *   GST  = 200 - 169.49 ≈ 30.51
     */
    public function test_gst_inclusive_per_unit_calculation()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 200.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'inclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        $order = $this->placeCodOrder($customer);

        // Parse the stored order items
        $items = $order->order_items;
        $this->assertNotEmpty($items);

        $item = $items[0];

        // The item product_rate should be the base price (price without GST)
        $expectedBase = round(200.00 / (1 + 18 / 100), 2); // ≈ 169.49
        $expectedGST  = round(200.00 - $expectedBase, 2);   // ≈ 30.51

        $this->assertEquals($expectedBase, $item['product_rate'],
            'product_rate should be the base price (before GST) for inclusive type');
        $this->assertEqualsWithDelta($expectedGST, $item['gst_amount'] / $item['product_qty'], 0.02,
            'GST per unit should equal displayPrice - basePrice');
    }

    /**
     * Verify that for a single-unit order the inclusive GST is NOT
     * double-counted: base + GST = displayPrice.
     */
    public function test_gst_inclusive_not_double_counted_single_unit()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 118.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'inclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        $order = $this->placeCodOrder($customer);
        $item  = $order->order_items[0];

        // base + GST should equal the display price of ₹118
        $basePlusGST = $item['product_rate'] + ($item['gst_amount'] / $item['product_qty']);
        $this->assertEqualsWithDelta(118.00, $basePlusGST, 0.02,
            'Inclusive: base + GST per unit must equal the display price');
    }

    /**
     * Verify that for a multi-unit order the inclusive GST is NOT
     * double-counted when totalled across quantity.
     */
    public function test_gst_inclusive_not_double_counted_multi_unit()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 118.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'inclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 3);

        $order = $this->placeCodOrder($customer);
        $item  = $order->order_items[0];

        // The 'price' field stores base * qty
        // The 'gst_amount' stores total GST for the line
        $lineBase = $item['product_rate'] * $item['product_qty'];
        $lineGST  = $item['gst_amount'];
        $lineTotal = 118.00 * 3; // 354.00

        $this->assertEqualsWithDelta($lineTotal, $lineBase + $lineGST, 0.02,
            'Inclusive: total line cost = base*qty + total GST (no double counting)');

        // Also verify the order's total amount
        $this->assertEqualsWithDelta($lineTotal, $order->order_total_amt, 0.02,
            'Order total for a single inclusive-GST product with no charges should equal displayPrice * qty');
    }

    // ---------------------------------------------------------------
    //  GST Exclusive Calculation Tests
    // ---------------------------------------------------------------

    /**
     * When gst_type is 'exclusive', GST is added on top of the base price.
     *   CGST = base * cgst_rate / 100
     *   SGST = base * sgst_rate / 100
     *   displayPrice = base + CGST + SGST
     */
    public function test_gst_exclusive_calculation()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 100.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'exclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 2);

        $order = $this->placeCodOrder($customer);
        $item  = $order->order_items[0];

        // START: imohitmehto | 2026-08-25 | FIX: Test assertion corrected for exclusive GST
        // For exclusive, the stored product_rate is the base price (before GST)
        // product_rate stores base price (100), not display price (118)
        // base = 100, displayPrice = 100 + 9 + 9 = 118
        $expectedBasePrice     = 100.00;
        $expectedLineTotal    = 118.00 * 2; // 236.00 (displayPrice * qty)

        $this->assertEquals($expectedBasePrice, $item['product_rate'],
            'For exclusive, product_rate should be the base price without GST');
        // END: imohitmehto | FIX: GST exclusive test assertion

        // GST amounts
        $expectedCGST = 9.00 * 2;  // per line
        $expectedSGST = 9.00 * 2;
        $this->assertEqualsWithDelta($expectedCGST, $item['cgst_amount'], 0.02);
        $this->assertEqualsWithDelta($expectedSGST, $item['sgst_amount'], 0.02);
    }

    // ---------------------------------------------------------------
    //  Price Source from ProductStockInventory
    // ---------------------------------------------------------------

    /**
     * The checkout price must come from ProductStockInventory, not from
     * the product table's legacy price columns.
     *
     * We set the stock inventory price different from what a legacy price
     * might be, and verify the order uses the stock inventory price.
     */
    public function test_checkout_uses_stock_inventory_price()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'            => 799.00,
            'discounted_unit_price' => 699.00,
            'gst_rate'              => 18.00,
            'gst_type'              => 'inclusive',
            'cgst_rate'             => 9.00,
            'sgst_rate'             => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        $order = $this->placeCodOrder($customer);
        $item  = $order->order_items[0];

        // The price source should be discounted_unit_price (699), not unit_price (799)
        // For inclusive GST: base = 699 / 1.18 ≈ 592.37
        $expectedBase = round(699.00 / 1.18, 2);

        $this->assertEqualsWithDelta($expectedBase, $item['product_rate'], 0.02,
            'Price should be derived from discounted_unit_price of ProductStockInventory');
    }

    /**
     * When discounted_unit_price is NULL, the fallback should be unit_price.
     */
    public function test_checkout_falls_back_to_unit_price_when_no_discount()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'            => 250.00,
            'discounted_unit_price' => null,
            'gst_rate'              => 18.00,
            'gst_type'              => 'inclusive',
            'cgst_rate'             => 9.00,
            'sgst_rate'             => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        $order = $this->placeCodOrder($customer);
        $item  = $order->order_items[0];

        $expectedBase = round(250.00 / 1.18, 2);
        $this->assertEqualsWithDelta($expectedBase, $item['product_rate'], 0.02,
            'When discounted_unit_price is null, unit_price should be used');
    }

    // ---------------------------------------------------------------
    //  Delivery Charges from Active Charges
    // ---------------------------------------------------------------

    /**
     * Only active charges (charge_status = 1) should be included.
     */
    public function test_only_active_charges_are_included()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 100.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'exclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        // Active charge: ₹50 fixed delivery
        Charge::create([
            'charge_name'   => 'Delivery',
            'charge_type'   => 'fixed',
            'charge_value'  => 50.00,
            'charge_status' => 1,
        ]);

        // Inactive charge: should NOT appear
        Charge::create([
            'charge_name'   => 'Packaging',
            'charge_type'   => 'fixed',
            'charge_value'  => 25.00,
            'charge_status' => 0, // Inactive
        ]);

        $order = $this->placeCodOrder($customer);

        // For exclusive 18% on ₹100:
        //   displayPrice = 118, CGST = 9, SGST = 9
        //   subTotal = 100
        //   charges = 50 (only the active one)
        //   grandTotal = 100 + 9 + 9 + 50 = 168
        $this->assertEqualsWithDelta(168.00, $order->order_total_amt, 0.02,
            'Only active charges should be included in total');

        // Verify the charges JSON
        $charges = $order->order_charges;
        $chargeNames = collect($charges)->pluck('charge_name')->toArray();
        $this->assertContains('Delivery', $chargeNames);
        $this->assertNotContains('Packaging', $chargeNames);
    }

    /**
     * Fixed charges should add their value directly.
     */
    public function test_fixed_charge_value_is_added_directly()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 200.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'inclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        Charge::create([
            'charge_name'   => 'Shipping',
            'charge_type'   => 'fixed',
            'charge_value'  => 100.00,
            'charge_status' => 1,
        ]);

        $order = $this->placeCodOrder($customer);

        // Inclusive: displayPrice = 200, qty = 1, total = 200
        // Charges: 100
        // grandTotal = 200 + 100 = 300
        $this->assertEqualsWithDelta(300.00, $order->order_total_amt, 0.02);
    }

    /**
     * Percentage charges should be calculated from the subTotal.
     */
    public function test_percentage_charge_calculated_from_subtotal()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 200.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'exclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 2);

        // 10% handling charge
        Charge::create([
            'charge_name'   => 'Handling',
            'charge_type'   => 'percentage',
            'charge_value'  => 10.00, // 10%
            'charge_status' => 1,
        ]);

        $order = $this->placeCodOrder($customer);

        // Exclusive on ₹200 base, qty 2:
        //   subTotal (base * qty) = 200 * 2 = 400
        //   CGST = 18 * 2 = 36
        //   SGST = 18 * 2 = 36
        //   Handling = 10% of 400 = 40
        //   grandTotal = 400 + 36 + 36 + 40 = 512
        $this->assertEqualsWithDelta(512.00, $order->order_total_amt, 0.02);
    }

    /**
     * Multiple active charges should all be summed.
     */
    public function test_multiple_active_charges_are_all_included()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 500.00,
            'gst_rate'    => 0.00, // No GST for simplicity
            'gst_type'    => 'inclusive',
            'cgst_rate'   => 0.00,
            'sgst_rate'   => 0.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        Charge::create(['charge_name' => 'Delivery',   'charge_type' => 'fixed',     'charge_value' => 50.00,  'charge_status' => 1]);
        Charge::create(['charge_name' => 'Packaging',  'charge_type' => 'fixed',     'charge_value' => 20.00,  'charge_status' => 1]);
        Charge::create(['charge_name' => 'Insurance',  'charge_type' => 'percentage','charge_value' => 2.00,   'charge_status' => 1]);

        $order = $this->placeCodOrder($customer);

        // subTotal = 500, charges: 50 + 20 + (2% of 500 = 10) = 80
        // grandTotal = 500 + 80 = 580
        $this->assertEqualsWithDelta(580.00, $order->order_total_amt, 0.02);
    }

    // ---------------------------------------------------------------
    //  Order data integrity
    // ---------------------------------------------------------------

    public function test_order_stores_gst_amount_correctly()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 118.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'inclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 2);

        $order = $this->placeCodOrder($customer);

        // For inclusive: GST per unit = 118 - (118/1.18) = 18
        // Total GST = 18 * 2 = 36
        $this->assertEqualsWithDelta(36.00, $order->order_gst_amount, 0.02,
            'order_gst_amount should store total GST across all items');
    }

    public function test_order_charges_json_contains_gst_line_items()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, [
            'unit_price'  => 100.00,
            'gst_rate'    => 18.00,
            'gst_type'    => 'exclusive',
            'cgst_rate'   => 9.00,
            'sgst_rate'   => 9.00,
        ]);
        $this->addItemToCart($customer, $product, 1);

        $order = $this->placeCodOrder($customer);
        $charges = $order->order_charges;

        $chargeTypes = collect($charges)->pluck('charge_type')->toArray();
        $this->assertContains('tax', $chargeTypes,
            'Charges JSON should contain CGST and SGST entries with type "tax"');
    }

    public function test_order_status_is_set_to_confirmed_for_cod()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product, ['unit_price' => 100.00, 'gst_rate' => 0, 'gst_type' => 'inclusive']);
        $this->addItemToCart($customer, $product, 1);

        $order = $this->placeCodOrder($customer);

        $this->assertEquals(\App\Enums\OrderStatus::CONFIRMED, $order->order_status,
            'COD orders should be set to CONFIRMED immediately');
    }
}
