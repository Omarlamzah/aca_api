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
        Schema::create('definitions', function (Blueprint $table) {
            $table->id('DefinitionID');
            $table->unsignedBigInteger('PartID');
            $table->unsignedBigInteger('QuestionID');
            $table->text('Description');
            $table->string('ImageURL')->nullable();
            $table->timestamps();
            $table->foreign('PartID')->references('PartID')->on('quiz_parts')->onDelete('cascade');
            $table->foreign('QuestionID')->references('QuestionID')->on('questions')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('definitions');
    }
};
