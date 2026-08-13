<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'tbl_orders';
    protected $primaryKey = 'order_id';

    protected $fillable = [
        'order_placed_cust_id',
        'order_items_qty',
        'order_items',
        'order_delivery_details',
        'order_gst_number',
        'order_gst_amount',
        'order_charges',
        'order_total_amt',
        'order_paid_amt',
        'order_due_amt',
        'order_payment_id',
        'order_payment_mode',
        'order_date_time',
        'order_payment_date_time',
        'order_tracking_details',
        'order_payment_status',
        'order_status',
        'order_created_at',
        'order_updated_at',
    ];

    public $timestamps = false;

    const CREATED_AT = 'order_created_at';
    const UPDATED_AT = 'order_updated_at';

    /**
     * Cast JSON fields automatically
     */
    protected $casts = [
        'order_items'           => 'array',
        'order_delivery_details'=> 'array',
        'order_charges'         => 'array',
    ];

    /**
     * Order belongs to a customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'order_placed_cust_id', 'cust_id');
    }
    
    // Total distinct products (SAFE)
    public function getTotalItemsAttribute()
    {
        $items = $this->order_items;
    
        if (is_string($items)) {
            $items = json_decode($items, true);
        }
    
        return is_array($items) ? count($items) : 0;
    }
    
    public function getTotalQuantityAttribute()
    {
        $items = $this->order_items;
    
        if (is_string($items)) {
            $items = json_decode($items, true);
        }
    
        return is_array($items)
            ? collect($items)->sum('product_qty')
            : 0;
    }
    
    /**
     * ALWAYS return order_items as array
     */
    public function getOrderItemsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
    
        // First decode
        if (is_string($value)) {
            $decoded = json_decode($value, true);
    
            // If still string → decode again (DOUBLE ENCODED)
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
    
            return is_array($decoded) ? $decoded : [];
        }
    
        return is_array($value) ? $value : [];
    }
    
    public function getOrderChargesAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
    
        // First decode
        if (is_string($value)) {
            $decoded = json_decode($value, true);
    
            // Handle double-encoded JSON
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
    
            return is_array($decoded) ? $decoded : [];
        }
    
        return is_array($value) ? $value : [];
    }
    
    public function getOrderDeliveryDetailsAttribute($value)
    {
        if (empty($value)) {
            return [];
        }
    
        // First decode
        if (is_string($value)) {
            $decoded = json_decode($value, true);
    
            // Handle double-encoded JSON
            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }
    
            return is_array($decoded) ? $decoded : [];
        }
    
        return is_array($value) ? $value : [];
    }

}
