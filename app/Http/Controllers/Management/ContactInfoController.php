<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\ContactInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactInfoController extends Controller
{
    /**
     * Display the contact information management page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $contactInfo = ContactInfo::first();
        return view('management.contact-info', compact('contactInfo'));
    }

    /**
     * Update the contact information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'address' => 'required|string',
            'whatsapp' => 'nullable|string|max:20',
            'instagram_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Find or create the contact info record
            $contactInfo = ContactInfo::firstOrNew([]);
            $contactInfo->mobile = $request->mobile;
            $contactInfo->email = $request->email;
            $contactInfo->address = $request->address;
            $contactInfo->whatsapp = $request->whatsapp;
            $contactInfo->instagram_url = $request->instagram_url;
            $contactInfo->facebook_url = $request->facebook_url;
            $contactInfo->twitter_url = $request->twitter_url;
            $contactInfo->youtube_url = $request->youtube_url;
            $contactInfo->status = $request->status;
            $contactInfo->save();

            return redirect()->back()->with('success', 'Contact information updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update contact information: ' . $e->getMessage())->withInput();
        }
    }
}