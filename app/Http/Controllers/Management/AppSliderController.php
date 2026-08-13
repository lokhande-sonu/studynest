<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\AppSlider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AppSliderController extends Controller
{
    /**
     * Display a listing of the slider images.
     */
    public function index()
    {
        $sliders = AppSlider::orderBy('id', 'desc')->get();
        return view('management.app-slider', compact('sliders'));
    }

    /**
     * Store a newly created slider image.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'  => 'required|image|mimes:jpeg,png,jpg,gif',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $folderName = env('SLIDER_FOLDER', 'app-sliders');
        $uploadPath = public_path('uploads/' . $folderName);

        // Ensure folder exists
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Generate unique filename
        $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();

        // Move file to uploads directory
        $request->file('image')->move($uploadPath, $imageName);

        // Save to DB
        AppSlider::create([
            'slider_photo_url' => $imageName,
            'status'    => $request->status,
        ]);

        return redirect()->back()->with('success', 'Slider image added successfully.');
    }

    /**
     * Update the specified slider image.
     */
    public function update(Request $request, $id)
    {
        $slider = AppSlider::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'image'  => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->hasFile('image')) {
            $folderName = env('SLIDER_FOLDER', 'app-sliders');
            $uploadPath = public_path('uploads/' . $folderName);

            // Ensure folder exists
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Delete old image
            if ($slider->slider_photo_url && File::exists($uploadPath . '/' . $slider->slider_photo_url)) {
                File::delete($uploadPath . '/' . $slider->slider_photo_url);
            }

            // Generate unique filename
            $imageName = time() . '_' . Str::random(10) . '.' . $request->file('image')->getClientOriginalExtension();
            
            // Move file to uploads directory
            $request->file('image')->move($uploadPath, $imageName);
            
            $slider->slider_photo_url = $imageName;
        }

        $slider->status = $request->status;
        $slider->save();

        return redirect()->back()->with('success', 'Slider updated successfully.');
    }

    /**
     * Update the status of the specified slider image.
     */
    public function updateStatus(Request $request, $id)
    {
        $slider = AppSlider::findOrFail($id);
        $slider->status = $request->status;
        $slider->save();

        return redirect()->back()->with('success', 'Slider status updated successfully.');
    }

    /**
     * Remove the specified slider image from storage.
     */
    public function destroy($id)
    {
        $slider = AppSlider::findOrFail($id);

        $folderName = env('SLIDER_FOLDER', 'app-sliders');
        $uploadPath = public_path('uploads/' . $folderName);

        if ($slider->slider_photo_url && File::exists($uploadPath . '/' . $slider->slider_photo_url)) {
            File::delete($uploadPath . '/' . $slider->slider_photo_url);
        }

        $slider->delete();

        return redirect()->back()->with('success', 'Slider image deleted successfully.');
    }
}
