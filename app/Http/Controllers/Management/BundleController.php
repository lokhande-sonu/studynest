<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Product;
use App\Models\School;
use App\Models\Classes;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class BundleController extends Controller
{
    public function index()
    {
        $bundles = Bundle::withCount('products')->orderBy('b_id', 'desc')->get();
        $schools = School::where('sch_status', 1)->get();
        $classes = Classes::where('class_status', 1)->get();
        $subjects = Subject::where('subject_status', 1)->get();
        
        // Pass products with their variants for selection
        $products = Product::with('variants')->where('p_status', 1)->select('p_id', 'p_name', 'p_photo', 'p_brand')->get();

        return view('management.bundle.index', compact('bundles', 'schools', 'classes', 'subjects', 'products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'b_name' => 'required|string',
            'b_short_desc' => 'required|string',
            'b_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'b_status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $uploadPath = 'uploads/bundle';
            $imageName = null;

            if ($request->hasFile('b_image')) {
                if (!file_exists(public_path($uploadPath))) {
                    mkdir(public_path($uploadPath), 0777, true);
                }
                $image = $request->file('b_image');
                $imageName = time() . '.' . $image->extension();
                $image->move(public_path($uploadPath), $imageName);
            }

            $bundle = Bundle::create([
                'b_name' => $request->b_name,
                'b_short_desc' => $request->b_short_desc,
                'b_image' => $imageName,
                'b_status' => $request->b_status,
                'is_all_schools' => $request->has('is_all_schools') ? 1 : 0,
                'is_all_classes' => $request->has('is_all_classes') ? 1 : 0,
                'is_all_subjects' => $request->has('is_all_subjects') ? 1 : 0,
            ]);

            // Sync relationships
            if (!$bundle->is_all_schools && $request->has('schools')) {
                $bundle->schools()->sync($request->schools);
            }
            if (!$bundle->is_all_classes && $request->has('classes')) {
                $bundle->classes()->sync($request->classes);
            }
            if (!$bundle->is_all_subjects && $request->has('subjects')) {
                $bundle->subjects()->sync($request->subjects);
            }

            return redirect()->route('management.bundles.index')->with('success', 'Bundle created successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $bundle = Bundle::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'b_name' => 'required|string',
            'b_short_desc' => 'required|string',
            'b_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'b_status' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $uploadPath = 'uploads/bundle';
            
            if ($request->hasFile('b_image')) {
                // Delete old image
                if ($bundle->b_image && File::exists(public_path($uploadPath . '/' . $bundle->b_image))) {
                    File::delete(public_path($uploadPath . '/' . $bundle->b_image));
                }

                $image = $request->file('b_image');
                $imageName = time() . '.' . $image->extension();
                $image->move(public_path($uploadPath), $imageName);
                $bundle->b_image = $imageName;
            }

            $bundle->b_name = $request->b_name;
            $bundle->b_short_desc = $request->b_short_desc;
            $bundle->b_status = $request->b_status;
            $bundle->is_all_schools = $request->has('is_all_schools') ? 1 : 0;
            $bundle->is_all_classes = $request->has('is_all_classes') ? 1 : 0;
            $bundle->is_all_subjects = $request->has('is_all_subjects') ? 1 : 0;
            $bundle->save();

            // Sync relationships
            if ($bundle->is_all_schools) {
                $bundle->schools()->detach();
            } else {
                $bundle->schools()->sync($request->schools ?? []);
            }

            if ($bundle->is_all_classes) {
                $bundle->classes()->detach();
            } else {
                $bundle->classes()->sync($request->classes ?? []);
            }

            if ($bundle->is_all_subjects) {
                $bundle->subjects()->detach();
            } else {
                $bundle->subjects()->sync($request->subjects ?? []);
            }

            return redirect()->route('management.bundles.index')->with('success', 'Bundle updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $bundle = Bundle::findOrFail($id);
        $bundle->delete(); // This will trigger the deleting event in Model to delete image
        return redirect()->route('management.bundles.index')->with('success', 'Bundle deleted successfully.');
    }

    public function getProducts($id)
    {
        $bundle = Bundle::with(['products' => function($query) {
            $query->select('tbl_products.p_id');
        }])->findOrFail($id);

        $selectedProducts = $bundle->products->map(function($product) {
            return [
                'product_id' => $product->p_id,
                'quantity' => $product->pivot->quantity,
                'variant_id' => $product->pivot->product_variant_id
            ];
        });

        return response()->json([
            'bundle_name' => $bundle->b_name,
            'products' => $selectedProducts
        ]);
    }

    public function updateProducts(Request $request, $id)
    {
        $bundle = Bundle::findOrFail($id);
        
        $syncData = [];
        if ($request->has('product_id')) {
            foreach ($request->product_id as $key => $productId) {
                // If the product is not checked (some browsers might send all if hidden inputs are used,
                // but here we expect only checked ones due to form submission logic)
                $syncData[$productId] = [
                    'quantity' => $request->quantity[$key] ?? 1,
                    'product_variant_id' => $request->variant_id[$key] ?? null
                ];
            }
        }

        $bundle->products()->sync($syncData);
        return redirect()->route('management.bundles.index')->with('success', 'Bundle products updated successfully.');
    }
}
