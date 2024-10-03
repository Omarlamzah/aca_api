<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;
    protected $primaryKey = 'AnswerID';
    protected $fillable = ['QuestionID', 'AnswerText', 'IsCorrect'];

    public function question()
    {
        return $this->belongsTo(Question::class, 'QuestionID');
    }




    public function userResponses()
    {
        return $this->belongsToMany(UserResponse::class, 'user_response_answers', 'answer_id', 'user_response_id');
    }
}
