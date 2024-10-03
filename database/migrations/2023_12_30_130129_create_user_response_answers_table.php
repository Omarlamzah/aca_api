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
        Schema::create('user_response_answers', function (Blueprint $table) {
            $table->id();
            $table->string("user_response_id");
            $table->string("answer_id");


             $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            // Add other columns as needed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_response_answers');
    }
};
