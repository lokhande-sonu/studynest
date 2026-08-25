<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\School;
use App\Models\ProductCategory;

class SitemapController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $schools = School::all();
        $categories = ProductCategory::all();

        return response()->view('sitemap', [
            'products' => $products,
            'schools' => $schools,
            'categories' => $categories,
        ])->header('Content-Type', 'text/xml');
    }
}
