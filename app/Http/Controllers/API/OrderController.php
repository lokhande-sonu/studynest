<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\ProductInventory;
use Illuminate\Support\Facades\DB;





class OrderController extends Controller
{
    // public function placeOrder(Request $request)
    // {
    //     $customer = $request->auth_customer;
    
    //     // Create the order
    //     $order = Order::create([
    //         'order_placed_cust_id' => $customer->cust_id,
    //         'order_items_qty' => $request->order_items_qty,
    //         'order_items' => json_encode($request->order_items),
    //         'order_delivery_details' => json_encode($request->delivery_details),
    //         'order_charges' => json_encode($request->order_charges),
    //         'order_total_amt' => $request->order_total_amt,
    //         'order_paid_amt' => 0,
    //         'order_due_amt' => $request->order_total_amt,
    //         'order_payment_mode' => $request->order_payment_mode,
    //         'order_date_time' => now(),
    //         'order_payment_status' => 0,
    //         'order_status' => 2, // Payment Pending 
    //         'order_created_at' => now(),
    //     ]);
        
    //     //  $order->order_items = json_decode($order->order_items, true); // order_items is JSON
    //     // $order->order_delivery_details = json_decode($order->order_delivery_details, true); // order_items is JSON
    //     // $order->order_charges = json_decode($order->order_charges, true); // order_items is JSON

    
    //     // Clear the customer's active cart
    //     Cart::where('cust_id', $customer->cust_id)
    //             ->where('cart_status', 1)
    //             ->delete();
    
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Order placed successfully (awaiting payment)',
    //         'data' => $order
    //     ]);
    // }
    

    public function placeOrder(Request $request)
    {
        $customer = $request->auth_customer;
    
        DB::beginTransaction();
    
        try {
    
            // ---- Order creation (same as before) ----
            $order = Order::create([
                'order_placed_cust_id'   => $customer->cust_id,
                'order_items_qty'        => $request->order_items_qty,
                'order_items'            => json_encode($request->order_items),
                'order_delivery_details' => json_encode($request->delivery_details),
                'order_charges'          => json_encode($request->order_charges),
                'order_total_amt'        => $request->order_total_amt,
                'order_paid_amt'         => 0,
                'order_due_amt'          => $request->order_total_amt,
                'order_payment_mode'     => $request->order_payment_mode,
                'order_date_time'        => now(),
                'order_payment_status'   => 0,
                'order_status'           => 2,
                'order_created_at'       => now(),
            ]);
    
            // ---- Stock deduction ----
            foreach ($request->order_items as $item) {
    
                $inventory = ProductInventory::where('product_id', $item['product_id'])
                    ->where('packet_size', $item['packet_size'])
                    ->where('product_unit', $item['product_unit'])
                    ->where('p_stock_status', 1)
                    ->lockForUpdate()
                    ->first();
    
                if (!$inventory) {
                    throw new \Exception(
                        "Stock not found for Product ID {$item['product_id']} ({$item['packet_size']} {$item['product_unit']})"
                    );
                }
    
                if ($inventory->p_stock_qty < $item['product_qty']) {
                    throw new \Exception(
                        "Only {$inventory->p_stock_qty} left for Product ID {$item['product_id']} ({$item['packet_size']} {$item['product_unit']})"
                    );
                }
    
                $inventory->update([
                    'p_stock_qty' => $inventory->p_stock_qty - $item['product_qty'],
                    'p_stock_updated_at' => now(),
                ]);
            }
    
            DB::commit();
    
            return response()->json([
                'status'  => true,
                'message' => 'Order placed successfully',
                'data'    => $order
            ], 200);
    
        } catch (\Exception $e) {
    
            DB::rollBack();
    
            return response()->json([
                'status'    => false,
                'message'   => $e->getMessage(), // ✅ exception sent to API
                'exception' => class_basename($e) // optional
            ], 400);
        }
    }



    public function confirmOrder(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->first();

        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }

        // Update order payment details
        $order->order_payment_id = $request->payment_id;
        $order->order_paid_amt = $order->order_total_amt;
        $order->order_due_amt = 0;
        $order->order_payment_status = 1;
        $order->order_payment_date_time = now();
        $order->order_status = 3; // Payment Completed
        $order->order_updated_at = now();
        $order->save();

        // Deduct stock for each product in the order
        $orderItems = json_decode($order->order_items, true); // order_items is JSON

        foreach ($orderItems as $item) {
            $inventory = ProductInventory::where('product_id', $item['product_id'])
                            ->where('packet_size', $item['packet_size'])
                            ->where('product_unit', $item['product_unit'])
                            ->first();

            if ($inventory) {
                $inventory->p_stock_qty = max(0, $inventory->p_stock_qty - $item['product_qty']);
                $inventory->save();
            }
        }

        // Clear customer's cart
        Cart::where('cust_id', $order->order_placed_cust_id)
            ->where('cart_status', 1)
            ->delete();
        
        $order->order_items = json_decode($order->order_items, true); // order_items is JSON
        $order->order_delivery_details = json_decode($order->order_delivery_details, true); // order_items is JSON
        $order->order_charges = json_decode($order->order_charges, true); // order_items is JSON


        return response()->json([
            'status' => true,
            'message' => 'Order payment confirmed and stock updated',
            'data' => $order
        ]);
    }


    public function getOrders(Request $request)
    {
        $customer = $request->auth_customer;
    
        $orders = Order::where('order_placed_cust_id', $customer->cust_id)
                       ->orderBy('order_id', 'DESC')
                       ->get()
                       ->map(function($order) {
                            return [
                                'order_id' => $order->order_id,
                                'order_items_qty' => $order->order_items_qty,
                                'order_items' => $order->order_items,
                                'delivery_details' => $order->order_delivery_details,
                                'order_charges' => $order->order_charges,
                                'order_total_amt' => $order->order_total_amt,
                                'order_paid_amt' => $order->order_paid_amt,
                                'order_due_amt' => $order->order_due_amt,
                                'order_payment_mode' => $order->order_payment_mode,
                                'order_payment_status' => $order->order_payment_status,
                                'order_status' => $order->order_status,
                                'order_date_time' => $order->order_date_time,
                            ];
                       });
    
        return response()->json([
            'status' => true,
            'message' => 'Orders fetched successfully',
            'data' => $orders
        ]);
    }

    
    public function getOrderDetails(Request $request)
    {
        $customer = $request->auth_customer;
    
        // Fetch the order for this customer
        $order = Order::where('order_id', $request->order_id)
                      ->where('order_placed_cust_id', $customer->cust_id)
                      ->first();
    
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order not found'
            ], 404);
        }
    
        // Decode order JSON fields
        $orderItems = $order->order_items;
        $deliveryDetails = $order->order_delivery_details;
        $orderCharges = $order->order_charges;
    
        // Prepare response
        $response = [
            'order_id' => $order->order_id,
            'order_items_qty' => $order->order_items_qty,
            'order_items' => $orderItems,
            'delivery_details' => $deliveryDetails,
            'order_charges' => $orderCharges,
            'order_total_amt' => $order->order_total_amt,
            'order_paid_amt' => $order->order_paid_amt,
            'order_due_amt' => $order->order_due_amt,
            'order_payment_mode' => $order->order_payment_mode,
            'order_payment_status' => $order->order_payment_status,
            'order_status' => $order->order_status,
            'order_date_time' => $order->order_date_time,
            'order_updated_date_time' => $order->order_updated_at,

        ];
    
        return response()->json([
            'status' => true,
            'message' => 'Order details fetched successfully',
            'data' => $response
        ]);
    }


    public function updateOrder(Request $request)
    {
        $order = Order::where('order_id', $request->order_id)->first();

        $order->order_status = $request->order_status;
        $order->order_updated_at = now();
        $order->save();
        
        $order->order_items = $order->order_items; // order_items is JSON
        $order->order_delivery_details = $order->order_delivery_details; // order_items is JSON
        $order->order_charges = $order->order_charges; // order_items is JSON

        return response()->json([
            'status' => true,
            'message' => 'Order updated',
            'data' => $order
        ]);
    }
}
