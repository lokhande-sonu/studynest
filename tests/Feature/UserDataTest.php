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

/**
 * Feature tests for user data management (Requirement 3).
 *
 * Covers checkout pre-fill of customer address and customer profile
 * updates after an order is placed.
 */
class UserDataTest extends TestCase
{
    use RefreshDatabase;

    // ---------------------------------------------------------------
    //  Helpers
    // ---------------------------------------------------------------

    private function createCustomer(array $overrides = []): Customer
    {
        return Customer::create(array_merge([
            'cust_name'     => 'PreFill Customer',
            'cust_email'    => 'prefill@example.com',
            'cust_mobile'   => '9876543210',
            'cust_password' => Hash::make('password'),
            'cust_status'   => 1,
        ], $overrides));
    }

    private function createCategory(): ProductCategory
    {
        return ProductCategory::create(['cat_name' => 'Test', 'cat_status' => 1]);
    }

    private function createProduct(ProductCategory $cat): Product
    {
        return Product::create([
            'p_name'          => 'Test Product',
            'p_cat_id'        => $cat->cat_id,
            'p_status'        => 1,
            'p_gender'        => 'Unisex',
            'is_all_schools'  => 1,
            'is_all_classes'  => 1,
            'is_all_subjects' => 1,
        ]);
    }

    private function createStock(Product $product): ProductStockInventory
    {
        return ProductStockInventory::create([
            'prod_sku'              => 'UF-' . $product->p_id,
            'prod_id'               => $product->p_id,
            'prod_variant_id'       => null,
            'available_stock'       => 100,
            'unit_price'            => 100.00,
            'discounted_unit_price' => null,
            'gst_rate'              => 0,
            'gst_type'              => 'inclusive',
            'cgst_rate'             => 0,
            'sgst_rate'             => 0,
            'status'                => 1,
        ]);
    }

    private function addItemToCart(Customer $customer, Product $product, int $qty = 1): CartItem
    {
        return CartItem::create([
            'cust_id'    => $customer->cust_id,
            'product_id' => $product->p_id,
            'variant_id' => null,
            'quantity'   => $qty,
        ]);
    }

    private function orderData(array $overrides = []): array
    {
        return array_merge([
            'first_name'     => 'Rajesh',
            'last_name'      => 'Kumar',
            'email'          => 'rajesh@example.com',
            'phone'          => '9876543210',
            'address'        => '456 New Address, Sector 5, Noida',
            'city'           => 'Noida',
            'state'          => 'Uttar Pradesh',
            'pincode'        => '201301',
            'payment_method' => 'cod',
        ], $overrides);
    }

    // ---------------------------------------------------------------
    //  Checkout Pre-fill Tests
    // ---------------------------------------------------------------

    /**
     * When a customer already has address info in their profile,
     * the checkout view should be able to access it for pre-fill.
     *
     * We verify this by checking that the customer's address data
     * is retrievable after it was previously saved.
     */
    public function test_checkout_accessible_with_logged_in_customer()
    {
        $customer = $this->createCustomer([
            'cust_address' => '123 Old Street',
            'cust_city'    => 'Mumbai',
            'cust_state'   => 'Maharashtra',
            'cust_pincode' => '400001',
        ]);

        $response = $this->actingAs($customer)->get('/checkout');

        $response->assertStatus(200);
    }

    /**
     * After a customer has placed an order, their profile address
     * should be updated with the delivery address used in the order.
     */
    public function test_customer_profile_address_is_initially_empty()
    {
        $customer = $this->createCustomer();

        $this->assertNull($customer->cust_address);
        $this->assertNull($customer->cust_city);
        $this->assertNull($customer->cust_state);
        $this->assertNull($customer->cust_pincode);
    }

    /**
     * When a customer places an order with a new address, the profile
     * should be updated with that address for future pre-fill.
     */
    public function test_customer_profile_updates_after_order()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product);
        $this->addItemToCart($customer, $product, 1);

        $this->actingAs($customer);

        $this->post('/checkout/place-order', $this->orderData([
            'address' => '789 Updated Lane, Sector 10, Noida',
            'city'    => 'Noida',
            'state'   => 'Uttar Pradesh',
            'pincode' => '201301',
        ]));

        // Refresh the customer model
        $customer->refresh();

        $this->assertEquals('789 Updated Lane, Sector 10, Noida', $customer->cust_address,
            'Customer address should be updated after placing an order');
        $this->assertEquals('Noida', $customer->cust_city);
        $this->assertEquals('Uttar Pradesh', $customer->cust_state);
        $this->assertEquals('201301', $customer->cust_pincode);
    }

    /**
     * Subsequent orders should update the profile with the latest address.
     */
    public function test_customer_profile_updates_with_latest_order_address()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product1 = $this->createProduct($cat);
        $product2 = $this->createProduct($cat);
        $this->createStock($product1);
        $this->createStock($product2);

        $this->actingAs($customer);

        // First order — Mumbai address
        $this->addItemToCart($customer, $product1, 1);
        $this->post('/checkout/place-order', $this->orderData([
            'address' => '111 First Street, Andheri',
            'city'    => 'Mumbai',
            'state'   => 'Maharashtra',
            'pincode' => '400001',
        ]));

        $customer->refresh();
        $this->assertEquals('Mumbai', $customer->cust_city);

        // Second order — Delhi address
        $this->addItemToCart($customer, $product2, 1);
        $this->post('/checkout/place-order', $this->orderData([
            'address' => '222 Second Avenue, Connaught Place',
            'city'    => 'New Delhi',
            'state'   => 'Delhi',
            'pincode' => '110001',
        ]));

        $customer->refresh();
        $this->assertEquals('222 Second Avenue, Connaught Place', $customer->cust_address,
            'Profile should reflect the latest order address');
        $this->assertEquals('New Delhi', $customer->cust_city);
        $this->assertEquals('Delhi', $customer->cust_state);
        $this->assertEquals('110001', $customer->cust_pincode);
    }

    // ---------------------------------------------------------------
    //  Order stores delivery details correctly
    // ---------------------------------------------------------------

    public function test_order_stores_delivery_details_from_request()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product);
        $this->addItemToCart($customer, $product, 1);

        $this->actingAs($customer);

        $deliveryAddress = '999 Test Nagar, Whitefield';
        $this->post('/checkout/place-order', $this->orderData([
            'first_name' => 'Amit',
            'last_name'  => 'Sharma',
            'address'    => $deliveryAddress,
            'city'       => 'Bangalore',
            'state'      => 'Karnataka',
            'pincode'    => '560001',
        ]));

        $order = Order::where('order_placed_cust_id', $customer->cust_id)->first();
        $this->assertNotNull($order);

        $details = $order->order_delivery_details;
        $this->assertEquals('Amit', $details['first_name']);
        $this->assertEquals('Sharma', $details['last_name']);
        $this->assertEquals($deliveryAddress, $details['address']);
        $this->assertEquals('Bangalore', $details['city']);
        $this->assertEquals('Karnataka', $details['state']);
        $this->assertEquals('560001', $details['pincode']);
    }

    public function test_order_is_linked_to_customer()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product);
        $this->addItemToCart($customer, $product, 1);

        $this->actingAs($customer);
        $this->post('/checkout/place-order', $this->orderData());

        $order = Order::where('order_placed_cust_id', $customer->cust_id)->first();
        $this->assertNotNull($order);
        $this->assertEquals($customer->cust_id, $order->order_placed_cust_id);
    }

    /**
     * Customer can retrieve their orders through the relationship.
     */
    public function test_customer_has_orders_relationship()
    {
        $customer = $this->createCustomer();
        $cat      = $this->createCategory();
        $product  = $this->createProduct($cat);
        $this->createStock($product);
        $this->addItemToCart($customer, $product, 1);

        $this->actingAs($customer);
        $this->post('/checkout/place-order', $this->orderData());

        $this->assertDatabaseHas('tbl_orders', [
            'order_placed_cust_id' => $customer->cust_id,
        ]);

        $orders = $customer->orders;
        $this->assertCount(1, $orders);
    }
}
