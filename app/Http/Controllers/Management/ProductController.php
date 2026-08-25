<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductVariant;
use App\Models\ProductStockInventory;
use App\Models\School;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'variants', 'stockInventories'])->orderBy('p_id', 'desc')->get();
        return view('management.product', compact('products'));
    }

    public function create()
    {
        $categories = ProductCategory::where('cat_status', 1)->get();
        $schools = School::where('sch_status', 1)->get();
        $classes = Classes::where('class_status', 1)->get();
        $subjects = Subject::where('subject_status', 1)->get();

        return view('management.add-product', compact('categories', 'schools', 'classes', 'subjects'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'p_name'        => 'required|string',
            'p_cat_id'      => 'required|integer',
            'p_brand'       => 'nullable|string',
            'p_gender'      => 'required|in:Not Applicable,Unisex,Boys,Girls',
            'p_photos'      => 'required|array|min:1|max:5',
            'p_photos.*'    => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'p_short_desc'  => 'required|string',
            'p_full_desc'   => 'nullable|string',
            'p_tag'         => 'nullable|string',
            
            // Stock/Variant validation
            'variant_name'  => 'required|array',
            'variant_name.*'=> 'required|string',
            'prod_sku'      => 'required|array',
            'prod_sku.*'    => 'required|string|distinct', // distinct in request
            'available_stock'=> 'required|array',
            'available_stock.*'=> 'required|numeric|min:0',
            'unit_price'    => 'required|array',
            'unit_price.*'  => 'required|numeric|min:0',
            'discounted_unit_price' => 'nullable|array',
            'discounted_unit_price.*' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Upload Photos
            $uploadPath = env('PRODUCT_PHOTO', 'uploads/product-photo');
            $mainPhoto = null;
            $uploadedPhotos = [];

            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0777, true);
            }

            if ($request->hasFile('p_photos')) {
                foreach ($request->file('p_photos') as $index => $photo) {
                    $filename = time() . '_' . $index . '.' . $photo->extension();
                    $photo->move(public_path($uploadPath), $filename);
                    $uploadedPhotos[] = $filename;
                    
                    if ($index === 0) {
                        $mainPhoto = $filename;
                    }
                }
            }

            // Create Product
            $product = Product::create([
                'p_cat_id'      => $request->p_cat_id,
                'p_name'        => $request->p_name,
                'p_tag'         => $request->p_tag,
                'p_highlight'   => $request->p_highlight,
                'p_short_desc'  => $request->p_short_desc,
                'p_full_desc'   => $request->p_full_desc,
                'p_brand'       => $request->p_brand,
                'p_gender'      => $request->p_gender,
                'p_photo'       => $mainPhoto,
                'p_status'      => $request->p_status ?? 1,
                'is_all_schools'=> $request->has('is_all_schools') ? 1 : 0,
                'is_all_classes'=> $request->has('is_all_classes') ? 1 : 0,
                'is_all_subjects'=> $request->has('is_all_subjects') ? 1 : 0,
                'p_created_at'  => now(),
                'p_updated_at'  => now(),
            ]);

            // Save Images to Table
            foreach ($uploadedPhotos as $index => $photoName) {
                ProductImage::create([
                    'product_id' => $product->p_id,
                    'img_path' => $photoName,
                    'sort_order' => $index
                ]);
            }

            // Sync Relationships
            if (!$product->is_all_schools && $request->has('schools')) {
                $product->schools()->sync($request->schools);
            }
            
            if (!$product->is_all_classes && $request->has('classes')) {
                $product->classes()->sync($request->classes);
            }

            if (!$product->is_all_subjects && $request->has('subjects')) {
                $product->subjects()->sync($request->subjects);
            }

            // Create Variants and Stock
            if ($request->has('variant_name')) {
                foreach ($request->variant_name as $key => $vName) {
                    if (empty($vName)) continue;

                    // Create Variant
                    $variant = ProductVariant::create([
                        'product_id' => $product->p_id,
                        'prod_variant' => $vName,
                        'status' => 1
                    ]);

                    // Calculate CGST and SGST (half of GST rate each)
                    $gstRate = $request->gst_rate[$key] ?? 18;
                    $cgstRate = $gstRate / 2;
                    $sgstRate = $gstRate / 2;

                    // Create Stock Inventory
                    ProductStockInventory::create([
                        'prod_sku' => $request->prod_sku[$key],
                        'prod_id' => $product->p_id,
                        'prod_variant_id' => $variant->prod_variant_id,
                        'available_stock' => $request->available_stock[$key],
                        'unit_price' => $request->unit_price[$key],
                        'discounted_unit_price' => $request->discounted_unit_price[$key] ?? null,
                        'gst_rate' => $gstRate,
                        'gst_type' => $request->gst_type[$key] ?? 'inclusive',
                        'cgst_rate' => $cgstRate,
                        'sgst_rate' => $sgstRate,
                        'status' => 1
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('management.products.index')->with('success', 'Product created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Delete uploaded files if error
            if (!empty($uploadedPhotos)) {
                foreach ($uploadedPhotos as $photoName) {
                    if (file_exists(public_path($uploadPath . '/' . $photoName))) {
                        @unlink(public_path($uploadPath . '/' . $photoName));
                    }
                }
            }
            return redirect()->back()->with('error', 'Failed to create product: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $product = Product::with(['category', 'variants', 'stockInventories.variant', 'schools', 'classes', 'subjects', 'images'])->findOrFail($id);
        
        // Auto-migrate legacy photo to ProductImage if needed
        if ($product->p_photo && $product->images->count() == 0) {
            ProductImage::create([
                'product_id' => $product->p_id,
                'img_path' => $product->p_photo,
                'sort_order' => 0
            ]);
            $product->load('images');
        }

        $categories = ProductCategory::where('cat_status', 1)->get();
        $schools = School::where('sch_status', 1)->get();
        $classes = Classes::where('class_status', 1)->get();
        $subjects = Subject::where('subject_status', 1)->get();

        return view('management.edit-product', compact('product', 'categories', 'schools', 'classes', 'subjects'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'p_name'        => 'required|string',
            'p_cat_id'      => 'required|integer',
            'p_brand'       => 'nullable|string',
            'p_gender'      => 'required|in:Not Applicable,Unisex,Boys,Girls',
            'p_short_desc'  => 'required|string',
            'p_photos'      => 'nullable|array|max:5',
            'p_photos.*'    => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'p_full_desc'   => 'nullable|string',
            
             // Stock/Variant validation
             'variant_name'  => 'required|array',
             'variant_name.*'=> 'required|string',
             'prod_sku'      => 'required|array',
             'prod_sku.*'    => 'required|string|distinct', 
             'available_stock'=> 'required|array',
             'available_stock.*'=> 'required|numeric|min:0',
             'unit_price'    => 'required|array',
             'unit_price.*'  => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Handle Photos
            $uploadPath = env('PRODUCT_PHOTO', 'uploads/product-photo');
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0777, true);
            }

            // 1. Handle Deletions
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = ProductImage::find($imageId);
                    if ($image && $image->product_id == $product->p_id) {
                        if (file_exists(public_path($uploadPath . '/' . $image->img_path))) {
                            @unlink(public_path($uploadPath . '/' . $image->img_path));
                        }
                        $image->delete();
                    }
                }
            }

            // 2. Handle New Uploads
            if ($request->hasFile('p_photos')) {
                // Count check
                $existingCount = ProductImage::where('product_id', $product->p_id)->count();
                $newCount = count($request->file('p_photos'));
                
                if (($existingCount + $newCount) > 5) {
                    throw new \Exception("You can only upload up to 5 photos. You have $existingCount and tried to add $newCount.");
                }

                foreach ($request->file('p_photos') as $index => $photo) {
                    $filename = time() . '_' . uniqid() . '.' . $photo->extension(); 
                    $photo->move(public_path($uploadPath), $filename);
                    
                    ProductImage::create([
                        'product_id' => $product->p_id,
                        'img_path' => $filename,
                        'sort_order' => $existingCount + $index
                    ]);
                }
            }

            // 3. Update Main Cover Photo
            $firstImage = ProductImage::where('product_id', $product->p_id)->orderBy('sort_order')->orderBy('id')->first();
            if ($firstImage) {
                $product->p_photo = $firstImage->img_path;
            } else {
                $product->p_photo = null;
            }

            // Update Product
            $product->update([
                'p_cat_id'      => $request->p_cat_id,
                'p_name'        => $request->p_name,
                'p_tag'         => $request->p_tag,
                'p_highlight'   => $request->p_highlight,
                'p_short_desc'  => $request->p_short_desc,
                'p_full_desc'   => $request->p_full_desc,
                'p_brand'       => $request->p_brand,
                'p_gender'      => $request->p_gender,
                'p_status'      => $request->p_status ?? 1,
                'is_all_schools'=> $request->has('is_all_schools') ? 1 : 0,
                'is_all_classes'=> $request->has('is_all_classes') ? 1 : 0,
                'is_all_subjects'=> $request->has('is_all_subjects') ? 1 : 0,
                'p_updated_at'  => now(),
            ]);

            // Sync Relationships
            if (!$product->is_all_schools && $request->has('schools')) {
                $product->schools()->sync($request->schools);
            } else {
                $product->schools()->detach();
            }

            if (!$product->is_all_classes && $request->has('classes')) {
                $product->classes()->sync($request->classes);
            } else {
                $product->classes()->detach();
            }

            if (!$product->is_all_subjects && $request->has('subjects')) {
                $product->subjects()->sync($request->subjects);
            } else {
                $product->subjects()->detach();
            }

            // Handle Variants and Stock
            // Simplest strategy: Delete existing variants and stock, and recreate them.
            // This is destructive for IDs but easiest to implement for bulk edit without complex tracking.
            // If preserving IDs is important (e.g. for orders), we need a smarter update logic.
            // Given "modify current product management", usually we want to preserve if possible.
            // But to save time and complexity and ensure consistency with the new input array, 
            // I'll stick to delete-recreate for now, unless I can match by SKU.
            // Matching by SKU is better.

            // Get existing stock IDs
            $existingStockIds = $product->stockInventories->pluck('id')->toArray();
            $keptStockIds = [];

            if ($request->has('prod_sku')) {
                foreach ($request->prod_sku as $key => $sku) {
                    // Check if this SKU exists for this product
                    $existingStock = ProductStockInventory::where('prod_sku', $sku)
                        ->where('prod_id', $product->p_id)
                        ->first();

                    $vName = $request->variant_name[$key];

                    if ($existingStock) {
                        // Calculate CGST and SGST (half of GST rate each)
                        $gstRate = $request->gst_rate[$key] ?? 18;
                        $cgstRate = $gstRate / 2;
                        $sgstRate = $gstRate / 2;

                        // Update
                        $existingStock->update([
                            'available_stock' => $request->available_stock[$key],
                            'unit_price' => $request->unit_price[$key],
                            'discounted_unit_price' => $request->discounted_unit_price[$key] ?? null,
                            'gst_rate' => $gstRate,
                            'gst_type' => $request->gst_type[$key] ?? 'inclusive',
                            'cgst_rate' => $cgstRate,
                            'sgst_rate' => $sgstRate,
                            'status' => 1
                        ]);
                        
                        // Update variant name if needed
                        $existingStock->variant->update(['prod_variant' => $vName]);
                        
                        $keptStockIds[] = $existingStock->id;
                    } else {
                        // Create New
                        $variant = ProductVariant::create([
                            'product_id' => $product->p_id,
                            'prod_variant' => $vName,
                            'status' => 1
                        ]);

                        // Calculate CGST and SGST
                        $gstRate = $request->gst_rate[$key] ?? 18;
                        $cgstRate = $gstRate / 2;
                        $sgstRate = $gstRate / 2;

                        $newStock = ProductStockInventory::create([
                            'prod_sku' => $sku,
                            'prod_id' => $product->p_id,
                            'prod_variant_id' => $variant->prod_variant_id,
                            'available_stock' => $request->available_stock[$key],
                            'unit_price' => $request->unit_price[$key],
                            'discounted_unit_price' => $request->discounted_unit_price[$key] ?? null,
                            'gst_rate' => $gstRate,
                            'gst_type' => $request->gst_type[$key] ?? 'inclusive',
                            'cgst_rate' => $cgstRate,
                            'sgst_rate' => $sgstRate,
                            'status' => 1
                        ]);
                        $keptStockIds[] = $newStock->id;
                    }
                }
            }

            // Delete removed items
            $stocksToDelete = array_diff($existingStockIds, $keptStockIds);
            if (!empty($stocksToDelete)) {
                // Delete stocks
                ProductStockInventory::whereIn('id', $stocksToDelete)->delete();
                // Also delete orphan variants? 
                // Variants are linked 1:N but in my logic I created 1 variant per stock for simplicity.
                // I should cleanup variants that have no stock.
                $variantsToCheck = ProductVariant::where('product_id', $product->p_id)->get();
                foreach($variantsToCheck as $v) {
                     $hasStock = ProductStockInventory::where('prod_variant_id', $v->prod_variant_id)->exists();
                     if(!$hasStock) {
                         $v->delete();
                     }
                }
            }

            DB::commit();
            return redirect()->route('management.products.index')->with('success', 'Product updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update product: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        
        // Delete photo
        $uploadPath = env('PRODUCT_PHOTO', 'uploads/product-photo');
        if ($product->p_photo && file_exists(public_path($uploadPath . '/' . $product->p_photo))) {
            @unlink(public_path($uploadPath . '/' . $product->p_photo));
        }

        $product->stockInventories()->delete();
        $product->variants()->delete();
        $product->schools()->detach();
        $product->classes()->detach();
        $product->subjects()->detach();
        
        $product->delete();

        return back()->with('success', 'Product deleted successfully');
    }

    public function exportExcel()
    {
        $products = Product::with(['category', 'variants', 'stockInventories.variant', 'images'])->orderBy('p_id', 'desc')->get();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $uploadPath = env('PRODUCT_PHOTO', 'uploads/product-photo');
        $baseUrl = env('APP_URL', 'https://studynested.com');

        $headers = [
            'Product ID', 'Name', 'Category', 'Brand', 'Gender', 'Short Description', 'Full Description',
            'Is All Schools', 'Is All Classes', 'Is All Subjects', 'Schools', 'Classes', 'Subjects',
            'Variant Name', 'SKU', 'Stock', 'Unit Price', 'Discounted Price', 'GST Rate', 'GST Type', 'Status', 'Image URLs', 'Image Filenames'
        ];
        $sheet->fromArray($headers, NULL, 'A1');

        $data = [];
        foreach ($products as $product) {
            // Build image URLs and filenames
            $imageUrls = [];
            $imageFilenames = [];
            if ($product->images->count() > 0) {
                foreach ($product->images as $img) {
                    $imageUrls[] = $baseUrl . '/' . $uploadPath . '/' . $img->img_path;
                    $imageFilenames[] = $img->img_path;
                }
            } elseif ($product->p_photo) {
                $imageUrls[] = $baseUrl . '/' . $uploadPath . '/' . $product->p_photo;
                $imageFilenames[] = $product->p_photo;
            }

            $imageUrlsStr = implode("\n", $imageUrls);
            $imageFilenamesStr = implode(",", $imageFilenames);

            // Get Schools, Classes, Subjects as comma-separated names
            $schoolsList = $product->schools->pluck('sch_name')->implode(',');
            $classesList = $product->classes->pluck('class_name')->implode(',');
            $subjectsList = $product->subjects->pluck('subject_name')->implode(',');

            if ($product->stockInventories->count() > 0) {
                foreach ($product->stockInventories as $inventory) {
                    $data[] = [
                        $product->p_id,
                        $product->p_name,
                        $product->category->cat_name ?? 'N/A',
                        $product->p_brand,
                        $product->p_gender,
                        $product->p_short_desc,
                        $product->p_full_desc,
                        $product->is_all_schools ? 'Yes' : 'No',
                        $product->is_all_classes ? 'Yes' : 'No',
                        $product->is_all_subjects ? 'Yes' : 'No',
                        $schoolsList,
                        $classesList,
                        $subjectsList,
                        $inventory->variant->prod_variant ?? 'N/A',
                        $inventory->prod_sku,
                        $inventory->available_stock,
                        $inventory->unit_price,
                        $inventory->discounted_unit_price,
                        $inventory->gst_rate ?? 18,
                        $inventory->gst_type ?? 'inclusive',
                        $product->p_status == 1 ? 'Active' : 'Inactive',
                        $imageUrlsStr,
                        $imageFilenamesStr,
                    ];
                }
            } else {
                $data[] = [
                    $product->p_id,
                    $product->p_name,
                    $product->category->cat_name ?? 'N/A',
                    $product->p_brand,
                    $product->p_gender,
                    $product->p_short_desc,
                    $product->p_full_desc,
                    $product->is_all_schools ? 'Yes' : 'No',
                    $product->is_all_classes ? 'Yes' : 'No',
                    $product->is_all_subjects ? 'Yes' : 'No',
                    $schoolsList,
                    $classesList,
                    $subjectsList,
                    '', '', '', '', '', '', '',
                    $product->p_status == 1 ? 'Active' : 'Inactive',
                    $imageUrlsStr,
                    $imageFilenamesStr,
                ];
            }
        }

        if (!empty($data)) {
            $sheet->fromArray($data, NULL, 'A2');
        }

        // Enable text wrapping for image URLs column
        foreach ($sheet->getRowIterator() as $row) {
            $cell = $sheet->getCell('Q' . $row->getRowIndex());
            $cell->getStyle()->getAlignment()->setWrapText(true);
        }

        foreach (range('A', 'R') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }

        // Set larger width for image columns
        $sheet->getColumnDimension('Q')->setWidth(60);
        $sheet->getColumnDimension('R')->setWidth(40);

        $writer = new Xlsx($spreadsheet);
        $fileName = 'products_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . urlencode($fileName) . '"');
        $writer->save('php://output');
        exit;
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'import_file' => 'required|mimes:xlsx,xls,csv'
        ]);

        try {
            $file = $request->file('import_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Read header row and map column positions
            $headers = array_map('strtolower', array_map('trim', $rows[0]));
            $colMap = [];
            foreach ($headers as $index => $header) {
                $colMap[$header] = $index;
            }
            
            // Check if new format (has Schools/GST Rate columns) or old format
            $isNewFormat = isset($colMap['schools']) && isset($colMap['gst rate']);
            
            unset($rows[0]); // Remove header from data rows

            DB::beginTransaction();

            $successCount = 0;
            $errorCount = 0;
            $imageErrors = [];

            // Handle uploaded images if any
            $uploadedImages = [];
            $uploadPath = env('PRODUCT_PHOTO', 'uploads/product-photo');
            if (!file_exists(public_path($uploadPath))) {
                mkdir(public_path($uploadPath), 0777, true);
            }

            // Process uploaded image files (from separate file input)
            if ($request->hasFile('product_images')) {
                foreach ($request->file('product_images') as $imageFile) {
                    $filename = time() . '_' . uniqid() . '.' . $imageFile->extension();
                    $imageFile->move(public_path($uploadPath), $filename);
                    $uploadedImages[$imageFile->getClientOriginalName()] = $filename;
                }
            }

            // Process ZIP file of images if uploaded
            if ($request->hasFile('image_zip')) {
                $zipFile = $request->file('image_zip');
                $zip = new \ZipArchive();
                $tempDir = storage_path('app/temp_images_' . time());
                mkdir($tempDir, 0777, true);

                if ($zip->open($zipFile->getRealPath()) === TRUE) {
                    $zip->extractTo($tempDir);
                    $zip->close();

                    // Process extracted images
                    $iterator = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS)
                    );
                    foreach ($iterator as $fileItem) {
                        if ($fileItem->isFile() && in_array(strtolower($fileItem->getExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $originalName = $fileItem->getFilename();
                            $filename = time() . '_' . uniqid() . '.' . $fileItem->getExtension();
                            copy($fileItem->getRealPath(), public_path($uploadPath . '/' . $filename));
                            $uploadedImages[$originalName] = $filename;
                        }
                    }

                    // Cleanup temp directory
                    array_map('unlink', glob("$tempDir/*"));
                    rmdir($tempDir);
                }
            }

            // Helper function to get value from row by column name
            $getCol = function($row, $colMap, $name, $default = null) {
                $name = strtolower(trim($name));
                return isset($colMap[$name]) && isset($row[$colMap[$name]]) ? $row[$colMap[$name]] : $default;
            };

            foreach ($rows as $rowIndex => $row) {
                $pName = $getCol($row, $colMap, 'name');
                if (empty($pName)) continue; // Name is mandatory

                $productId = $getCol($row, $colMap, 'product id');
                $catName = $getCol($row, $colMap, 'category');
                $brand = $getCol($row, $colMap, 'brand');
                $genderVal = $getCol($row, $colMap, 'gender', 'Unisex');
                $gender = in_array($genderVal, ['Unisex', 'Boys', 'Girls']) ? $genderVal : 'Unisex';
                $shortDesc = $getCol($row, $colMap, 'short description');
                $fullDesc = $getCol($row, $colMap, 'full description');
                
                $isAllSchoolsVal = strtolower($getCol($row, $colMap, 'is all schools', ''));
                $isAllSchools = ($isAllSchoolsVal == 'yes' || $isAllSchoolsVal == '1') ? 1 : 0;
                
                $isAllClassesVal = strtolower($getCol($row, $colMap, 'is all classes', ''));
                $isAllClasses = ($isAllClassesVal == 'yes' || $isAllClassesVal == '1') ? 1 : 0;
                
                $isAllSubjectsVal = strtolower($getCol($row, $colMap, 'is all subjects', ''));
                $isAllSubjects = ($isAllSubjectsVal == 'yes' || $isAllSubjectsVal == '1') ? 1 : 0;

                // Schools, Classes, Subjects (comma-separated names)
                $schoolsStr = $getCol($row, $colMap, 'schools', '');
                $classesStr = $getCol($row, $colMap, 'classes', '');
                $subjectsStr = $getCol($row, $colMap, 'subjects', '');

                $variantName = $getCol($row, $colMap, 'variant name');
                $sku = $getCol($row, $colMap, 'sku');
                $stock = $getCol($row, $colMap, 'stock', 0);
                $unitPrice = $getCol($row, $colMap, 'unit price', 0);
                $discountPrice = $getCol($row, $colMap, 'discounted price');
                
                // GST fields
                $gstRateVal = $getCol($row, $colMap, 'gst rate', '');
                $gstRate = is_numeric($gstRateVal) ? floatval($gstRateVal) : 18;
                
                $gstTypeVal = strtolower($getCol($row, $colMap, 'gst type', ''));
                $gstType = in_array($gstTypeVal, ['inclusive', 'exclusive']) ? $gstTypeVal : 'inclusive';
                $cgstRate = $gstRate / 2;
                $sgstRate = $gstRate / 2;
                
                $statusVal = strtolower($getCol($row, $colMap, 'status', ''));
                $status = ($statusVal == 'inactive') ? 0 : 1;
                $imageFilenamesStr = $getCol($row, $colMap, 'image filenames', '');

                // 1. Find or Create Product
                $product = null;
                if ($productId) {
                    $product = Product::find($productId);
                } else {
                    $product = Product::where('p_name', $pName)->first();
                }

                $catId = null;
                if (!empty($catName)) {
                    $category = ProductCategory::where('cat_name', 'LIKE', $catName)->first();
                    if ($category) {
                        $catId = $category->cat_id;
                    }
                }

                $productData = [
                    'p_name' => $pName,
                    'p_cat_id' => $catId ?? 1,
                    'p_brand' => $brand,
                    'p_gender' => $gender,
                    'p_short_desc' => $shortDesc ?? '',
                    'p_full_desc' => $fullDesc,
                    'is_all_schools' => $isAllSchools,
                    'is_all_classes' => $isAllClasses,
                    'is_all_subjects' => $isAllSubjects,
                    'p_status' => $status,
                    'p_updated_at' => now(),
                ];

                if ($product) {
                    $product->update($productData);
                } else {
                    $productData['p_created_at'] = now();
                    $product = Product::create($productData);
                }

                // Sync Schools, Classes, Subjects relationships
                // Use case-insensitive matching to handle slight variations in names
                if (!$product->is_all_schools && !empty($schoolsStr)) {
                    $schoolNames = array_map('trim', explode(',', $schoolsStr));
                    $schoolIds = [];
                    foreach ($schoolNames as $sName) {
                        if (empty($sName)) continue;
                        $school = \App\Models\School::whereRaw('LOWER(TRIM(sch_name)) = LOWER(?)', [trim($sName)])->first();
                        if ($school) {
                            $schoolIds[] = $school->sch_id;
                        }
                    }
                    if (!empty($schoolIds)) {
                        $product->schools()->sync($schoolIds);
                    }
                } elseif ($product->is_all_schools) {
                    $product->schools()->detach();
                }

                if (!$product->is_all_classes && !empty($classesStr)) {
                    $classNames = array_map('trim', explode(',', $classesStr));
                    $classIds = [];
                    foreach ($classNames as $cName) {
                        if (empty($cName)) continue;
                        $class = \App\Models\Classes::whereRaw('LOWER(TRIM(class_name)) = LOWER(?)', [trim($cName)])->first();
                        if ($class) {
                            $classIds[] = $class->class_id;
                        }
                    }
                    if (!empty($classIds)) {
                        $product->classes()->sync($classIds);
                    }
                } elseif ($product->is_all_classes) {
                    $product->classes()->detach();
                }

                if (!$product->is_all_subjects && !empty($subjectsStr)) {
                    $subjectNames = array_map('trim', explode(',', $subjectsStr));
                    $subjectIds = [];
                    foreach ($subjectNames as $sName) {
                        if (empty($sName)) continue;
                        $subject = \App\Models\Subject::whereRaw('LOWER(TRIM(subject_name)) = LOWER(?)', [trim($sName)])->first();
                        if ($subject) {
                            $subjectIds[] = $subject->subject_id;
                        }
                    }
                    if (!empty($subjectIds)) {
                        $product->subjects()->sync($subjectIds);
                    }
                } elseif ($product->is_all_subjects) {
                    $product->subjects()->detach();
                }

                // 2. Handle Variant and Stock
                if (!empty($sku)) {
                    // Normalize variant name
                    $variantName = trim($variantName ?? 'Standard');
                    
                    // Find variant with case-insensitive and trimmed matching
                    $variant = ProductVariant::where('product_id', $product->p_id)
                        ->whereRaw('LOWER(TRIM(prod_variant)) = LOWER(?)', [$variantName])
                        ->first();

                    if (!$variant) {
                        $variant = ProductVariant::create([
                            'product_id' => $product->p_id,
                            'prod_variant' => $variantName ?: 'Standard',
                            'status' => 1
                        ]);
                    }

                    $inventory = ProductStockInventory::where('prod_sku', $sku)->first();
                    $inventoryData = [
                        'prod_id' => $product->p_id,
                        'prod_variant_id' => $variant->prod_variant_id,
                        'available_stock' => $stock,
                        'unit_price' => $unitPrice,
                        'discounted_unit_price' => $discountPrice,
                        'gst_rate' => $gstRate,
                        'gst_type' => $gstType,
                        'cgst_rate' => $cgstRate,
                        'sgst_rate' => $sgstRate,
                        'status' => 1
                    ];

                    if ($inventory) {
                        if ($inventory->prod_id == $product->p_id) {
                            $inventory->update($inventoryData);
                        } else {
                            $errorCount++;
                            continue;
                        }
                    } else {
                        $inventoryData['prod_sku'] = $sku;
                        ProductStockInventory::create($inventoryData);
                    }
                }

                // 3. Handle Product Images from Excel column R
                if (!empty($imageFilenamesStr)) {
                    $imageFilenames = array_map('trim', explode(',', $imageFilenamesStr));
                    $sortOrder = 0;

                    foreach ($imageFilenames as $imgFilename) {
                        if (empty($imgFilename)) continue;

                        // Check if image was uploaded
                        if (isset($uploadedImages[$imgFilename])) {
                            $storedFilename = $uploadedImages[$imgFilename];

                            // Check if image already exists for this product
                            $existingImage = ProductImage::where('product_id', $product->p_id)
                                ->where('img_path', $storedFilename)
                                ->first();

                            if (!$existingImage) {
                                ProductImage::create([
                                    'product_id' => $product->p_id,
                                    'img_path' => $storedFilename,
                                    'sort_order' => $sortOrder++
                                ]);
                            }
                        } else {
                            // Image not found in uploaded files
                            $imageErrors[] = "Row " . ($rowIndex + 2) . ": Image '$imgFilename' not found in uploaded files";
                        }
                    }

                    // Update main product photo (first image)
                    $firstImage = ProductImage::where('product_id', $product->p_id)
                        ->orderBy('sort_order')
                        ->orderBy('id')
                        ->first();

                    if ($firstImage) {
                        $product->update(['p_photo' => $firstImage->img_path]);
                    }
                }

                $successCount++;
            }

            DB::commit();

            $message = "Import completed. Items processed: $successCount, Errors/Skipped: $errorCount";
            if (!empty($imageErrors)) {
                $message .= "<br><br>Image warnings:<br>" . implode("<br>", array_slice($imageErrors, 0, 10));
                if (count($imageErrors) > 10) {
                    $message .= "<br>... and " . (count($imageErrors) - 10) . " more";
                }
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error during import: ' . $e->getMessage());
        }
    }
}
