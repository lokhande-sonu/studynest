<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class PayUHelper
{
    private $merchantKey;
    private $merchantSalt;
    private $mode;
    private $paymentUrl;

    public function __construct()
    {
        $this->merchantKey = config('services.payu.merchant_key');
        $this->merchantSalt = config('services.payu.merchant_salt');
        $this->mode = config('services.payu.mode');
        
        $this->paymentUrl = ($this->mode === 'production') 
            ? 'https://secure.payu.in/_payment' 
            : 'https://test.payu.in/_payment';
    }

    /**
     * Generate hash for PayU request
     * Formula: sha512(key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5||||||SALT)
     */
    public function generateHash($params)
    {
        $hashData = [
            $this->merchantKey,
            $params['txnid'],
            $params['amount'],
            $params['productinfo'],
            $params['firstname'],
            $params['email'],
            $params['udf1'] ?? '',
            $params['udf2'] ?? '',
            $params['udf3'] ?? '',
            $params['udf4'] ?? '',
            $params['udf5'] ?? '',
            '', '', '', '', '', // udf6 to udf10
            $this->merchantSalt
        ];

        $hashString = implode('|', $hashData);
        return strtolower(hash('sha512', $hashString));
    }

    /**
     * Verify PayU response hash
     * Formula: sha512(SALT|status||||||udf5|udf4|udf3|udf2|udf1|email|firstname|productinfo|amount|txnid|key)
     */
    public function verifyHash($response)
    {
        $salt = $this->merchantSalt;
        $status = $response['status'];
        $unmappedstatus = $response['unmappedstatus'];
        $key = $response['key'];
        $txnid = $response['txnid'];
        $amount = $response['amount'];
        $productinfo = $response['productinfo'];
        $firstname = $response['firstname'];
        $email = $response['email'];
        $udf1 = $response['udf1'] ?? '';
        $udf2 = $response['udf2'] ?? '';
        $udf3 = $response['udf3'] ?? '';
        $udf4 = $response['udf4'] ?? '';
        $udf5 = $response['udf5'] ?? '';
        $hash = $response['hash'];

        $hashData = [
            $salt,
            $status,
            '', '', '', '', '', // udf10 to udf6
            $udf5,
            $udf4,
            $udf3,
            $udf2,
            $udf1,
            $email,
            $firstname,
            $productinfo,
            $amount,
            $txnid,
            $key
        ];

        $hashString = implode('|', $hashData);
        $calculatedHash = strtolower(hash('sha512', $hashString));

        if ($calculatedHash !== $hash) {
            Log::error('PayU Hash Verification Failed', [
                'calculated' => $calculatedHash,
                'received' => $hash,
                'response' => $response
            ]);
            return false;
        }

        return true;
    }

    public function getPaymentUrl()
    {
        return $this->paymentUrl;
    }

    public function getMerchantKey()
    {
        return $this->merchantKey;
    }
}
