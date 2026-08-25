<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    use HasFactory;
    
    protected $table = 'tbl_contact_info';
    
    protected $fillable = [
        'mobile',
        'email',
        'address',
        'whatsapp',
        'instagram_url',
        'facebook_url',
        'twitter_url',
        'youtube_url',
        'status',
        'created_at',
        'updated_at'
    ];
}
