<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppSlider extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_app_slider';
    
    protected $fillable = [
        'slider_photo_url',
        'caption',
        'status',
        'created_at',
        'updated_at'
    ];
}
