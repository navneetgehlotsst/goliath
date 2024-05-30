<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeZone extends Model
{
    use HasFactory;

    protected $table = "timezone";

    protected $fillable = [
        'country_code',
        'timezone',
        'gmt_offset',
        'dst_offset',
        'raw_offset'
    ];
}
