<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUser extends Model
{
    use HasFactory;


    protected $fillable = [
        'first_name',
        'last_name',
        'full_name',
        'slug',
        'email',
        'phone',
        'country_code',
        'role',
        'type',
        'otp',
        'otp_expired',
        'avatar',
        'device_token',
        'device_type',
        'status',
        'timezone'
    ];
}
