<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    use HasFactory;

    protected $table = 'tbl_prod_inventory';
    protected $primaryKey = 'p_stock_id';

    protected $fillable = [
        'product_id',
        'packet_size',
        'product_unit',
        'product_rate',        // required field
        'p_stock_qty',         // required field
        'p_stock_status',
        'p_stock_created_at',
        'p_stock_updated_at',
    ];

    public $timestamps = false;

    const CREATED_AT = 'p_stock_created_at';
    const UPDATED_AT = 'p_stock_updated_at';

    /**
     * Relation with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'p_id');
    }
}
