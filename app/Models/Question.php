<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $primaryKey = 'QuestionID';
    protected $fillable = ['QuestionText', 'ImgURL', 'IsBest',"quiz_id","ismainquestion	"];

    public function quizParts()
    {
        return $this->belongsToMany(QuizPart::class, 'quiz_part_questions', 'QuestionID', 'PartID');
    }

    public function definitions()
    {
        return $this->hasMany(Definition::class, 'QuestionID');
    }

    public function answers()
    {
        return $this->hasMany(Answer::class, 'QuestionID');
    }



    public function userResponses()
    {
        return $this->belongsToMany(UserResponse::class, 'user_response_questions', 'question_id', 'user_response_id');
    }
}
