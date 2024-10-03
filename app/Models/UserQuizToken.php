<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizToken extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'quiz_part_id', 'token'];



    public function quizpart()
    {
        return $this->belongsTo(QuizPart::class, 'quiz_part_id');
    }

}



