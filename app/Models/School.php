<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class School extends Model
{
    use HasFactory;

    protected $table = 'tbl_schools';
    protected $primaryKey = 'sch_id';

    protected $fillable = [
        'sch_name',
        'sch_mobile',
        'sch_email',
        'sch_address',
        'sch_desc',
        'sch_city',
        'sch_logo',
        'sch_status',
        'is_verified',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'tbl_product_schools', 'school_id', 'product_id');
    }

    protected static function booted()
    {
        static::deleting(function ($school) {
            $uploadPath = 'uploads/school-logo';
            if ($school->sch_logo) {
                $fullPath = public_path($uploadPath . '/' . $school->sch_logo);
                if (File::exists($fullPath)) {
                    File::delete($fullPath);
                }
            }
        });
    }
}
