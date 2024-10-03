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
        Schema::table('quiz_parts', function (Blueprint $table) {
            $table->string('identify'); // Adjust the data type and other options as needed

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_parts', function (Blueprint $table) {
            $table->dropColumn('identify');

        });
    }
};
