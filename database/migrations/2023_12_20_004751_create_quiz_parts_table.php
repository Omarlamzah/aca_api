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
        Schema::create('quiz_parts', function (Blueprint $table) {
            $table->id('PartID');
            $table->string('PartName');
            $table->string('type');
            $table->string('ImgURL')->nullable();
            $table->string('ImgCorrectURL')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('quiz_parts');
    }
};
