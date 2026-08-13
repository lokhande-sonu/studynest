<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * Send WhatsApp Notification via Aisensy/Buzzflow
     */

    public static function sendWhatsApp($mobile, $campaignName, $templateParams = [], $userName = 'Customer')
    {
        $apiKey = env('WHATSAPP_API_KEY');
    
        // if (!$apiKey) {
        //     Log::error("WHATSAPP API Key not found in .env");
        //     return false;
        // }
    
        // Clean mobile number
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
    
        if (strlen($mobile) == 10) {
            $mobile = '+91' . $mobile;
        } elseif (strlen($mobile) > 10 && !str_starts_with($mobile, '+')) {
            $mobile = '+' . $mobile;
        }
    
        // FORCE all params to string (CRITICAL FIX)
        $templateParams = array_map(function ($param) {
            return strval($param);
        }, $templateParams);
    
        try {
            // Build payload separately (clean + debug-friendly) AIsensy
            // $payload = [
            //     'apiKey' => $apiKey,
            //     'campaignName' => $campaignName,
            //     'destination' => $mobile,
            //     'userName' => (string) $userName,
            //     'source' => 'Studynest',
            //     'templateParams' => $templateParams,
            // ];
            
             $payload = [
                'type' => "buttonTemplate",
                'templateId' => $campaignName,
                'templateLanguage' => "en",
                'sender_phone' => $mobile,
                'templateArgs' => $templateParams,
            ];
    
            // Dbug log (optional but useful)
            // Log::info('WhatsApp Payload', [
            //     'payload' => $payload,
            //     'types' => array_map('gettype', $templateParams)
            // ]);
    
            // Send request AI Sensy
            // $response = Http::withHeaders([
            //     'Content-Type' => 'application/json'
            // ])->post('https://backend.aisensy.com/campaign/t1/api/v2', $payload);
            
             $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post('https://api.buzzflow.co.in/API_V2/Whatsapp/send_template/cEE4MndhL3p3b2RIRXY1WFdOQldvdz09', $payload);
    
                    Log::error("WhatsApp failed Response " . $response);

                Log::error("WhatsApp failed Response Body: " . $response->body());
            if ($response->successful()) {
                Log::info("WhatsApp sent successfully to $mobile. Campaign: $campaignName");
                return true;
            } else {
                Log::error("WhatsApp failed for $mobile. Response: " . $response->body());
                return false;
            }
    
        } catch (\Exception $e) {
            Log::error("WhatsApp exception for $mobile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS Notification via bulk9.com
     */
    public static function sendSMS($mobile, $message, $templateId = null, $entityId = null)
    {
        $authKey = env('SMS_AUTH_KEY');
        if (!$authKey) {
            Log::error("SMS Auth Key not found in .env");
            return false;
        }

        // Ensure mobile is 10 digits or handled by bulk9 requirements
        if (strlen($mobile) > 10 && str_starts_with($mobile, '91')) {
            $mobile = substr($mobile, 2);
        }

        try {
            $response = Http::withHeaders([
                'authorization' => $authKey,
                'Content-Type' => 'application/json',
            ])->post('https://bulk9.com/dev/bulkV2', [
                'route' => 'dlt_manual',
                'sender_id' => env('SMS_SENDER_ID', ''),
                'entity_id' => $entityId ?? env('SMS_ENTITY_ID'),
                'template_id' => $templateId,
                'message' => $message,
                'flash' => 0,
                'numbers' => $mobile,
            ]);

            if ($response->successful()) {
                Log::info("SMS sent successfully to $mobile. Message: $message");
                return true;
            } else {
                Log::error("SMS failed for $mobile. Response: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("SMS exception for $mobile: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Trigger all notifications (WhatsApp & SMS)
     */
    public static function notify($type, $data)
    {
        $mobile = $data['mobile'] ?? null;
        if (!$mobile) return;

        $userName = $data['name'] ?? 'Customer';

        switch ($type) {
            case 'order_placed':
                // WhatsApp
                self::sendWhatsApp($mobile, 'order_place_notification', [
                    $userName,
                    $data['order_id'],
                    $data['total_amount']
                ], $userName);
                
                Log::info("Template Para: {$userName} Order ID: {$data['order_id']} Amount: {$data['total_amount']}");

                // SMS
                $msg = "Hello $userName, your order #{$data['order_id']} of ₹{$data['total_amount']} has been placed successfully on Studynest.";
                self::sendSMS($mobile, $msg, env('SMS_TEMPLATE_ORDER_PLACED'));
                break;

            case 'order_status_update':
                // WhatsApp
                self::sendWhatsApp($mobile, 'order_status_notification', [
                    $userName,
                    $data['order_id'],
                    $data['status']
                ], $userName);

                // SMS
                $msg = "Hello $userName, your order #{$data['order_id']} status has been updated to: {$data['status']}. Team Studynest";
                self::sendSMS($mobile, $msg, env('SMS_TEMPLATE_ORDER_STATUS'));
                break;

            case 'prebooking_confirmation':
                // WhatsApp
                self::sendWhatsApp($mobile, 'Prebooking Confirmation Notification', [
                    $userName,
                    $data['school_name'],
                    $data['class']
                ], $userName);

                // SMS
                $msg = "Hello $userName, your pre-booking for {$data['school_name']} has been confirmed successfully. Team Studynest";
                self::sendSMS($mobile, $msg, env('SMS_TEMPLATE_PREBOOKING'));
                break;
        }
    }
}
