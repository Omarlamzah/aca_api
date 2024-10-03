<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->id('AnswerID');
            $table->unsignedBigInteger('QuestionID');
            $table->text('AnswerText');
            $table->boolean('IsCorrect');
            $table->timestamps();
            $table->foreign('QuestionID')->references('QuestionID')->on('questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('answers');
    }
};
