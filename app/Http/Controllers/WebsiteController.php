<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\School;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\AppSlider;
use App\Models\ContactInfo;
use App\Models\Enquiry;
use App\Models\PreBooking;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductStockInventory;
use App\Models\CartItem;
use App\Models\Charge;
use App\Models\Bundle;
use App\Models\Blog;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;
use App\Mail\OrderStatusUpdateMail;
use App\Mail\PreBookingMail;
use App\Mail\ContactEnquiryMail;
use App\Helpers\NotificationHelper;
use App\Helpers\PayUHelper;
use App\Enums\OrderStatus;
use App\Enums\OrderPaymentMode;
use App\Services\CartService;
use App\Services\PricingService;


class WebsiteController extends Controller
{
    /**
     * Display the website home page.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $schools = School::where('sch_status', 1)->get();
        $classes = Classes::where('class_status', 1)->get();
        $categories = ProductCategory::where('cat_status', 1)->withCount('products')->get();
        $sliders = AppSlider::where('status', 1)->orderBy('id', 'desc')->get();

        // Fetch featured products or just recent ones for now
        $newArrivals = Product::where('p_status', 1)
            ->with(['category', 'schools', 'stockInventories'])
            ->orderBy('p_created_at', 'desc')
            ->take(8)
            ->get();

        return view('website.index', compact('schools', 'classes', 'categories', 'sliders', 'newArrivals'));
    }

    public function prebooking()
    {
        $schools = School::where('sch_status', 1)->get();
        $classes = Classes::where('class_status', 1)->get();
        return view('website.prebooking', compact('schools', 'classes'));
    }

    public function prebookingStore(Request $request)
    {
        $request->validate([
            'school_id' => 'required|exists:tbl_schools,sch_id',
            'class' => 'required|exists:tbl_classes,class_id', // input name is 'class' in view
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_mobile' => 'required|string|max:20',
        ]);

        try {
            $booking = PreBooking::create([
                'school_id' => $request->school_id,
                'class_id' => $request->class,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_mobile' => $request->customer_mobile,
                'token_amount' => 99.00,
                'payment_status' => 'pending', // In a real app, integrate payment gateway here
                'status' => 1,
            ]);

            // Send mail to customer
            if ($booking->customer_email) {
                Mail::to($booking->customer_email)
                    ->send(new PreBookingMail($booking, 'customer'));
            }

            // Send mail to admin
            Mail::to(env('ADMIN_EMAIL', config('mail.from.address')))
                ->send(new PreBookingMail($booking, 'admin'));

            // WhatsApp & SMS Notification to Customer
            $school = School::find($request->school_id);
            $class = Classes::find($request->class);
            NotificationHelper::notify('prebooking_confirmation', [
                'mobile' => $booking->customer_mobile,
                'name' => $booking->customer_name,
                'school_name' => $school->sch_name ?? 'School',
                'class' => $class->class_name ?? 'Class'
            ]);

            return redirect()->back()->with('success', 'Pre-booking request submitted successfully! Please proceed with payment (Mock).');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Display the website with summary data.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
        return view('website.about-us');
    }

    public function shop(Request $request)
    {
        $query = Product::where('p_status', 1)
            ->with(['category', 'schools', 'classes', 'stockInventories', 'variants']);

        if ($request->has('school') && $request->school != null) {
            $schoolId = $request->school;
            $query->where(function ($q) use ($schoolId) {
                $q->where('is_all_schools', 1)
                    ->orWhereHas('schools', function ($q2) use ($schoolId) {
                        $q2->where('tbl_schools.sch_id', $schoolId);
                    });
            });
        }

        if ($request->has('class') && $request->class != null) {
            $classId = $request->class;
            $query->where(function ($q) use ($classId) {
                $q->where('is_all_classes', 1)
                    ->orWhereHas('classes', function ($q2) use ($classId) {
                        $q2->where('tbl_classes.class_id', $classId);
                    })
                    // Products with no class link at all are treated as
                    // available for every class (prevents orphaned products
                    // from disappearing from filtered listings).
                    ->orWhereDoesntHave('classes');
            });
        }

        if ($request->has('subject') && $request->subject != null) {
            $subjectId = $request->subject;
            $query->where(function ($q) use ($subjectId) {
                $q->where('is_all_subjects', 1)
                    ->orWhereHas('subjects', function ($q2) use ($subjectId) {
                        $q2->where('tbl_subjects.subject_id', $subjectId);
                    });
            });
        }

        if ($request->has('category') && $request->category != null) {
            $query->where('p_cat_id', $request->category);
        }

        if ($request->has('gender') && $request->gender != null) {
            $query->where('p_gender', $request->gender);
        }

        if ($request->has('search') && $request->search != null) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('p_name', 'like', "%{$search}%")
                    ->orWhere('p_tag', 'like', "%{$search}%")
                    ->orWhere('p_short_desc', 'like', "%{$search}%")
                    ->orWhere('p_full_desc', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q2) use ($search) {
                        $q2->where('cat_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('sort_by') && in_array($request->sort_by, ['price_low_high', 'price_high_low'])) {
            $query->addSelect([
                'min_price' => ProductStockInventory::select('unit_price')
                    ->whereColumn('prod_id', 'tbl_products.p_id')
                    ->orderBy('unit_price', 'asc')
                    ->limit(1)
            ]);

            if ($request->sort_by == 'price_low_high') {
                $query->orderBy('min_price', 'asc');
            } elseif ($request->sort_by == 'price_high_low') {
                $query->orderBy('min_price', 'desc');
            }
        } else {
            $categoryOrder = env('CATEGORY_DISPLAY_ORDER');
            if ($categoryOrder) {
                $categories = array_reverse(explode(',', $categoryOrder));
                $query->leftJoin('tbl_product_categories', 'tbl_products.p_cat_id', '=', 'tbl_product_categories.cat_id')
                    ->select('tbl_products.*')
                    ->orderByRaw("FIELD(tbl_product_categories.cat_name, '" . implode("','", array_map('addslashes', $categories)) . "') DESC")
                    ->orderBy('tbl_products.p_created_at', 'desc');
            } else {
                $query->orderBy('p_created_at', 'desc');
            }
        }

        $products = $query->paginate(12);

        $schools = School::where('sch_status', 1)->get();
        $classes = Classes::where('class_status', 1)->get();
        $subjects = Subject::where('subject_status', 1)->get();
        $categories = ProductCategory::where('cat_status', 1)->get();

        return view('website.shop', compact('products', 'schools', 'classes', 'subjects', 'categories'));
    }

    public function Schoolshop(Request $request)
    {
        $query = Product::where('p_status', 1)
            ->with(['category', 'schools', 'classes', 'stockInventories', 'variants']);

        if ($request->has('school') && $request->school != null) {
            $schoolId = $request->school;
            $query->where(function ($q) use ($schoolId) {
                $q->where('is_all_schools', 1)
                    ->orWhereHas('schools', function ($q2) use ($schoolId) {
                        $q2->where('tbl_schools.sch_id', $schoolId);
                    });
            });
        }

        if ($request->has('class') && $request->class != null) {
            $classId = $request->class;
            $query->where(function ($q) use ($classId) {
                $q->where('is_all_classes', 1)
                    ->orWhereHas('classes', function ($q2) use ($classId) {
                        $q2->where('tbl_classes.class_id', $classId);
                    })
                    // Products with no class link at all are treated as
                    // available for every class (prevents orphaned products
                    // from disappearing from filtered listings).
                    ->orWhereDoesntHave('classes');
            });
        }

        if ($request->has('subject') && $request->subject != null) {
            $subjectId = $request->subject;
            $query->where(function ($q) use ($subjectId) {
                $q->where('is_all_subjects', 1)
                    ->orWhereHas('subjects', function ($q2) use ($subjectId) {
                        $q2->where('tbl_subjects.subject_id', $subjectId);
                    });
            });
        }

        if ($request->has('category') && $request->category != null) {
            $query->where('p_cat_id', $request->category);
        }

        if ($request->has('gender') && $request->gender != null) {
            $query->where('p_gender', $request->gender);
        }

        if ($request->has('search') && $request->search != null) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('p_name', 'like', "%{$search}%")
                    ->orWhere('p_tag', 'like', "%{$search}%")
                    ->orWhere('p_short_desc', 'like', "%{$search}%")
                    ->orWhere('p_full_desc', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q2) use ($search) {
                        $q2->where('cat_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->has('sort_by') && in_array($request->sort_by, ['price_low_high', 'price_high_low'])) {
            $query->addSelect([
                'min_price' => ProductStockInventory::select('unit_price')
                    ->whereColumn('prod_id', 'tbl_products.p_id')
                    ->orderBy('unit_price', 'asc')
                    ->limit(1)
            ]);

            if ($request->sort_by == 'price_low_high') {
                $query->orderBy('min_price', 'asc');
            } elseif ($request->sort_by == 'price_high_low') {
                $query->orderBy('min_price', 'desc');
            }
        } else {
            $categoryOrder = env('CATEGORY_DISPLAY_ORDER');
            if ($categoryOrder) {
                $categories = array_reverse(explode(',', $categoryOrder));
                $query->leftJoin('tbl_product_categories', 'tbl_products.p_cat_id', '=', 'tbl_product_categories.cat_id')
                    ->select('tbl_products.*')
                    ->orderByRaw("FIELD(tbl_product_categories.cat_name, '" . implode("','", array_map('addslashes', $categories)) . "') DESC")
                    ->orderBy('tbl_products.p_created_at', 'desc');
            } else {
                $query->orderBy('p_created_at', 'desc');
            }
        }

        $products = $query->paginate(12);

        // Bundles Query
        $bundlesQuery = Bundle::where('b_status', 1)->with(['products.stockInventories', 'products.images', 'products.category', 'products.variants']);

        if ($request->has('school') && $request->school != null) {
            $schoolId = $request->school;
            $bundlesQuery->where(function ($q) use ($schoolId) {
                $q->where('is_all_schools', 1)
                    ->orWhereHas('schools', function ($q2) use ($schoolId) {
                        $q2->where('tbl_schools.sch_id', $schoolId);
                    });
            });
        }

        if ($request->has('class') && $request->class != null) {
            $classId = $request->class;
            $bundlesQuery->where(function ($q) use ($classId) {
                $q->where('is_all_classes', 1)
                    ->orWhereHas('classes', function ($q2) use ($classId) {
                        $q2->where('tbl_classes.class_id', $classId);
                    });
            });
        }

        if ($request->has('subject') && $request->subject != null) {
            $subjectId = $request->subject;
            $bundlesQuery->where(function ($q) use ($subjectId) {
                $q->where('is_all_subjects', 1)
                    ->orWhereHas('subjects', function ($q2) use ($subjectId) {
                        $q2->where('tbl_subjects.subject_id', $subjectId);
                    });
            });
        }

        if ($request->has('category') && $request->category != null) {
            $catId = $request->category;
            $bundlesQuery->whereHas('products', function ($q) use ($catId) {
                $q->where('p_cat_id', $catId);
            });
        }

        if ($request->has('search') && $request->search != null) {
            $search = $request->search;
            $bundlesQuery->where(function ($q) use ($search) {
                $q->where('b_name', 'like', "%{$search}%")
                    ->orWhereHas('products', function ($q2) use ($search) {
                        $q2->where('p_name', 'like', "%{$search}%");
                    });
            });
        }

        $bundles = $bundlesQuery->get();

        $schools = School::where('sch_status', 1)->get();
        $subjects = Subject::where('subject_status', 1)->get();

        if ($request->has('school') && $request->school != null) {
            $schoolId = $request->school;

            $productsForSchool = Product::where('p_status', 1)
                ->where(function ($q) use ($schoolId) {
                    $q->where('is_all_schools', 1)
                        ->orWhereHas('schools', function ($q2) use ($schoolId) {
                            $q2->where('tbl_schools.sch_id', $schoolId);
                        });
                })
                ->select('p_id', 'is_all_classes', 'p_cat_id')
                ->get();

            // If any product for this school serves ALL classes, or none of
            // the school's products have explicit class links, every active
            // class is a valid option.
            $linkedClassCount = DB::table('tbl_product_classes')
                ->whereIn('product_id', $productsForSchool->pluck('p_id'))
                ->count();

            $servesAllClasses = $productsForSchool->contains('is_all_classes', 1)
                || $linkedClassCount === 0;

            $productClassIds = $servesAllClasses
                ? Classes::where('class_status', 1)->pluck('class_id')
                : DB::table('tbl_product_classes')
                    ->whereIn('product_id', $productsForSchool->pluck('p_id'))
                    ->pluck('class_id');

            $bundleClassIds = DB::table('tbl_bundle_classes')
                ->whereIn('bundle_id', $bundles->pluck('b_id'))
                ->pluck('class_id');

            $finalClassIds = $productClassIds->merge($bundleClassIds)->unique()->values();
            $classes = Classes::where('class_status', 1)
                ->whereIn('class_id', $finalClassIds)
                ->get();

            $productCatIds = $productsForSchool->pluck('p_cat_id')->filter()->unique();
            $bundleCatIds = $bundles->flatMap(function ($b) {
                return $b->products->pluck('p_cat_id');
            })->filter()->unique();
            $finalCatIds = $productCatIds->merge($bundleCatIds)->unique()->values();
            $categories = ProductCategory::where('cat_status', 1)
                ->whereIn('cat_id', $finalCatIds)
                ->get();
        } else {
            $classes = Classes::where('class_status', 1)->get();
            $categories = ProductCategory::where('cat_status', 1)->get();
        }

        return view('website.school-shop', compact('products', 'bundles', 'schools', 'classes', 'subjects', 'categories'));
    }

    public function schools()
    {
        $schools = School::where('sch_status', 1)->paginate(12);
        return view('website.schools', compact('schools'));
    }

    public function bundles(Request $request)
    {
        $bundlesQuery = Bundle::where('b_status', 1)
            ->with(['products.stockInventories', 'products.images', 'products.category', 'products.variants']);

        // Apply filters if provided
        if ($request->has('school') && $request->school != null) {
            $schoolId = $request->school;
            $bundlesQuery->where(function ($q) use ($schoolId) {
                $q->where('is_all_schools', 1)
                    ->orWhereHas('schools', function ($q2) use ($schoolId) {
                        $q2->where('tbl_schools.sch_id', $schoolId);
                    });
            });
        }

        if ($request->has('class') && $request->class != null) {
            $classId = $request->class;
            $bundlesQuery->where(function ($q) use ($classId) {
                $q->where('is_all_classes', 1)
                    ->orWhereHas('classes', function ($q2) use ($classId) {
                        $q2->where('tbl_classes.class_id', $classId);
                    });
            });
        }

        if ($request->has('subject') && $request->subject != null) {
            $subjectId = $request->subject;
            $bundlesQuery->where(function ($q) use ($subjectId) {
                $q->where('is_all_subjects', 1)
                    ->orWhereHas('subjects', function ($q2) use ($subjectId) {
                        $q2->where('tbl_subjects.subject_id', $subjectId);
                    });
            });
        }

        if ($request->has('search') && $request->search != null) {
            $search = $request->search;
            $bundlesQuery->where(function ($q) use ($search) {
                $q->where('b_name', 'like', "%{$search}%")
                    ->orWhere('b_short_desc', 'like', "%{$search}%")
                    ->orWhereHas('products', function ($q2) use ($search) {
                        $q2->where('p_name', 'like', "%{$search}%");
                    });
            });
        }

        $bundles = $bundlesQuery->orderBy('b_created_at', 'desc')->paginate(12);

        // Get filter options
        $schools = School::where('sch_status', 1)->get();
        $classes = Classes::where('class_status', 1)->get();
        $subjects = Subject::where('subject_status', 1)->get();

        return view('website.bundles', compact('bundles', 'schools', 'classes', 'subjects'));
    }

    public function bundleDetails($id)
    {
        $bundle = Bundle::where('b_id', $id)
            ->where('b_status', 1)
            ->with(['products.stockInventories', 'products.images', 'products.category', 'products.variants', 'schools', 'classes', 'subjects'])
            ->firstOrFail();

        // Get related bundles (same school/class/subject)
        $relatedBundles = Bundle::where('b_status', 1)
            ->where('b_id', '!=', $id)
            ->where(function ($q) use ($bundle) {
                if ($bundle->schools->count() > 0) {
                    $q->orWhereHas('schools', function ($sq) use ($bundle) {
                        $sq->whereIn('tbl_schools.sch_id', $bundle->schools->pluck('sch_id'));
                    });
                }
                if ($bundle->classes->count() > 0) {
                    $q->orWhereHas('classes', function ($sq) use ($bundle) {
                        $sq->whereIn('tbl_classes.class_id', $bundle->classes->pluck('class_id'));
                    });
                }
            })
            ->with(['products.stockInventories', 'products.category'])
            ->take(4)
            ->get();

        return view('website.bundle-details', compact('bundle', 'relatedBundles'));
    }

    public function bulkAddToCart(Request $request)
    {
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login to add items to cart', 'login_required' => true], 401);
            }
            return redirect()->back()->with('error', 'Please login to add items to cart')->with('open_login', true);
        }

        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:tbl_products,p_id',
        ]);

        $productIds = $request->input('product_ids');
        $variants = $request->input('variants', []);
        $quantities = $request->input('quantities', []);
        $custId = Auth::id();
        $count = 0;
        $errors = [];

        foreach ($productIds as $id) {
            $variantId = $variants[$id] ?? null;
            $requestedQty = (int) ($quantities[$id] ?? 1);
            if ($requestedQty < 1) {
                $requestedQty = 1;
            }

            // Check Stock
            $stock = CartService::stockFor($id, $variantId);
            $availableStock = $stock ? $stock->available_stock : 0;

            if ($availableStock <= 0) {
                // Skip if out of stock
                continue;
            }

            $cartItem = CartItem::where('cust_id', $custId)
                ->where('product_id', $id)
                ->where('variant_id', $variantId)
                ->first();

            $currentQty = $cartItem ? $cartItem->quantity : 0;
            $newQty = $currentQty + $requestedQty;

            if ($newQty > $availableStock) {
                // Skip if limit reached
                continue;
            }

            if ($cartItem) {
                $cartItem->quantity = $newQty;
                $cartItem->save();
            } else {
                CartItem::create([
                    'cust_id' => $custId,
                    'product_id' => $id,
                    'variant_id' => $variantId,
                    'quantity' => $requestedQty
                ]);
            }
            $count++;
        }

        if ($request->ajax()) {
            $cartCount = \App\Models\CartItem::where('cust_id', $custId)->count();
            if ($count > 0) {
                return response()->json(['success' => true, 'message' => "$count products added to cart successfully!", 'added_count' => $count, 'cart_count' => $cartCount]);
            } else {
                return response()->json(['success' => false, 'message' => "No products were added. They might be out of stock."], 400);
            }
        }
        if ($count > 0) {
            return redirect()->route('website.cart')->with('success', "$count products added to cart successfully!");
        } else {
            return redirect()->back()->with('warning', "No products were added. They might be out of stock.");
        }
    }

    public function productDetails($id)
    {
        $product = Product::with(['category', 'schools', 'classes', 'subjects', 'images', 'variants', 'stockInventories'])->findOrFail($id);

        // Fetch related products (Same School, Class, Category, Gender)
        $schoolIds = $product->schools->pluck('sch_id')->toArray();
        $classIds = $product->classes->pluck('class_id')->toArray();

        $relatedProducts = Product::where('p_cat_id', $product->p_cat_id)
            ->where('p_id', '!=', $id)
            ->where('p_status', 1)
            ->with(['category', 'stockInventories', 'variants'])
            ->inRandomOrder()
            ->take(15)
            ->get();

        return view('website.product-details', compact('product', 'relatedProducts'));
    }

    public function addToCart(Request $request, $id)
    {
        if (!Auth::check()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Please login to add items to cart', 'login_required' => true], 401);
            }
            return redirect()->back()->with('error', 'Please login to add items to cart')->with('open_login', true);
        }

        $product = Product::with('variants')->findOrFail($id);

        $variantId = $request->input('variant_id');
        $quantity = (int) $request->input('quantity', 1);
        $customizationOption = $request->input('customization_option');
        $customizationText = $request->input('customization_text');

        // Customization surcharge mapping
        $customizationPrices = [
            'normal' => 0,
            'basic' => 999,
            'premium' => 1199,
            'platinum' => 1499,
        ];
        $customizationPrice = isset($customizationPrices[$customizationOption]) ? $customizationPrices[$customizationOption] : 0;

        // If customization selected, ensure text present
        if ($customizationOption && $customizationOption !== 'normal') {
            if (!$customizationText || trim($customizationText) === '') {
                return redirect()->back()->with('error', 'Please enter text for customization.');
            }
        }

        if ($quantity < 1)
            $quantity = 1;

        // Validate variant
        if ($variantId) {
            $variant = $product->variants->where('prod_variant_id', $variantId)->first();
            if (!$variant) {
                return redirect()->back()->with('error', 'Invalid variant selected.');
            }
        } elseif ($product->variants->count() > 0) {
            return redirect()->back()->with('error', 'Please select a variant.');
        }

        // Check Stock
        $stock = CartService::stockFor($id, $variantId);
        $availableStock = $stock ? $stock->available_stock : 0;

        if ($availableStock <= 0) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'This product is out of stock.'], 400);
            }
            return redirect()->back()->with('error', 'This product is out of stock.');
        }

        $custId = Auth::id();

        $cartItem = CartItem::where('cust_id', $custId)
            ->where('product_id', $id)
            ->where('variant_id', $variantId)
            ->first();

        $currentQty = $cartItem ? $cartItem->quantity : 0;
        $cap = min($availableStock, 5);
        $totalQty = $currentQty + $quantity;

        if ($totalQty > $cap) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You can purchase a maximum of 5 units for this product.'], 400);
            }
            return redirect()->back()->with('error', "You can purchase a maximum of 5 units per product. Available stock: $availableStock. You already have $currentQty in cart.");
        }

        if ($cartItem) {
            $cartItem->quantity = $totalQty;
            $cartItem->save();
        } else {
            CartItem::create([
                'cust_id' => $custId,
                'product_id' => $id,
                'variant_id' => $variantId,
                'quantity' => min($quantity, 5)
            ]);
        }

        // Store customization meta in session keyed by product+variant
        if ($customizationOption) {
            $metaKey = $id . '|' . ($variantId ?: 'none');
            $meta = session('cart_customizations', []);
            $meta[$metaKey] = [
                'option' => $customizationOption,
                'text' => $customizationText,
                'price' => $customizationPrice,
            ];
            session(['cart_customizations' => $meta]);
        }

        if ($request->ajax()) {
            $count = \App\Models\CartItem::where('cust_id', $custId)->count();
            return response()->json(['success' => true, 'message' => 'Product added to cart', 'cart_count' => $count]);
        }

        if ($request->has('buy_now')) {
            return redirect()->route('website.cart');
        }

        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }

    public function updateCart(Request $request)
    {
        if ($request->id && $request->quantity) {
            if (!Auth::check()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized']);
            }

            $cartItem = CartItem::where('id', $request->id)
                ->where('cust_id', Auth::id())
                ->first();

            if ($cartItem) {
                $stock = CartService::stockFor($cartItem->product_id, $cartItem->variant_id);
                $maxStock = $stock ? (int) $stock->available_stock : 0;
                $cap = $maxStock > 0 ? min($maxStock, 5) : 5;

                if ($request->quantity > $cap) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Requested quantity exceeds limit (' . $cap . ').'
                    ]);
                }

                $cartItem->quantity = (int) $request->quantity;
                $cartItem->save();

                $cartData = CartService::build(Auth::id());
                $changedLine = $cartData['items']->firstWhere('id', $cartItem->id);

                return response()->json([
                    'success' => true,
                    'total' => $cartData['subtotal_inclusive'],
                    'row_subtotal' => $changedLine ? $changedLine->line_total : 0,
                    'grand_total' => $cartData['grand_total'],
                    'charges' => $cartData['charges']->map(function ($c) {
                        return [
                            'charge_id' => $c->charge_id,
                            'amount' => $c->total_amount,
                        ];
                    })->values()->all(),
                ]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Invalid request']);
    }

    public function removeFromCart(Request $request)
    {
        if ($request->id) {
            if (!Auth::check()) {
                if ($request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
                }
                return redirect()->route('login')->with('error', 'Please login to continue.');
            }

            $cartItem = CartItem::where('id', $request->id)
                ->where('cust_id', Auth::id())
                ->first();

            if ($cartItem) {
                $cartItem->delete();
                if ($request->ajax()) {
                    $cartData = CartService::build(Auth::id());
                    $cartCount = CartItem::where('cust_id', Auth::id())->count();
                    return response()->json([
                        'success' => true,
                        'total' => $cartData['subtotal_inclusive'],
                        'grand_total' => $cartData['grand_total'],
                        'charges' => $cartData['charges']->map(function ($c) {
                            return [
                                'charge_id' => $c->charge_id,
                                'amount' => $c->total_amount,
                            ];
                        })->values()->all(),
                        'cart_count' => $cartCount,
                    ]);
                }
                return redirect()->back()->with('success', 'Item removed from cart successfully.');
            }
        }
        return redirect()->back()->with('error', 'Item not found in cart.');
    }

    public function cart()
    {
        if (!Auth::check()) {
            return redirect()->route('website.index')->with('open_login', true);
        }

        $cartData = CartService::build(Auth::id());

        return view('website.cart', [
            'cartItems' => $cartData['items'],
            'subTotal' => $cartData['subtotal_inclusive'],
            'calculatedCharges' => $cartData['charges'],
            'grandTotal' => $cartData['grand_total'],
        ]);
    }

    public function checkout()
    {
        if (!Auth::check()) {
            return redirect()->route('website.index')->with('open_login', true);
        }

        $cartData = CartService::build(Auth::id());

        return view('website.checkout', [
            'cartItems' => $cartData['items'],
            'subTotal' => $cartData['subtotal_inclusive'],
            'calculatedCharges' => $cartData['charges'],
            'grandTotal' => $cartData['grand_total'],
        ]);
    }

    public function placeOrder(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to place an order.');
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|regex:/^[6-9][0-9]{9}$/|digits:10',
            'address' => 'required|string|min:10|max:500',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'pincode' => 'required|regex:/^[1-9][0-9]{5}$/|digits:6',
            'gst_number' => 'nullable|string|max:15|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/',
            'payment_method' => 'required|in:cod,online',
        ], [
            'phone.regex' => 'Invalid mobile number',
            'phone.digits' => 'Invalid mobile number',
            'phone.required' => 'Mobile number is required',
            'pincode.regex' => 'Invalid pincode',
            'pincode.digits' => 'Invalid pincode',
            'pincode.required' => 'Pincode is required',
            'address.min' => 'Address is too short',
            'address.required' => 'Address is required',
            'first_name.required' => 'First name is required',
            'last_name.required' => 'Last name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Invalid email address',
            'city.required' => 'City is required',
            'state.required' => 'State is required',
            'payment_method.required' => 'Please select payment method',
        ]);

        $cartItems = CartItem::where('cust_id', Auth::id())
            ->with(['product.category', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        try {
            DB::beginTransaction();

            $order = new Order();
            // Link order to the logged-in customer
            $order->order_placed_cust_id = Auth::id();

            $deliveryDetails = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'gst_number' => $request->gst_number,
            ];
            $order->order_delivery_details = $deliveryDetails;
            $order->order_gst_number = $request->gst_number;

            // Persist billing/contact details on the customer profile so the
            // address survives across orders and is visible in "My Account".
            $customer = Auth::user();
            $customer->cust_name = trim($request->first_name . ' ' . $request->last_name);
            $customer->cust_mobile = $request->phone;
            $customer->cust_email = $request->email;
            $customer->cust_address = $request->address . ($request->apartment ? ', ' . $request->apartment : '');
            $customer->cust_city = $request->city;
            $customer->cust_state = $request->state;
            $customer->cust_country = 'India';
            $customer->cust_pincode = $request->pincode;
            $customer->save();

            // Authoritative server-side pricing (never trust the client).
            $lines = CartService::pricedLines(Auth::id());

            $orderItemsData = [];
            $totalCGST = 0;
            $totalSGST = 0;
            $totalGST = 0;

            foreach ($lines as $item) {
                // Re-verify stock availability at order time.
                $stock = CartService::stockFor($item->product_id, $item->variant_id);
                $availableStock = $stock ? (int) $stock->available_stock : 0;

                if ($item->quantity > $availableStock) {
                    DB::rollback();
                    return redirect()->back()->with('error', "Insufficient stock for \"{$item->product->p_name}\". Available: {$availableStock}.");
                }

                $pricing = PricingService::itemPricing(
                    $item->display_unit,
                    $item->gst_rate,
                    $item->gst_type,
                    $item->quantity
                );

                $totalCGST += $pricing['line_cgst'];
                $totalSGST += $pricing['line_sgst'];
                $totalGST += $pricing['line_gst'];

                $orderItemsData[] = [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->p_name,
                    'product_image' => $item->product->p_photo ? asset('uploads/product-photo/' . $item->product->p_photo) : null,
                    'product_category_id' => $item->product->p_cat_id ?? 0,
                    'product_category_name' => $item->product->category->cat_name ?? null,
                    'product_tag' => $item->product->p_tag ?? null,
                    'variant_id' => $item->variant_id,
                    'variant_name' => $item->variant ? $item->variant->prod_variant : null,
                    'product_qty' => $item->quantity,
                    'product_rate' => $pricing['base_unit_price'], // base price without GST
                    'price' => $pricing['unit_price_incl_gst'], // GST-inclusive unit price
                    'total_price' => $pricing['line_total_incl_gst'], // GST-inclusive line total
                    'gst_rate' => $pricing['gst_rate'],
                    'gst_type' => $pricing['gst_type'],
                    'cgst_rate' => $pricing['cgst_rate'],
                    'cgst_amount' => $pricing['line_cgst'],
                    'sgst_rate' => $pricing['sgst_rate'],
                    'sgst_amount' => $pricing['line_sgst'],
                    'gst_amount' => $pricing['line_gst'],
                    'customization_option' => $item->customization_option,
                    'customization_text' => $item->customization_text,
                    'customization_price' => $item->surcharge,
                ];
            }

            $subtotalBase = round($lines->sum('line_base'), 2);
            $subtotalInclusive = round($lines->sum('line_total'), 2);

            // Calculate charges (delivery / processing) with their own GST.
            $charges = Charge::where('charge_status', 1)->get();
            $calculatedCharges = [];
            $totalChargesInclGst = 0;
            $totalChargeGst = 0;

            foreach ($charges as $charge) {
                $waived = PricingService::isChargeWaived($charge, $subtotalInclusive);
                $base = $waived ? 0.0 : PricingService::chargeBaseAmount($charge, $subtotalBase, $subtotalInclusive);

                $pricing = PricingService::chargePricing(
                    $base,
                    $charge->gst_rate,
                    $charge->gst_type,
                    $charge->cgst_rate,
                    $charge->sgst_rate
                );

                $totalChargesInclGst += $pricing['total_amount_incl_gst'];
                $totalChargeGst += $pricing['gst_amount'];

                $calculatedCharges[] = [
                    'charge_id' => $charge->charge_id,
                    'charge_name' => $charge->charge_name,
                    'charge_value' => $charge->charge_value,
                    'charge_type' => $charge->charge_type,
                    'gst_rate' => $pricing['gst_rate'],
                    'gst_type' => $pricing['gst_type'],
                    'cgst_rate' => $pricing['cgst_rate'],
                    'sgst_rate' => $pricing['sgst_rate'],
                    'calculated_amount' => $pricing['total_amount_incl_gst'], // GST-inclusive
                    'calculated_amount_before_gst' => $pricing['base_amount'],
                    'cgst_amount' => $pricing['cgst_amount'],
                    'sgst_amount' => $pricing['sgst_amount'],
                    'gst_amount' => $pricing['gst_amount'],
                    'total_amount' => $pricing['total_amount_incl_gst'],
                    'applied' => !$waived,
                ];
            }

            // Add CGST and SGST for invoice display (product taxes only).
            if ($totalCGST > 0) {
                $calculatedCharges[] = [
                    'charge_id' => 'cgst',
                    'charge_name' => 'CGST',
                    'charge_value' => '',
                    'charge_type' => 'tax',
                    'calculated_amount' => round($totalCGST, 2),
                    'calculated_amount_before_gst' => round($totalCGST, 2),
                    'cgst_amount' => round($totalCGST, 2),
                    'sgst_amount' => 0,
                    'gst_amount' => round($totalCGST, 2),
                ];
            }
            if ($totalSGST > 0) {
                $calculatedCharges[] = [
                    'charge_id' => 'sgst',
                    'charge_name' => 'SGST',
                    'charge_value' => '',
                    'charge_type' => 'tax',
                    'calculated_amount' => round($totalSGST, 2),
                    'calculated_amount_before_gst' => round($totalSGST, 2),
                    'cgst_amount' => 0,
                    'sgst_amount' => round($totalSGST, 2),
                    'gst_amount' => round($totalSGST, 2),
                ];
            }

            $grandTotal = round($subtotalInclusive + $totalChargesInclGst, 2);

            $order->order_items = $orderItemsData;
            $order->order_items_qty = $lines->sum('quantity');
            $order->order_charges = $calculatedCharges;
            // Product GST + charge GST (matches grand total exactly).
            $order->order_gst_amount = round($totalGST + $totalChargeGst, 2);
            $order->order_total_amt = $grandTotal;
            $order->order_paid_amt = 0;
            $order->order_due_amt = $grandTotal;
            $order->order_date_time = now();
            $order->order_created_at = now();
            $order->order_updated_at = now();

            $paymentMethod = $request->input('payment_method');
            $order->order_payment_mode = $paymentMethod === 'cod' ? \App\Enums\OrderPaymentMode::COD : \App\Enums\OrderPaymentMode::ONLINE;
            $order->order_payment_status = 2; // Pending
            $order->order_status = \App\Enums\OrderStatus::PENDING;

            $order->save();

            // Also save to order_items table for legacy/reporting
            foreach ($lines as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->order_id;
                $orderItem->product_id = $item->product_id;
                $orderItem->variant_id = $item->variant_id;
                $orderItem->product_name = $item->product->p_name;
                $orderItem->variant_name = $item->variant ? $item->variant->prod_variant : null;
                $orderItem->quantity = $item->quantity;
                $orderItem->price = $item->price;
                $orderItem->total_price = $item->line_total;
                $orderItem->save();
            }

            if ($paymentMethod === 'online') {
                $payuHelper = new PayUHelper();
                
                $txnid = 'ORD_' . $order->order_id . '_' . time();
                $order->order_payment_id = $txnid;
                $order->save();

                $params = [
                    'txnid' => $txnid,
                    'amount' => number_format((float) $order->order_total_amt, 2, '.', ''),
                    'productinfo' => 'Order for Studynest #' . $order->order_id,
                    'firstname' => $request->first_name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'surl' => route('website.payu.callback'),
                    'furl' => route('website.payu.callback'),
                    'udf1' => (string) $order->order_id,
                ];

                $hash = $payuHelper->generateHash($params);
                $params['key'] = $payuHelper->getMerchantKey();
                $params['hash'] = $hash;
                $params['service_provider'] = 'payu_paisa';
                $params['lastname'] = $request->last_name;
                $params['city'] = $request->city;
                $params['state'] = $request->state;
                $params['zipcode'] = $request->pincode;

                DB::commit();

                return view('website.payu.redirect', [
                    'payment_url' => $payuHelper->getPaymentUrl(),
                    'parameters' => $params
                ]);
            }

            // This part is for COD - clear cart, decrement stock immediately
            CartItem::where('cust_id', Auth::id())->delete();
            session()->forget('cart_customizations');
            
            $order->order_status = \App\Enums\OrderStatus::CONFIRMED;
            $order->save();

            // Decrement stock for COD orders
            $this->decrementStock($order);

            DB::commit();

            $this->sendOrderNotifications($order);

            return redirect()->route('website.thank-you', ['order_id' => $order->order_id])->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Place Order Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Something went wrong while placing your order. Please try again.');
        }
    }

    public function thankYou(Request $request)
    {
        $order = null;
        if ($request->has('order_id')) {
            $order = Order::find($request->order_id);
        }
        return view('website.thank-you', compact('order'));
    }

    public function contact()
    {
        $contactInfo = ContactInfo::first();
        return view('website.contact-us', compact('contactInfo'));
    }

    public function contactStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            $enquiryData = [
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'subject' => $request->subject,
                'message' => $request->message,
            ];

            Enquiry::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                // 'subject' => $request->subject, // Enquiry model/table doesn't have subject
                'message' => "Subject: " . $request->subject . "\n\n" . $request->message,
                'status' => 1,
            ]);

            // Send mail to admin
            Mail::to('colloborate@studynested.com')->send(new ContactEnquiryMail($enquiryData));

            return redirect()->back()->with('success', 'Your message has been sent successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function termsConditions()
    {
        return view('website.terms-conditions');
    }

    public function privacyPolicy()
    {
        return view('website.privacy-policy');
    }

    public function refundPolicy()
    {
        return view('website.refund-policy');
    }

    public function cancellationPolicy()
    {
        return view('website.cancellation-policy');
    }

    public function disclaimer()
    {
        return view('website.disclaimer');
    }

    public function myOrders()
    {
        if (!Auth::check()) {
            return redirect()->route('website.index')->with('open_login', true);
        }

        $orders = Order::where('order_placed_cust_id', Auth::id())
            ->orderBy('order_id', 'desc')
            ->get();

        return view('website.my-orders.index', compact('orders'));
    }

    public function myOrderDetails($id)
    {
        if (!Auth::check()) {
            return redirect()->route('website.index')->with('open_login', true);
        }

        $order = Order::where('order_id', $id)
            ->where('order_placed_cust_id', Auth::id())
            ->firstOrFail();

        return view('website.my-orders.show', compact('order'));
    }

    public function trackOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer'
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to track your order.'
            ], 401);
        }

        $order = Order::where('order_id', $request->order_id)
            ->where('order_placed_cust_id', Auth::id())
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found or you do not have permission to view this order.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->order_id,
                'order_status' => \App\Enums\OrderStatus::label($order->order_status),
                'payment_status' => $order->order_payment_status == 1 ? 'Paid' : ($order->order_payment_status == 2 ? 'Pending' : 'Failed'),
                'order_date' => $order->order_date_time ? date('d M Y, h:i A', strtotime($order->order_date_time)) : null,
                'updated_at' => $order->order_updated_at ? date('d M Y, h:i A', strtotime($order->order_updated_at)) : null,
                'items_qty' => $order->order_items_qty,
            ]
        ]);
    }

    public function searchSuggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['success' => true, 'data' => []]);
        }
        $categoryId = $request->query('category');

        $query = Product::where('p_status', 1)
            ->where(function ($inner) use ($q) {
                $inner->where('p_name', 'like', '%' . $q . '%')
                    ->orWhere('p_tag', 'like', '%' . $q . '%')
                    ->orWhere('p_short_desc', 'like', '%' . $q . '%')
                    ->orWhereHas('category', function ($q3) use ($q) {
                        $q3->where('cat_name', 'like', '%' . $q . '%');
                    });
            });
        if (!empty($categoryId)) {
            $query->where('p_cat_id', $categoryId);
        }

        $items = $query->orderBy('p_name')
            ->limit(5)
            ->get(['p_id', 'p_name']);

        $data = $items->map(function ($p) {
            return [
                'id' => $p->p_id,
                'name' => $p->p_name,
                'url' => route('website.product-details', $p->p_id),
            ];
        })->values()->all();

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function cancelOrder($id)
    {
        if (!Auth::check()) {
            return redirect()->route('website.index')->with('open_login', true);
        }

        $order = Order::where('order_id', $id)
            ->where('order_placed_cust_id', Auth::id())
            ->firstOrFail();

        // Allow cancellation for Pending and Confirmed orders
        if (in_array($order->order_status, [\App\Enums\OrderStatus::PENDING, \App\Enums\OrderStatus::CONFIRMED])) {
            $order->order_status = \App\Enums\OrderStatus::CANCELLED;
            $order->save();

            // Restore stock for cancelled orders
            $this->restoreStock($order);

            $statusText = "Order Cancelled";

            $delivery = is_array($order->order_delivery_details)
                ? $order->order_delivery_details
                : json_decode($order->order_delivery_details, true);

            // Mail::to($delivery['email'] ?? null)
            //     ->send(new OrderStatusUpdateMail(
            //         $order,
            //         $statusText,
            //         now()->format('d M Y h:i A')
            //     ));

            // WhatsApp & SMS Notification to Customer
            if ($delivery['phone'] ?? null) {
                NotificationHelper::notify('order_status_update', [
                    'mobile' => $delivery['phone'],
                    'name' => ($delivery['first_name'] ?? 'Customer') . ' ' . ($delivery['last_name'] ?? ''),
                    'order_id' => $order->order_id,
                    'status' => $statusText
                ]);
                
                Log::error('Entered Notification Call' . $delivery['phone']);
            }
            
            Log::error('Exited Notification Call' . $delivery['phone']);


            return redirect()->back()->with('success', 'Order cancelled successfully.');
        }

        return redirect()->back()->with('error', 'Order cannot be cancelled at this stage.');
    }

    public function deleteAccountRequest()
    {
        return view('website.delete-account-request');
    }

    /**
     * PayU Payment Callback
     */
    public function payuCallback(Request $request)
    {
        $payload = $request->all();
        
        if (empty($payload['txnid'])) {
            return redirect()->route('website.index')->with('error', 'Invalid payment response.');
        }

        $orderId = $payload['udf1'] ?? null;
        $order = Order::find($orderId);
        
        if (!$order) {
            return redirect()->route('website.index')->with('error', 'Order not found.');
        }

        $payuHelper = new PayUHelper();
        if ($payuHelper->verifyHash($payload)) {
            if ($payload['status'] === 'success') {
                // Check if already processed
                if ($order->order_payment_status != 1) {
                    $order->order_payment_status = 1; // Paid
                    $order->order_status = \App\Enums\OrderStatus::CONFIRMED;
                    $order->order_payment_date_time = now();
                    $order->order_paid_amt = $order->order_total_amt;
                    $order->order_due_amt = 0;
                    $order->order_updated_at = now();
                    $order->save();

                    // Decrement stock for online payment orders
                    $this->decrementStock($order);

                    // Clear cart for the customer
                    CartItem::where('cust_id', $order->order_placed_cust_id)->delete();
                    session()->forget('cart_customizations');

                    // Send Notifications
                    $this->sendOrderNotifications($order);
                }

                return redirect()->route('website.thank-you', ['order_id' => $order->order_id])->with('success', 'Payment successful and order placed!');
            } else {
                return redirect()->route('website.cart')->with('error', 'Payment failed or cancelled: ' . ($payload['field9'] ?? 'Reason unknown'));
            }
        }

        return redirect()->route('website.cart')->with('error', 'Payment verification failed. Please contact support.');
    }

    /**
     * PayU Webhook
     */
    public function payuWebhook(Request $request)
    {
        $payload = $request->all();
        $payuHelper = new PayUHelper();

        if ($payuHelper->verifyHash($payload) && $payload['status'] === 'success') {
            $orderId = $payload['udf1'] ?? null;
            $order = Order::find($orderId);
            
            if ($order && $order->order_payment_status != 1) {
                $order->order_payment_status = 1;
                $order->order_status = \App\Enums\OrderStatus::CONFIRMED;
                $order->order_payment_date_time = now();
                $order->order_paid_amt = $order->order_total_amt;
                $order->order_due_amt = 0;
                $order->order_updated_at = now();
                $order->save();

                // Decrement stock for online payment orders
                $this->decrementStock($order);

                // Clear cart for the customer
                CartItem::where('cust_id', $order->order_placed_cust_id)->delete();

                $this->sendOrderNotifications($order);
            }
        }

        return response()->json(['status' => 'received']);
    }

    /**
     * Decrement available stock for every line of an order.
     */
    private function decrementStock(Order $order): void
    {
        foreach ($order->order_items as $item) {
            $stock = CartService::stockFor((int) $item['product_id'], $item['variant_id'] ?? null);

            if ($stock) {
                $stock->decrement('available_stock', (int) ($item['product_qty'] ?? 1));
            }
        }
    }

    /**
     * Restore available stock for every line of an order.
     */
    private function restoreStock(Order $order): void
    {
        foreach ($order->order_items as $item) {
            $stock = CartService::stockFor((int) $item['product_id'], $item['variant_id'] ?? null);

            if ($stock) {
                $stock->increment('available_stock', (int) ($item['product_qty'] ?? 1));
            }
        }
    }

    /**
     * Centralized method to send order notifications
     */
    private function sendOrderNotifications($order)
    {
        try {
            $delivery = $order->order_delivery_details;

            // Email to Customer
            //Mail::to($delivery['email'] ?? null)->send(new OrderPlacedMail($order, 'customer'));

            // Email to Admin
            //Mail::to(env('ADMIN_EMAIL', config('mail.from.address')))->send(new OrderPlacedMail($order, 'admin'));

            // WhatsApp & SMS Notification
            if ($delivery['phone'] ?? null) {
                NotificationHelper::notify('order_placed', [
                    'mobile' => $delivery['phone'],
                    'name' => ($delivery['first_name'] ?? 'Customer') . ' ' . ($delivery['last_name'] ?? ''),
                    'order_id' => $order->order_id,
                    'total_amount' => number_format($order->order_total_amt, 2)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Order Notification Error: ' . $e->getMessage());
        }
    }

    public function blogs()
    {
        $blogs = Blog::where('b_status', 1)->orderBy('b_id', 'desc')->paginate(9);
        return view('website.blogs', compact('blogs'));
    }

    public function blogDetails($slug)
    {
        $blog = Blog::where('b_slug', $slug)->where('b_status', 1)->firstOrFail();
        
        $recentBlogs = Blog::where('b_id', '!=', $blog->b_id)
            ->where('b_status', 1)
            ->orderBy('b_id', 'desc')
            ->take(5)
            ->get();
            
        return view('website.blog-details', compact('blog', 'recentBlogs'));
    }
}
