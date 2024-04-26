<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InningsOver extends Model
{
    use HasFactory;

    protected $table = 'innings_overs';

    protected $fillable = ['match_innings_id', 'overs'];

    public function overable() {
        return $this->morphTo();
    }

    public function questions() {
        return $this->hasMany(OverQuestion::class);
    }
}
