<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductInventory;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

class ApiOrderFlowTest extends TestCase
{
    use \Tests\CreatesApplication;

    private Customer $customer;
    private Customer $otherCustomer;
    private ProductInventory $inventory;
    private Product $product;

    private const TOKEN = 'test-api-token';

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();

        $this->customer = $this->makeCustomer();
        $this->otherCustomer = $this->makeCustomer();

        $category = ProductCategory::create([
            'cat_name' => 'ZZZAPI_Cat_' . uniqid(),
            'cat_status' => 1,
            'cat_created_at' => now(),
        ]);

        $this->product = Product::create([
            'p_cat_id' => $category->cat_id,
            'p_name' => 'API Order Product',
            'p_full_desc' => 'desc',
            'p_status' => 1,
            'p_created_at' => now(),
        ]);

        $this->inventory = ProductInventory::create([
            'product_id' => $this->product->p_id,
            'packet_size' => '100',
            'product_unit' => 'gm',
            'product_rate' => 100,
            'p_stock_qty' => 10,
            'p_stock_status' => 1,
            'p_stock_created_at' => now(),
        ]);
    }

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    private function makeCustomer(): Customer
    {
        $token = bin2hex(random_bytes(30));

        return Customer::create([
            'cust_name' => 'API Customer',
            'cust_email' => 'api' . uniqid() . '@example.com',
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
            'cust_api_token' => $token,
            'cust_api_token_validity' => Carbon::now()->addDays(30),
        ]);
    }

    private function headers(Customer $customer): array
    {
        return [
            'Authorization' => self::TOKEN,
            'User-Auth-Token' => $customer->cust_api_token,
        ];
    }

    private function placeOrderPayload(): array
    {
        return [
            'order_items_qty' => 2,
            'order_items' => [[
                'product_id' => $this->product->p_id,
                'packet_size' => '100',
                'product_unit' => 'gm',
                'product_qty' => 2,
            ]],
            'delivery_details' => [
                'first_name' => 'API',
                'last_name' => 'Customer',
                'phone' => $this->customer->cust_mobile,
            ],
            'order_charges' => [],
            'order_total_amt' => 200,
            'order_payment_mode' => 2,
        ];
    }

    public function test_place_order_deducts_stock_once(): void
    {
        $response = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/place', $this->placeOrderPayload());

        $response->assertStatus(200)->assertJson(['status' => true]);

        $this->inventory->refresh();
        $this->assertSame(8, (int) $this->inventory->p_stock_qty);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();
        $this->assertNotNull($order);
        $this->assertSame(OrderStatus::PENDING, (int) $order->order_status);
    }

    public function test_confirm_order_marks_confirmed_without_double_deduction(): void
    {
        $place = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/place', $this->placeOrderPayload());
        $place->assertStatus(200);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();

        $response = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/confirm', [
                'order_id' => $order->order_id,
                'payment_id' => 'PAYTEST123',
            ]);

        $response->assertStatus(200)->assertJson(['status' => true]);
        $order->refresh();
        $this->assertSame(OrderStatus::CONFIRMED, (int) $order->order_status);
        $this->assertSame('PAYTEST123', $order->order_payment_id);
        $this->assertSame(1, (int) $order->order_payment_status);

        // Stock must be deducted exactly once (8, not 6).
        $this->inventory->refresh();
        $this->assertSame(8, (int) $this->inventory->p_stock_qty);
    }

    public function test_cannot_confirm_another_customers_order(): void
    {
        $place = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/place', $this->placeOrderPayload());
        $place->assertStatus(200);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();

        $response = $this->withHeaders($this->headers($this->otherCustomer))
            ->postJson('/api/order/confirm', [
                'order_id' => $order->order_id,
                'payment_id' => 'PAYTEST123',
            ]);

        $response->assertStatus(404);
        $order->refresh();
        $this->assertSame(OrderStatus::PENDING, (int) $order->order_status);
    }

    public function test_customer_can_cancel_own_order_and_stock_is_restored(): void
    {
        $place = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/place', $this->placeOrderPayload());
        $place->assertStatus(200);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();
        $this->inventory->refresh();
        $this->assertSame(8, (int) $this->inventory->p_stock_qty);

        $response = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/update', [
                'order_id' => $order->order_id,
                'order_status' => OrderStatus::CANCELLED,
            ]);

        $response->assertStatus(200)->assertJson(['status' => true]);
        $order->refresh();
        $this->assertSame(OrderStatus::CANCELLED, (int) $order->order_status);

        $this->inventory->refresh();
        $this->assertSame(10, (int) $this->inventory->p_stock_qty);
    }

    public function test_customer_cannot_cancel_another_customers_order(): void
    {
        $place = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/place', $this->placeOrderPayload());
        $place->assertStatus(200);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();

        $response = $this->withHeaders($this->headers($this->otherCustomer))
            ->postJson('/api/order/update', [
                'order_id' => $order->order_id,
                'order_status' => OrderStatus::CANCELLED,
            ]);

        $response->assertStatus(404);
        $order->refresh();
        $this->assertSame(OrderStatus::PENDING, (int) $order->order_status);
    }

    public function test_customer_cannot_set_non_cancel_status(): void
    {
        $place = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/place', $this->placeOrderPayload());
        $place->assertStatus(200);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();

        $response = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/update', [
                'order_id' => $order->order_id,
                'order_status' => OrderStatus::DELIVERED,
            ]);

        $response->assertStatus(422);
        $order->refresh();
        $this->assertSame(OrderStatus::PENDING, (int) $order->order_status);
    }

    public function test_requires_api_token_and_login_token(): void
    {
        $this->getJson('/api/getOrders')->assertStatus(401);
    }

    public function test_customer_orders_are_scoped_to_customer(): void
    {
        $place = $this->withHeaders($this->headers($this->customer))
            ->postJson('/api/order/place', $this->placeOrderPayload());
        $place->assertStatus(200);

        $order = Order::where('order_placed_cust_id', $this->customer->cust_id)->first();

        // The other customer must not see this order in their list or details.
        $this->withHeaders($this->headers($this->otherCustomer))
            ->getJson('/api/getOrders')
            ->assertStatus(200)
            ->assertJsonMissingPath('data.0.order_id', $order->order_id);

        $this->withHeaders($this->headers($this->otherCustomer))
            ->getJson('/api/getOrderDetails/' . $order->order_id)
            ->assertStatus(404);
    }
}
