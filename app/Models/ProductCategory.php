<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;



class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'tbl_product_categories';
    protected $primaryKey = 'cat_id';

    protected $fillable = [
        'cat_name',
        'cat_photo',
        'cat_status',
        'cat_created_at',
        'cat_updated_at'
    ];

    public $timestamps = false;

    const CREATED_AT = 'cat_created_at';
    const UPDATED_AT = 'cat_updated_at';

    public function products()
    {
        return $this->hasMany(Product::class, 'p_cat_id', 'cat_id');
    }
    
    protected static function booted()
    {
        static::deleting(function ($productCategory) {
    
            $uploadPath = env('PRODUCT_CATEGORY_PHOTO', 'uploads/product-category-photo');
    
            if ($productCategory->cat_photo) {
                $fullPath = public_path($uploadPath . '/' . $productCategory->cat_photo);
    
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        });
    }
}


