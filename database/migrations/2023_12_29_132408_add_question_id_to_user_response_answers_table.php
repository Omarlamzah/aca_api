<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_response_answers', function (Blueprint $table) {
            $table->unsignedBigInteger('question_id'); // Adjust the data type and other options as needed
            $table->foreign('question_id')->references('QuestionID')->on('questions'); // Adjust the referenced column and table as needed

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_response_answers', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn('question_id');
        });
    }
};
