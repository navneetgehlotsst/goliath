<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetitionList extends Model
{
    use HasFactory;

    protected $fillable = [
        'competiton_id',
        'title',
        'type',
        'competition_type',
        'date_start',
        'date_end',
        'status',
    ];
}
