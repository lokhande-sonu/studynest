<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Carbon\Carbon;

class AuthController extends Controller
{
    // Validate user - check mobile exists and send OTP (mock otp)
    public function validateUser(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $customer = Customer::where('cust_mobile', $request->mobile)->first();
    
        // Mock OTP
        $otp = 1234;
    
        return response()->json([
            'status' => true,
            'message' => $customer 
                ? 'User exists. OTP sent.'
                : 'New user. OTP sent.',
            'data' => [
                'user_exists' => $customer ? 1 : 0,
                'otp' => $otp
            ]
        ]);
    }
    
    // Auto login check (validate token)
    public function checkTokenValidity(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric',
            'token' => 'required|string'
        ]);
    
        $customer = Customer::where('cust_mobile', $request->mobile)->first();
    
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => null
            ]);
        }
    
        // Token mismatch
        if ($customer->cust_api_token !== $request->token) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token',
                'data' => null
            ]);
        }
    
        // Check if token expired
        if (!$customer->cust_api_token_validity || now()->greaterThan($customer->cust_api_token_validity)) {
            return response()->json([
                'status' => false,
                'message' => 'Token expired',
                'data' => null
            ]);
        }
    
        // Token valid → auto login success
        return response()->json([
            'status' => true,
            'message' => 'Token is valid',
            'data' => [
                'customer' => $customer
            ]
        ]);
    }



    // Login existing user using OTP
    public function userLogin(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits:10',
            'otp_check' => 'required|numeric'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        // OTP always 1234 (mock)
        if ($request->otp_check != 1) {
            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed',
            ]);
        }
    
        $customer = Customer::where('cust_mobile', $request->mobile)->first();
    
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ]);
        }
    
        $token = bin2hex(random_bytes(30));
    
        $customer->update([
            'cust_api_token' => $token,
            'cust_api_token_validity' => Carbon::now()->addDays(30)
        ]);
        
        // Add Full Photo URL
        $photoBasePath = rtrim(env('CUSTOMER_PROFILE_PHOTO'), '/');
        $baseUrl = asset($photoBasePath);
        $customer->cust_profile_photo = $customer->cust_profile_photo
            ? $baseUrl . '/' . $customer->cust_profile_photo
            : null;
    
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'data' => [
                'customer' => $customer,
                'token' => $token
            ]
        ]);
    }


    // Register customer
    public function userRegister(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'mobile'    => 'required|numeric|digits:10|unique:tbl_customers,cust_mobile',
            'email'     => 'nullable|email|unique:tbl_customers,cust_email',
            'address'   => 'required|string',
            'city'      =>  'required|string',
            'state'     =>  'required|string',
            'gender'    =>  'required|string',
            'pincode'   =>  'required|numeric|digits:6'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
        
        $token = bin2hex(random_bytes(30));
    
        $customer = Customer::create([
            'cust_name'     => $request->name,
            'cust_mobile'   => $request->mobile,
            'cust_email'    => $request->email,
            'cust_address'  => $request->address,
            'cust_city'     => $request->city,
            'cust_state'    => $request->state,
            'cust_country'  => 'India',
            'cust_pincode'  => $request->pincode,
            'cust_gender'   => $request->gender,
            'cust_status'   => 1,
            'cust_created_at' => now(),
            'cust_api_token' => $token,
            'cust_api_token_validity' => Carbon::now()->addDays(30)
            
        ]);
        
        $customer->token = $token;
    
        return response()->json([
            'status' => true,
            'message' => 'Customer registered successfully',
            'data' => $customer,
        ]);
    }

}
