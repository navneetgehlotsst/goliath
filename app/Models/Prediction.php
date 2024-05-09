<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'question_id',
        'over_id',
        'answere'
    ];


    public function competitionMatch()
    {
        return $this->belongsTo(CompetitionMatches::class, 'match_id', 'match_id');
    }
}
