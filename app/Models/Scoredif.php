<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scoredif extends Model
{
      use HasFactory;

    protected $table = 'scorechoosen';
    public $timestamps = false;

    protected $fillable = ['scoredif',"multiple"];



}
