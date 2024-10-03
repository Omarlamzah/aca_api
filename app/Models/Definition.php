<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Definition extends Model
{
    use HasFactory;
    protected $primaryKey = 'DefinitionID';
    protected $fillable = ['PartID', 'QuestionID', 'Description', 'ImageURL'];

    public function quizPart()
    {
        return $this->belongsTo(QuizPart::class, 'PartID');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'QuestionID');
    }
}
