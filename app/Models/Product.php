<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;


class Product extends Model
{
    use HasFactory;

    protected $table = 'tbl_products';
    protected $primaryKey = 'p_id';

    protected $fillable = [
        'p_cat_id',
        'p_name',
        'p_tag',
        'p_highlight',
        'p_short_desc',
        'p_full_desc',
        'p_photo',
        'p_brand',
        'p_gender',
        'is_all_schools',
        'is_all_classes',
        'is_all_subjects',
        'p_status',
        'p_published_on',
        'p_created_at',
        'p_updated_at',
    ];

    public $timestamps = false;

    const CREATED_AT = 'p_created_at';
    const UPDATED_AT = 'p_updated_at';

    /**
     * Relation with Product Category
     */
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'p_cat_id', 'cat_id');
    }
    
    // Legacy inventory relation, can be kept or removed. keeping for now.
    public function inventory()
    {
        return $this->hasMany(ProductInventory::class, 'product_id', 'p_id');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'p_id');
    }

    public function stockInventories()
    {
        return $this->hasMany(ProductStockInventory::class, 'prod_id', 'p_id');
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'tbl_product_schools', 'product_id', 'school_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'tbl_product_classes', 'product_id', 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'tbl_product_subjects', 'product_id', 'subject_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'p_id');
    }

    public function getPPriceAttribute()
    {
        return $this->stockInventories->min('unit_price') ?? 0;
    }
    
    public function getDiscountedPriceAttribute()
    {
        return $this->stockInventories->min('discounted_unit_price');
    }

    protected static function booted()
    {
        static::deleting(function ($product) {
    
            $uploadPath = env('PRODUCT_PHOTO', 'uploads/product-photo');
    
            if ($product->p_photo) {
                $fullPath = public_path($uploadPath . '/' . $product->p_photo);
    
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        });
    }
}
