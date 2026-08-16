<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Management;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductStockInventory;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class OrderStatusTest extends TestCase
{
    use \Tests\CreatesApplication;

    private Customer $customer;
    private Customer $otherCustomer;
    private Management $admin;
    private Product $product;
    private ProductVariant $variant;
    private ProductStockInventory $stock;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake();
        DB::beginTransaction();

        $this->customer = $this->makeCustomer();
        $this->otherCustomer = $this->makeCustomer();
        $this->admin = Management::create([
            'name' => 'Test Admin',
            'email' => 'admin' . uniqid() . '@example.com',
            'username' => 'admin' . uniqid(),
            'mobile' => '9000000001',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 1,
        ]);

        $category = ProductCategory::create([
            'cat_name' => 'ZZZOS_Cat_' . uniqid(),
            'cat_status' => 1,
            'cat_created_at' => now(),
        ]);

        $this->product = Product::create([
            'p_cat_id' => $category->cat_id,
            'p_name' => 'OrderStatus Product',
            'p_full_desc' => 'desc',
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
            'available_stock' => 10,
            'unit_price' => 100,
            'discounted_unit_price' => 100,
            'gst_rate' => 18,
            'gst_type' => 'inclusive',
            'cgst_rate' => 9,
            'sgst_rate' => 9,
            'status' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function makeCustomer(): Customer
    {
        return Customer::create([
            'cust_name' => 'Order Customer',
            'cust_email' => 'os' . uniqid() . '@example.com',
            'cust_mobile' => '9' . substr(str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT), 0, 9),
            'cust_password' => bcrypt('password'),
            'cust_address' => '123 Address Street',
            'cust_city' => 'City',
            'cust_state' => 'State',
            'cust_country' => 'India',
            'cust_pincode' => 110001,
            'cust_gender' => 'Other',
            'cust_status' => 1,
            'cust_created_at' => now(),
        ]);
    }

    private function makeOrder(Customer $customer, int $status): Order
    {
        $order = new Order();
        $order->order_placed_cust_id = $customer->cust_id;
        $order->order_items_qty = 2;
        $order->order_items = [[
            'product_id' => $this->product->p_id,
            'variant_id' => $this->variant->prod_variant_id,
            'product_qty' => 2,
        ]];
        $order->order_delivery_details = [
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'phone' => $customer->cust_mobile,
            'email' => $customer->cust_email,
        ];
        $order->order_charges = [];
        $order->order_total_amt = 211.80;
        $order->order_paid_amt = 0;
        $order->order_due_amt = 211.80;
        $order->order_payment_mode = 1;
        $order->order_date_time = now();
        $order->order_payment_status = 2;
        $order->order_status = $status;
        $order->order_created_at = now();
        $order->order_updated_at = now();
        $order->save();

        return $order;
    }

    public function test_customer_can_cancel_own_confirmed_order_and_stock_is_restored(): void
    {
        $order = $this->makeOrder($this->customer, OrderStatus::CONFIRMED);

        $response = $this->actingAs($this->customer)
            ->post('/my-orders/' . $order->order_id . '/cancel');

        $response->assertSessionHas('success');
        $order->refresh();
        $this->assertSame(OrderStatus::CANCELLED, (int) $order->order_status);
        $this->stock->refresh();
        $this->assertSame(12, (int) $this->stock->available_stock);
    }

    public function test_customer_cannot_cancel_delivered_order(): void
    {
        $order = $this->makeOrder($this->customer, OrderStatus::DELIVERED);

        $response = $this->actingAs($this->customer)
            ->post('/my-orders/' . $order->order_id . '/cancel');

        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertSame(OrderStatus::DELIVERED, (int) $order->order_status);
        $this->stock->refresh();
        $this->assertSame(10, (int) $this->stock->available_stock);
    }

    public function test_customer_cannot_cancel_someone_elses_order(): void
    {
        $order = $this->makeOrder($this->otherCustomer, OrderStatus::CONFIRMED);

        $response = $this->actingAs($this->customer)
            ->post('/my-orders/' . $order->order_id . '/cancel');

        $response->assertStatus(404);
        $order->refresh();
        $this->assertSame(OrderStatus::CONFIRMED, (int) $order->order_status);
    }

    public function test_management_can_confirm_pending_order(): void
    {
        $order = $this->makeOrder($this->customer, OrderStatus::PENDING);

        $response = $this->actingAs($this->admin, 'management')
            ->put('/management/order/' . $order->order_id . '/update-status', ['status' => OrderStatus::CONFIRMED]);

        $response->assertSessionHas('success');
        $order->refresh();
        $this->assertSame(OrderStatus::CONFIRMED, (int) $order->order_status);
    }

    public function test_management_cannot_skip_from_pending_to_delivered(): void
    {
        $order = $this->makeOrder($this->customer, OrderStatus::PENDING);

        $response = $this->actingAs($this->admin, 'management')
            ->put('/management/order/' . $order->order_id . '/update-status', ['status' => OrderStatus::DELIVERED]);

        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertSame(OrderStatus::PENDING, (int) $order->order_status);
    }

    public function test_management_cannot_reopen_cancelled_order(): void
    {
        $order = $this->makeOrder($this->customer, OrderStatus::CANCELLED);

        $response = $this->actingAs($this->admin, 'management')
            ->put('/management/order/' . $order->order_id . '/update-status', ['status' => OrderStatus::CONFIRMED]);

        $response->assertSessionHas('error');
        $order->refresh();
        $this->assertSame(OrderStatus::CANCELLED, (int) $order->order_status);
    }

    public function test_management_cancel_restores_stock(): void
    {
        $order = $this->makeOrder($this->customer, OrderStatus::CONFIRMED);
        $this->stock->decrement('available_stock', 2);
        $this->stock->refresh();
        $this->assertSame(8, (int) $this->stock->available_stock);

        $response = $this->actingAs($this->admin, 'management')
            ->put('/management/order/' . $order->order_id . '/update-status', ['status' => OrderStatus::CANCELLED]);

        $response->assertSessionHas('success');
        $order->refresh();
        $this->assertSame(OrderStatus::CANCELLED, (int) $order->order_status);
        $this->stock->refresh();
        $this->assertSame(10, (int) $this->stock->available_stock);
    }
}
