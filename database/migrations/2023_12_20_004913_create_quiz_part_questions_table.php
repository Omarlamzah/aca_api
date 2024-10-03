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
        Schema::create('quiz_part_questions', function (Blueprint $table) {
            $table->unsignedBigInteger('PartID');
            $table->unsignedBigInteger('QuestionID');
            $table->primary(['PartID', 'QuestionID']);
            $table->foreign('PartID')->references('PartID')->on('quiz_parts')->onDelete('cascade');
            $table->foreign('QuestionID')->references('QuestionID')->on('questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_part_questions');
    }
};
