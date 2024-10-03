<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_quiz_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained(); // Assuming you have a 'users' table.
            $table->foreignId('quiz_part_id')->constrained('quiz_parts', 'PartID');
            $table->string('token');
            // Add other columns as needed.
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_quiz_tokens');
    }
};
