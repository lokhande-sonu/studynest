<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    public function getStates(Request $request)
    {
        // Custom JSON validator
        $validator = Validator::make($request->all(), [
            'country_code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        // If country code is 91 -> return states
        if ($request->country_code == 91) {

            $states = [
                "Andhra Pradesh",
                "Arunachal Pradesh",
                "Assam",
                "Bihar",
                "Chhattisgarh",
                "Goa",
                "Gujarat",
                "Haryana",
                "Himachal Pradesh",
                "Jharkhand",
                "Karnataka",
                "Kerala",
                "Madhya Pradesh",
                "Maharashtra",
                "Manipur",
                "Meghalaya",
                "Mizoram",
                "Nagaland",
                "Odisha",
                "Punjab",
                "Rajasthan",
                "Sikkim",
                "Tamil Nadu",
                "Telangana",
                "Tripura",
                "Uttar Pradesh",
                "Uttarakhand",
                "West Bengal",

                // Union Territories
                "Andaman and Nicobar Islands",
                "Chandigarh",
                "Dadra and Nagar Haveli and Daman and Diu",
                "Delhi",
                "Jammu and Kashmir",
                "Ladakh",
                "Lakshadweep",
                "Puducherry"
            ];

            return response()->json([
                'status' => true,
                'message' => 'States fetched successfully',
                'data' => $states
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Currently not serviceable',
            'data' => []
        ], 200);
    }
}
