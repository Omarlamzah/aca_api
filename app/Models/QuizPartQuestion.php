<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizPartQuestion extends Model
{
    use HasFactory;
    protected $table = 'quiz_part_questions';
    protected $fillable = ['PartID', 'QuestionID'];
    public $timestamps = false; 
}
