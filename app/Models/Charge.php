<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charge extends Model
{
    use HasFactory;

    protected $table = 'tbl_charges';
    protected $primaryKey = 'charge_id';

    protected $fillable = [
        'charge_name',
        'charge_type',
        'charge_value',
        'charge_status',
        'charge_created_at',
        'charge_updated_at',
    ];

    public $timestamps = false;

    const CREATED_AT = 'charge_created_at';
    const UPDATED_AT = 'charge_updated_at';
}
