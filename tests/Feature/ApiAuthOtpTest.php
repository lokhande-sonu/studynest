<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\DB;

class ApiAuthOtpTest extends TestCase
{
    use \Tests\CreatesApplication;

    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        DB::beginTransaction();

        $this->customer = Customer::create([
            'cust_name' => 'OTP Customer',
            'cust_email' => 'otp' . uniqid() . '@example.com',
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

    protected function tearDown(): void
    {
        DB::rollBack();
        parent::tearDown();
    }

    public function test_validate_user_returns_otp_and_user_exists_flag(): void
    {
        $response = $this->postJson('/api/validateUser', [
            'mobile' => $this->customer->cust_mobile,
        ], ['Authorization' => 'test-api-token']);

        $response->assertStatus(200)
            ->assertJson(['status' => true, 'data' => ['user_exists' => 1]]);
        $this->assertMatchesRegularExpression('/^\d{6}$/', (string) $response->json('data.otp'));
    }

    public function test_login_with_correct_otp_succeeds(): void
    {
        $otp = $this->requestOtp($this->customer->cust_mobile);

        $response = $this->postJson('/api/userLogin', [
            'mobile' => $this->customer->cust_mobile,
            'otp_check' => $otp,
        ], ['Authorization' => 'test-api-token']);

        $response->assertStatus(200)->assertJson(['status' => true]);
        $response->assertJsonStructure(['data' => ['token']]);

        $this->customer->refresh();
        $this->assertNotNull($this->customer->cust_api_token);
    }

    public function test_otp_cannot_be_replayed(): void
    {
        $otp = $this->requestOtp($this->customer->cust_mobile);

        $first = $this->postJson('/api/userLogin', [
            'mobile' => $this->customer->cust_mobile,
            'otp_check' => $otp,
        ], ['Authorization' => 'test-api-token']);
        $first->assertStatus(200)->assertJson(['status' => true]);

        $second = $this->postJson('/api/userLogin', [
            'mobile' => $this->customer->cust_mobile,
            'otp_check' => $otp,
        ], ['Authorization' => 'test-api-token']);
        $second->assertJson(['status' => false, 'message' => 'OTP expired. Please request a new one.']);
    }

    public function test_login_with_wrong_otp_fails(): void
    {
        $this->requestOtp($this->customer->cust_mobile);

        $response = $this->postJson('/api/userLogin', [
            'mobile' => $this->customer->cust_mobile,
            'otp_check' => 999999,
        ], ['Authorization' => 'test-api-token']);

        $response->assertStatus(200)->assertJson(['status' => false, 'message' => 'OTP verification failed']);
    }

    public function test_login_with_unknown_mobile_fails(): void
    {
        $response = $this->postJson('/api/userLogin', [
            'mobile' => '9876543210',
            'otp_check' => 111111,
        ], ['Authorization' => 'test-api-token']);

        $response->assertStatus(200)->assertJson(['status' => false, 'message' => 'User not found']);
    }

    private function requestOtp(string $mobile): string
    {
        $response = $this->postJson('/api/validateUser', [
            'mobile' => $mobile,
        ], ['Authorization' => 'test-api-token']);

        $response->assertStatus(200);
        $otp = $response->json('data.otp');
        $this->assertIsString($otp);

        return $otp;
    }

    public function test_register_creates_customer_with_india_defaults(): void
    {
        $response = $this->postJson('/api/userRegister', [
            'name' => 'New Register',
            'mobile' => '9' . substr(str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT), 0, 9),
            'email' => 'reg' . uniqid() . '@example.com',
            'address' => '123 New Street',
            'city' => 'New City',
            'state' => 'New State',
            'gender' => 'Male',
            'pincode' => '560001',
        ], ['Authorization' => 'test-api-token']);

        $response->assertStatus(200)->assertJson(['status' => true]);

        $mobile = $response->json('data.cust_mobile');
        $customer = Customer::where('cust_mobile', $mobile)->first();
        $this->assertNotNull($customer);
        $this->assertSame('India', $customer->cust_country);
        $this->assertSame(1, (int) $customer->cust_status);
        $this->assertNotNull($customer->cust_api_token);
    }

    public function test_register_requires_validation_fields(): void
    {
        $response = $this->postJson('/api/userRegister', [
            'name' => '',
            'mobile' => '123',
        ], ['Authorization' => 'test-api-token']);

        $response->assertStatus(422)->assertJson(['status' => false]);
    }
}
