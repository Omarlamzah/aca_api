<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Essai extends Model
{
    use HasFactory;
    protected $fillable =["name"];
   protected $table= "validationessai";
    public function Quizes()
    {
        return $this->hasMany(QuizPart::class, 'id_esaai');
    }
}
