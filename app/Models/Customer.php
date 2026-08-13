<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // For login using customers
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'tbl_customers';
    protected $primaryKey = 'cust_id';

    protected $fillable = [
        'cust_name',
        'cust_email',
        'cust_mobile',
        'cust_password',
        'cust_address',
        'cust_city',
        'cust_state',
        'cust_country',
        'cust_pincode',
        'cust_gender',
        'cust_profile_photo',
        'cust_api_token',
        'cust_api_token_validity',
        'cust_status',
        'cust_created_at',
        'cust_updated_at',
    ];

    protected $hidden = [
        'cust_api_token',
    ];

    // Because your table uses custom timestamp column names
    public $timestamps = false;

    // Map timestamps manually
    const CREATED_AT = 'cust_created_at';
    const UPDATED_AT = 'cust_updated_at';

    public function getAuthPassword()
    {
        return $this->cust_password;
    }

    /**
     * Customer has many orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'order_placed_cust_id', 'cust_id');
    }
    
    protected $casts = [
        'cust_created_at' => 'datetime',
        'cust_updated_at' => 'datetime',

    ];
}
