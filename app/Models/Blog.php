<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    protected $table = 'tbl_blogs';
    protected $primaryKey = 'b_id';

    protected $fillable = [
        'b_title',
        'b_slug',
        'b_content',
        'b_image',
        'b_status',
        'b_meta_title',
        'b_meta_description',
        'b_meta_keywords',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'b_slug';
    }
}
