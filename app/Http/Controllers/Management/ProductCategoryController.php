<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ProductCategory::withCount('products')
                ->orderBy('cat_id', 'DESC')
                ->get();        
        
        return view('management.product-category', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cat_name' => 'required|string|max:255',
            'cat_status' => 'required|in:0,1',
            'cat_photo' => 'required|image|mimes:jpg,jpeg,png,webp'
        ]);
    
        $folder = env('PRODUCT_CATEGORY_PHOTO');
    
        $photoName = time() . '.' . $request->cat_photo->extension();
        $request->cat_photo->move(public_path($folder), $photoName);
    
        ProductCategory::create([
            'cat_name' => $request->cat_name,
            'cat_status' => $request->cat_status,
            'cat_photo' => $photoName,
            'cat_created_at' => now(),
            'cat_updated_at' => now()
        ]);
    
        return back()->with('success', 'Category created successfully.');
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'cat_name' => 'required|string|max:255',
            'cat_status' => 'required|in:0,1',
            'cat_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp'
        ]);
    
        $category = ProductCategory::findOrFail($id);
        $folder = env('PRODUCT_CATEGORY_PHOTO');
    
        // Update basic fields
        $category->cat_name = $request->cat_name;
        $category->cat_status = $request->cat_status;
    
        // Update photo if uploaded
        if ($request->hasFile('cat_photo')) {
    
            // Delete old photo
            $oldPath = public_path($folder . '/' . $category->cat_photo);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
    
            // Upload new photo
            $newName = time() . '.' . $request->cat_photo->extension();
            $request->cat_photo->move(public_path($folder), $newName);
            $category->cat_photo = $newName;
        }
    
        $category->cat_updated_at = now();
        $category->save();
    
        return back()->with('success', 'Category updated successfully.');
    }


    public function updateStatus($id)
    {
        $category = ProductCategory::findOrFail($id);

        $category->cat_status = $category->cat_status == 1 ? 0 : 1;
        $category->cat_updated_at = now();
        $category->save();

        return back()->with('success', 'Category status updated.');
    }

    public function destroy($id)
    {
        ProductCategory::destroy($id);
        return back()->with('success', 'Category deleted successfully.');
    }
}
