<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'tbl_product_variants';
    protected $primaryKey = 'prod_variant_id';

    protected $fillable = [
        'product_id',
        'prod_variant',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'p_id');
    }
}
