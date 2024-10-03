<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
                  $table->integer('quiz_id'); // Adjust the data type and other options as needed
 

        });
    }
    
    public function down()
    {
        Schema::table('questions', function (Blueprint $table) {
           
            $table->dropColumn('quiz_id');
        });
    }
    
};
