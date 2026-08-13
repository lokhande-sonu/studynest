<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CashfreeHelper
{
    private $appId;
    private $secretKey;
    private $mode;
    private $baseUrl;
    private $apiVersion;

    public function __construct()
    {
        $this->appId = config('services.cashfree.app_id');
        $this->secretKey = config('services.cashfree.secret_key');
        $this->mode = config('services.cashfree.mode');
        $this->apiVersion = config('services.cashfree.api_version');
        $this->baseUrl = $this->mode === 'production' 
            ? 'https://api.cashfree.com/pg' 
            : 'https://sandbox.cashfree.com/pg';
    }

    /**
     * Create a new order in Cashfree
     */
    public function createOrder($orderData)
    {
        try {
            if (!$this->appId || !$this->secretKey) {
                Log::error('Cashfree credentials missing in config');
                return [
                    'status' => false,
                    'message' => 'Cashfree credentials missing. Please check your .env file.',
                ];
            }

            $response = Http::withHeaders([
                'x-client-id' => $this->appId,
                'x-client-secret' => $this->secretKey,
                'x-api-version' => $this->apiVersion,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/orders', $orderData);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Cashfree Create Order Error', [
                'response' => $response->json(),
                'payload' => $orderData,
                'status_code' => $response->status()
            ]);

            return [
                'status' => false,
                'message' => $response->json()['message'] ?? 'Failed to create Cashfree order',
            ];

        } catch (\Exception $e) {
            Log::error('Cashfree Create Order Exception', [
                'error' => $e->getMessage()
            ]);
            return [
                'status' => false,
                'message' => 'Something went wrong while connecting to payment gateway.',
            ];
        }
    }

    /**
     * Get order details from Cashfree
     */
    public function getOrder($cfOrderId)
    {
        try {
            $response = Http::withHeaders([
                'x-client-id' => $this->appId,
                'x-client-secret' => $this->secretKey,
                'x-api-version' => $this->apiVersion,
            ])->get($this->baseUrl . '/orders/' . $cfOrderId);

            if ($response->successful()) {
                return [
                    'status' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'status' => false,
                'message' => $response->json()['message'] ?? 'Failed to fetch Cashfree order',
            ];

        } catch (\Exception $e) {
            Log::error('Cashfree Get Order Exception', [
                'error' => $e->getMessage()
            ]);
            return [
                'status' => false,
                'message' => 'Something went wrong while connecting to payment gateway.',
            ];
        }
    }

    /**
     * Verify payment status
     */
    public function verifyPayment($cfOrderId)
    {
        try {
            if (!$this->appId || !$this->secretKey) {
                Log::error('Cashfree credentials missing in config');
                return [
                    'status' => false,
                    'message' => 'Cashfree credentials missing. Please check your .env file.',
                ];
            }

            $response = Http::withHeaders([
                'x-client-id' => $this->appId,
                'x-client-secret' => $this->secretKey,
                'x-api-version' => $this->apiVersion,
            ])->get($this->baseUrl . '/orders/' . $cfOrderId . '/payments');

            if ($response->successful()) {
                return [
                    'status' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Cashfree Verify Payment Error', [
                'cf_order_id' => $cfOrderId,
                'response' => $response->json(),
                'status_code' => $response->status()
            ]);

            return [
                'status' => false,
                'message' => $response->json()['message'] ?? 'Failed to verify payment',
            ];

        } catch (\Exception $e) {
            Log::error('Cashfree Verify Payment Exception', [
                'error' => $e->getMessage()
            ]);
            return [
                'status' => false,
                'message' => 'Something went wrong while connecting to payment gateway.',
            ];
        }
    }
}
