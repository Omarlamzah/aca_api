<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserResponse extends Model
{
    use HasFactory;
    protected $table = 'user_response_answers';
    protected $fillable = ['user_response_id', 'answer_id','question_id	'];
    public function answers()
    {
        return $this->belongsToMany(Answer::class, 'user_response_answers', 'user_response_id', 'answer_id');
    }
}
