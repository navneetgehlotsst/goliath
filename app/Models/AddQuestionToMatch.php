<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AddQuestionToMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'matchid',
        'questionid	',
        'over',
    ];


    public function Question()
    {
        return $this->belongsTo(Question::class, 'id');
    }
}
