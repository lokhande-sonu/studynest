<?php 

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function getCustomerProfile(Request $request)
    {
        $customer = $request->auth_customer;
    
        // Build full URL
        $photoBasePath = rtrim(env('CUSTOMER_PROFILE_PHOTO'), '/');   // example: https://domain.com/uploads/customer/
        $baseUrl = asset($photoBasePath);
        $customer->cust_profile_photo = $customer->cust_profile_photo 
            ? $baseUrl . '/' . $customer->cust_profile_photo 
            : null;
    
        return response()->json([
            'status' => true,
            'message' => 'Profile fetched',
            'data' => $customer
        ]);
    }

    public function updateCustomerProfile(Request $request)
    {
        $customer = $request->auth_customer;
    
        // Validation (all required except email & profile photo)
        $request->validate([
            'cust_name'     => 'required|string|max:255',
            'cust_address'  => 'required|string',
            'cust_city'     => 'required|string|max:100',
            'cust_state'    => 'required|string|max:100',
            'cust_pincode'  => 'required|numeric|digits:6',
            'cust_gender'   => 'required|string|max:10',
    
            'cust_email'    => 'nullable|email|max:255',
            'cust_profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4048'
        ], [
            'cust_name.required' => 'Customer name is required',
            'cust_address.required' => 'Customer Address is required',
            'cust_city.required' => 'Customer City is required',
            'cust_state.required' => 'Customer State is required',
            'cust_pincode.required' => 'Customer Pincode is required',
            'cust_gender.required' => 'Customer Gender is required'
        ]);
    
        $folder = env('CUSTOMER_PROFILE_PHOTO'); // e.g. uploads/customers/
    
        // Upload photo if provided
        if ($request->hasFile('cust_profile_photo')) {
    
            if (!is_dir(public_path($folder))) {
                mkdir(public_path($folder), 0777, true);
            }
    
            // Remove old photo
            if ($customer->cust_profile_photo && file_exists(public_path($folder . $customer->cust_profile_photo))) {
                unlink(public_path($folder . $customer->cust_profile_photo));
            }
    
            $file = $request->file('cust_profile_photo');
            $filename = "/" . time() . "_" . uniqid() . "." . $file->extension();
            $file->move(public_path($folder), $filename);
    
            $customer->cust_profile_photo = $filename;
        }
    
        // Update customer fields
        $customer->cust_name    = $request->cust_name;
        $customer->cust_email   = $request->cust_email;
        $customer->cust_address = $request->cust_address;
        $customer->cust_city    = $request->cust_city;
        $customer->cust_state   = $request->cust_state;
        $customer->cust_country = "India";
        $customer->cust_pincode = $request->cust_pincode;
        $customer->cust_gender  = $request->cust_gender;
    
        $customer->cust_updated_at = now();
        $customer->save();
    
        // Build full URL for photo
        $photo_url = $customer->cust_profile_photo
            ? url($folder . $customer->cust_profile_photo)
            : null;
    
        return response()->json([
            'status'  => true,
            'message' => 'Profile updated successfully',
            'data'    => [
                "cust_id"       => $customer->cust_id,
                "cust_name"     => $customer->cust_name,
                "cust_email"    => $customer->cust_email,
                "cust_address"  => $customer->cust_address,
                "cust_city"     => $customer->cust_city,
                "cust_state"    => $customer->cust_state,
                "cust_country"  => $customer->cust_country,
                "cust_pincode"  => $customer->cust_pincode,
                "cust_gender"   => $customer->cust_gender,
                "cust_profile_photo" => $photo_url,
                "cust_updated_at"    => $customer->cust_updated_at,
            ]
        ]);
    }

}
