<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockInventory extends Model
{
    use HasFactory;

    protected $table = 'tbl_product_stock_inventory';

    protected $fillable = [
        'prod_sku',
        'prod_id',
        'prod_variant_id',
        'available_stock',
        'unit_price',
        'discounted_unit_price',
        'gst_rate',
        'gst_type',
        'cgst_rate',
        'sgst_rate',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'prod_id', 'p_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'prod_variant_id', 'prod_variant_id');
    }
}
