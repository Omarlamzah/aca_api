<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizPart extends Model
{
    use HasFactory;
    protected $primaryKey = 'PartID';
    protected $fillable = ['PartName', 'ImgURL', 'ImgCorrectURL',"type","identify","id_esaai"];

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'quiz_part_questions', 'PartID', 'QuestionID');
    }

    public function definitions()
    {
        return $this->hasMany(Definition::class, 'PartID');
    }

    public function userQuizTokens()
{
    return $this->hasMany(UserQuizToken::class);
}





    public function essai()
    {
        return $this->belongsTo(Essai::class, 'id_esaai', );
    }
}
