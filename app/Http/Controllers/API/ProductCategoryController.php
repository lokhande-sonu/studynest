<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    public function getProductCategories()
    {
        // $categories = ProductCategory::where('cat_status', 1)->get();
        
        $folderName = env('PRODUCT_CATEGORY_PHOTO', 'product-category-photo');
            $baseUrl = asset($folderName);

            $categories = ProductCategory::where('cat_status', 1)->get()->map(function ($categories) use ($baseUrl) {
                $categories->cat_photo = $baseUrl . '/' . $categories->cat_photo;
                return $categories;
            });
            
        if ($categories->isNotEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'Product categories fetched successfully',
                'data' => $categories
            ], 200);
        }
    
        return response()->json([
            'status' => false,
            'message' => 'No product categories available',
        ], 200);
    }
}
