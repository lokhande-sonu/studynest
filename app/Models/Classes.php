<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'tbl_classes';
    protected $primaryKey = 'class_id';

    protected $fillable = [
        'class_name',
        'class_status',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'tbl_product_classes', 'class_id', 'product_id');
    }
}
