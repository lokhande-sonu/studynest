<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'tbl_cust_cart';
    protected $primaryKey = 'cart_id';

    protected $fillable = [
        'cust_id',
        'products_in_cart',
        'cart_status',
        'cart_created_at',
        'cart_updated_at',
    ];

    public $timestamps = false;

    const CREATED_AT = 'cart_created_at';
    const UPDATED_AT = 'cart_updated_at';

    /**
     * Relation with Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'cust_id');
    }
}
