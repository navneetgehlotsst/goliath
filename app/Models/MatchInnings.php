<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchInnings extends Model
{
    use HasFactory;

    public function overs() {
        return $this->morphMany(InningsOver::class, 'overable');
    }

    public function inningsOvers()
    {
        return $this->hasMany(InningsOver::class, 'match_innings_id');
    }
}
