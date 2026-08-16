<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Validate app header token again for safety
        $appToken = config('api.token');

        // Fail closed: without a configured token every API request is rejected.
        if ($appToken === '' || $appToken === null || $request->header('Authorization') !== $appToken) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid App Token'
            ], 401);
        }

        // Check login token
        $loginToken = $request->header('User-Auth-Token');

        if (!$loginToken) {
            return response()->json([
                'status' => false,
                'message' => 'Login token is required'
            ], 401);
        }

        $customer = Customer::where('cust_api_token', $loginToken)->first();
        
        if (!$customer) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid login token'
            ], 401);
        }

        // Check if token expired
        if (!$customer->cust_api_token_validity || now()->greaterThan($customer->cust_api_token_validity)) {
            return response()->json([
                'status' => false,
                'message' => 'Login Token expired',
                'data' => null
            ]);
        }

        // Set customer globally
        $request->merge(['auth_customer' => $customer]);

        return $next($request);
    }
}
