<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winning extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'over_id',
        'win_type',
        'win_amount',
        'status'
    ];


    public function competitionMatch()
    {
        return $this->belongsTo(CompetitionMatches::class, 'match_id', 'match_id');
    }


    public function inningsOvers()
    {
        return $this->belongsTo(InningsOver::class, 'over_id', 'id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
