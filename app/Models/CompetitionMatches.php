<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionMatches extends Model
{
    use HasFactory;

    protected $fillable = [
        'competiton_id',
        'match_id',
        'match',
        'teama_name',
        'teama_short_name',
        'teama_img',
        'teamb_name',
        'teamb_short_name',
        'teamb_img',
        'formate',
        'match_start_date',
        'match_start_time',
        'status',
        'note',
        'teamaid',
        'teambid',
        'teamascorefull',
        'teambscorefull',
        'teamascore',
        'teambscore',
        'teamaover',
        'teambover',
    ];
}
