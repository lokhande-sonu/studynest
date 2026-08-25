<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AppSlider;

class AppSliderController extends Controller
{
    /**
     * Get all active slider images
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllSliders()
    {
        try {
            $folderName = env('SLIDER_FOLDER', 'app-sliders');
            $baseUrl = asset('uploads/' . $folderName);

            $sliders = AppSlider::where('status', 1)->get()->map(function ($slider) use ($baseUrl) {
                $slider->full_url = $baseUrl . '/' . $slider->slider_photo_url;
                return $slider;
            });

            return response()->json([
                'status' => true,
                'message' => 'Sliders retrieved successfully',
                'data' => $sliders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve sliders',
                'data' => null
            ], 500);
        }
    }
}
