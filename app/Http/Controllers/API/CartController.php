<?php 
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\ProductInventory;
use App\Models\Product;
use App\Models\Charge;


class CartController extends Controller
{

    public function getCartData(Request $request)
    {
        $customer = $request->auth_customer;
    
        // Fetch active cart for customer
        $cart = Cart::where('cust_id', $customer->cust_id)
                    ->where('cart_status', 1)
                    ->first();
    
        if (!$cart) {
            return response()->json([
                'status' => true,
                'message' => 'Cart is empty',
                'data' => [
                    'items' => [],
                    'order_subtotal' => 0,
                    'order_items_qty' => 0,
                    'other_charges' => []
                ]
            ]);
        }
    
        // Decode products_in_cart
        $cartItems = json_decode($cart->products_in_cart, true);
    
        $cartData = collect($cartItems)->map(function($item) {
            // Fetch product details
            $product = Product::find($item['product_id']);
    
            if (!$product) return null;
    
            // Fetch inventory matching packet_size and product_unit
            $inventory = ProductInventory::where('product_id', $item['product_id'])
                            ->where('packet_size', $item['packet_size'])
                            ->where('product_unit', $item['product_unit'])
                            ->first();
    
            $availableQty = $inventory ? $inventory->p_stock_qty : 0;
            $inStock = $inventory && $inventory->p_stock_status && $availableQty >= $item['qty'];
            

            $folderName = env('PRODUCT_PHOTO', 'product-photo');
            $baseUrl = asset($folderName);
            $product->p_image = $baseUrl . '/' . $product->p_photo;
    
            return [
                'product_id'    => $product->p_id,
                'product_name'  => $product->p_name ?? null,
                'product_image' => $product->p_image ?? null,
                'packet_size'   => $item['packet_size'],
                'product_unit'  => $item['product_unit'],
                'requested_qty' => $item['qty'],
                'available_qty' => $availableQty,
                'in_stock'      => $inStock,
                'product_rate'  => $inventory->product_rate ?? 0,
                'price'         => ($inventory->product_rate*$item['qty']) ?? 0,
            ];
        })->filter()->values(); // Remove nulls
    
        // Filter in-stock items
        $inStockItems = $cartData->filter(function($item) {
            return $item['in_stock'];
        });
    
        // Calculate order subtotal (sum of in-stock item price)
        $order_subtotal = $inStockItems->sum(function($item) {
            return $item['product_rate'] * $item['requested_qty'];
        });
    
        // Total quantity of in-stock items
        $order_items_qty = $inStockItems->sum('requested_qty');
    
        // Get all active charges
        //$other_charges = Charge::where('charge_status', 1)->get();
        
        // Here I want to add one field under $other_charges array of every item
        // Pick the $other_charges->charge type from every item fixed/percentage
        // also pick $other_charges->value then $order_subtotal + $other_charges->value = $other_charges->charge_total in case of fixed 
        // in case of percentage $order_subtotal + ($order_subtotal*$other_charges->value/100) = $other_charges->charge_total
        
        // Fetch charges & calculate charge_total
    $other_charges = Charge::where('charge_status', 1)->get()->map(function ($charge) use ($order_subtotal) {

        if ($charge->charge_type === 'fixed') {
            $charge_total = $charge->charge_value;
        } elseif ($charge->charge_type === 'percentage') {
            $charge_total = (($order_subtotal * $charge->charge_value) / 100);
        } else {
            $charge_total = $order_subtotal;
        }

        return [
            'charge_id'     => $charge->charge_id ?? null,
            'charge_name'   => $charge->charge_name ?? null,
            'charge_type'   => $charge->charge_type,
            'value'         => $charge->charge_value,
            'charge_total'  => round($charge_total, 2)
        ];
    });
    
        return response()->json([
            'status' => true,
            'message' => 'Cart fetched successfully',
            'data' => [
                'items' => $cartData,
                'order_subtotal' => $order_subtotal,
                'order_items_qty' => $order_items_qty,
                'other_charges' => $other_charges,
            ]
        ]);
    }


    // public function setCartData(Request $request)
    // {
    //     $customer = $request->auth_customer;
    
    //     if ($request->cart_data) {
    //         // Validation
    //         $validator = \Validator::make($request->all(), [
    //             'cart_data' => 'required|array|min:1',
        
    //             'cart_data.*.product_id'   => 'required|integer',
    //             'cart_data.*.packet_size'  => 'required',
    //             'cart_data.*.product_unit' => 'required|string',
    //             'cart_data.*.qty'          => 'required|integer|min:1',
    //         ]);
        
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'status' => false,
    //                 'message' => 'Validation error',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }
            
        
    //         // Normalize Data
    //         $cartItems = collect($request->cart_data)->map(function($item) {
    //             return [
    //                 'product_id'   => (int) $item['product_id'],
    //                 'packet_size'  => trim($item['packet_size']),
    //                 'product_unit' => trim($item['product_unit']),
    //                 'qty'          => (int) $item['qty'],
    //             ];
    //         })->all();
        
    //         // Save to Cart Table
    //         $cart = Cart::updateOrCreate(
    //                 ['cust_id' => $customer->cust_id],
    //                 [
    //                     'products_in_cart' => json_encode($cartItems),
    //                     'cart_status' => 1,
    //                     'cart_created_at' => now(),
    //                     'cart_updated_at' => now()
    //                 ]
    //             );
                
    //         $cart->products_in_cart = json_decode($cart->products_in_cart, true); // order_items is JSON
            
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Cart saved successfully',
    //             'data' => $cart
    //         ]);
            
    //     } else {
    //         return response()->json([
    //             'status' => true,
    //             'message' => 'Cart Data Empty',
                
    //         ]);
    //     }
    // }
    
    public function setCartData(Request $request)
{
    $customer = $request->auth_customer;

    // Empty cart payload
    if (empty($request->cart_data)) {
        return response()->json([
            'status'  => true,
            'message' => 'Cart Data Empty',
            'data'    => []
        ]);
    }

    // Validation
    $validator = \Validator::make($request->all(), [
        'cart_data' => 'required|array|min:1',
        'cart_data.*.product_id'   => 'required|integer',
        'cart_data.*.packet_size'  => 'required',
        'cart_data.*.product_unit' => 'required|string',
        'cart_data.*.qty'          => 'required|integer|min:1',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => false,
            'message' => 'Validation error',
            'errors'  => $validator->errors()
        ], 422);
    }

    // Normalize incoming items
    $newItems = collect($request->cart_data)->map(function ($item) {
        return [
            'product_id'   => (int) $item['product_id'],
            'packet_size'  => trim((string) $item['packet_size']),
            'product_unit' => trim((string) $item['product_unit']),
            'qty'          => (int) $item['qty'],
        ];
    });

    // Fetch active cart
    $cart = Cart::where('cust_id', $customer->cust_id)
                ->where('cart_status', 1)
                ->first();

    // ==============================
    // EXISTING CART
    // ==============================
    if ($cart) {

        $existingItems = collect(
            json_decode($cart->products_in_cart, true) ?? []
        );

        // Index cart items using unique key
        $indexedItems = $existingItems->keyBy(function ($item) {
            return $item['product_id'] . '|' . $item['packet_size'] . '|' . $item['product_unit'];
        });

        foreach ($newItems as $newItem) {

            $key = $newItem['product_id'] . '|' . $newItem['packet_size'] . '|' . $newItem['product_unit'];

            if ($indexedItems->has($key)) {
                // 🔥 FIX: get → modify → put (NO indirect modification)
                $item = $indexedItems->get($key);
                $item['qty'] += $newItem['qty'];
                $indexedItems->put($key, $item);
            } else {
                // New packet/unit → new entry
                $indexedItems->put($key, $newItem);
            }
        }

        $cart->update([
            'products_in_cart' => json_encode($indexedItems->values()->all()),
            'cart_updated_at'  => now(),
        ]);
    }
    // ==============================
    // NEW CART
    // ==============================
    else {

        $cart = Cart::create([
            'cust_id'           => $customer->cust_id,
            'products_in_cart'  => json_encode($newItems->values()->all()),
            'cart_status'       => 1,
            'cart_created_at'   => now(),
            'cart_updated_at'   => now(),
        ]);
    }

    // Prepare response
    $cart->products_in_cart = json_decode($cart->products_in_cart, true);

    return response()->json([
        'status'  => true,
        'message' => 'Cart updated successfully',
        'data'    => $cart
    ]);
}

    
    public function deleteCartItem(Request $request)
    {
        $customer = $request->auth_customer;
    
        $validator = \Validator::make($request->all(), [
            'cart_data' => 'required|array|min:1',
            'cart_data.*.product_id'   => 'required|integer',
            'cart_data.*.packet_size'  => 'required',
            'cart_data.*.product_unit' => 'required|string',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }
    
        $deleteItem = $request->cart_data[0]; // single delete
    
        $cart = Cart::where('cust_id', $customer->cust_id)
                    ->where('cart_status', 1)
                    ->first();
    
        if (!$cart || !$cart->products_in_cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart not found'
            ]);
        }
    
        $cartItems = collect(json_decode($cart->products_in_cart, true));
    
        $filteredItems = $cartItems->reject(function ($item) use ($deleteItem) {
            return (
                $item['product_id'] == $deleteItem['product_id'] &&
                $item['packet_size'] == $deleteItem['packet_size'] &&
                $item['product_unit'] == $deleteItem['product_unit']
            );
        })->values();
    
        $cart->products_in_cart = json_encode($filteredItems);
        $cart->cart_updated_at = now();
        $cart->save();
    
        return response()->json([
            'status' => true,
            'message' => 'Item removed from cart',
            'data' => $filteredItems
        ]);
    }
   



}
