<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Management extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'tbl_managements';
    protected $primaryKey = 'm_id';


    protected $fillable = ['name','email', 'username', 'mobile', 'email_verified_at','password', 'role', 'department', 'work_location', 'profile_photo', 'api_token','updated_at','created_at','status'];

    protected $hidden = ['password', 'api_token'];

}
