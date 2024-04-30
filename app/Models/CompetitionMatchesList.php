<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionMatchesList extends Model
{
    use HasFactory;

    protected $fillable = [
        'competiton_id',
        'match_id',
        'match',
        'teama_name',
        'teama_img',
        'teamb_name',
        'teamb_img',
        'formate',
        'match_start_date',
        'match_start_time',
        'status'
    ];
}
