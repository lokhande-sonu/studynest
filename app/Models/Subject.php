<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'tbl_subjects';
    protected $primaryKey = 'subject_id';

    protected $fillable = [
        'subject_name',
        'subject_status',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'tbl_product_subjects', 'subject_id', 'product_id');
    }
}
