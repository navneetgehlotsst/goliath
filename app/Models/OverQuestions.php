<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverQuestions extends Model
{
    use HasFactory;

    protected $table = 'over_questions';

    protected $fillable = ['innings_over_id', 'question_id'];

    public function over() {
        return $this->belongsTo(InningsOver::class);
    }

    // Define the relationship with the Question model
    public function loadquestion()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }

}
