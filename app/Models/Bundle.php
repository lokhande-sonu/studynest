<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Bundle extends Model
{
    use HasFactory;

    protected $table = 'tbl_bundles';
    protected $primaryKey = 'b_id';

    protected $fillable = [
        'b_name',
        'b_short_desc',
        'b_image',
        'b_status',
        'is_all_schools',
        'is_all_classes',
        'is_all_subjects',
        'b_created_at',
        'b_updated_at',
    ];

    public $timestamps = false;

    const CREATED_AT = 'b_created_at';
    const UPDATED_AT = 'b_updated_at';

    public function products()
    {
        return $this->belongsToMany(Product::class, 'tbl_bundle_products', 'bundle_id', 'product_id')->withPivot(['quantity', 'product_variant_id']);
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, 'tbl_bundle_schools', 'bundle_id', 'school_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'tbl_bundle_classes', 'bundle_id', 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'tbl_bundle_subjects', 'bundle_id', 'subject_id');
    }

    protected static function booted()
    {
        static::deleting(function ($bundle) {
            $uploadPath = 'uploads/bundle';
            if ($bundle->b_image) {
                $fullPath = public_path($uploadPath . '/' . $bundle->b_image);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        });
    }
}
