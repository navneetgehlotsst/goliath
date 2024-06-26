<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable ,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $appends = ['avatar_full_path'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }



    public function getAvatarFullPathAttribute()
    {
        if($this->avatar != ''){
            return asset($this->avatar);
        }else{
            return "";
        }
    }


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


    // Define the transactions relationship
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'id', 'user_id');
    }


    // Define the predictions relationship
    public function predictions()
    {
        return $this->hasMany(Prediction::class);
    }

}
