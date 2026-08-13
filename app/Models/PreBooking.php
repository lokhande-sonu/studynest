<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_id',
        'customer_name',
        'customer_email',
        'customer_mobile',
        'token_amount',
        'payment_status',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class, 'school_id', 'sch_id');
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id', 'class_id');
    }
}
