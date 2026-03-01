<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
         /**
        * Run the migrations.
        */
        Schema::create('tblusers', function (Blueprint $table) {
            $table->id('UserID');
            $table->string('Username')->unique();
            $table->string('Password');
            $table->enum('Role', ['Admin', 'Teacher', 'Student']);
            $table->string('remember_token')->nullable();
            $table->boolean('Status')->default(1);
            $table->timestamps();
        });
    }
     /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tblusers');
    }
};
