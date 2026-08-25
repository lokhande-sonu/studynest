<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function getProducts(Request $request)
    {
        $folderName = env('PRODUCT_PHOTO', 'product-photo');
        $baseUrl = asset($folderName);
        
        $query = Product::with('inventory')
            ->where('p_status', 1);
        
        if ($request->category_id) {
            $query->where('p_cat_id', $request->category_id);
        }
        
        if ($request->tag) {
            $query->where('p_tag', $request->tag);
        }
        
        /* 🔥 EXECUTE QUERY ONLY ONCE */
        $products = $query->get()->map(function ($product) use ($baseUrl) {
            if ($product->p_photo) {
                $product->p_photo = $baseUrl . '/' . $product->p_photo;
            }
            return $product;
        });
    
        // Merge extra fields inside same product array
        $formatted = $products->map(function ($product) {
    
            // total stock from all inventory rows
            $totalStock = $product->inventory->sum('p_stock_qty');
    
            // stock status
            $stockStatus = $totalStock > 0 ? "Available" : "Out of Stock";
    
            // first unit or blank
            $firstUnit = optional($product->inventory->first())->product_unit ?? '';
    
            // convert model to array so we can append new keys
            $productArray = $product->toArray();
    
            // merge new fields
            $productArray['product_unit'] = $firstUnit;
            $productArray['stock_status'] = $stockStatus;
    
            return $productArray;
        });
    
        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $formatted
        ]);
    }


    // Product details
    public function getProductDetails($id)
    {
        $product = Product::with(['category', 'inventory'])->find($id);
        
        if ($product && $product->p_photo) {
            $folderName = env('PRODUCT_PHOTO', 'product-photo');
            $product->p_photo = asset($folderName . '/' . $product->p_photo);
        }
    
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Product not found',
                'data' => null
            ]);
        }
    
        // Total stock from all inventory rows
        $totalStock = $product->inventory->sum('p_stock_qty');
    
        // Determine stock status
        $stockStatus = $totalStock > 0 ? "Available" : "Out of Stock";
    
        // First product unit (if multiple)
        $firstUnit = optional($product->inventory->first())->product_unit ?? '';
    
        // Convert to array and inject new fields
        $productArray = $product->toArray();
        $productArray['product_unit'] = $firstUnit;
        $productArray['stock_status'] = $stockStatus;
    
        return response()->json([
            'status' => true,
            'message' => 'Product details fetched',
            'data' => $productArray
        ]);
    }

}
