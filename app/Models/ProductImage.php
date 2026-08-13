<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected $table = 'tbl_product_images';

    protected $fillable = [
        'product_id',
        'img_path',
        'sort_order',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'p_id');
    }
}
