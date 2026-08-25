<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContactInfo;

class ContactInfoController extends Controller
{
    /**
     * Get contact information
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getContactInfo()
    {
        try {
            $contactInfo = ContactInfo::where('status', 1)->first();
            
            if (!$contactInfo) {
                return response()->json([
                    'status' => false,
                    'message' => 'Contact information not found',
                    'data' => null
                ], 404);
            }
            
            return response()->json([
                'status' => true,
                'message' => 'Contact information retrieved successfully',
                'data' => $contactInfo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve contact information',
                'data' => null
            ], 500);
        }
    }
}
