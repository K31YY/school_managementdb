<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tblteachers', function (Blueprint $table) {
            $table->id('TeacherID');
            $table->foreignId('UserID')->nullable()->constrained('tblusers', 'UserID')->onDelete('cascade');
            $table->string('TeacherName');
            $table->string('Gender');
            $table->date('DOB')->nullable();
            $table->string('Phone');
            $table->string('Email')->unique();
            $table->string('Specialty');
            $table->text('Address')->nullable();
            $table->date('StartDate')->nullable();
            $table->date('EndDate')->nullable();
            $table->string('Certificate')->nullable();
            $table->string('Photo')->nullable();
            $table->boolean('IsDeleted')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblteachers');
    }
};


// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Database\Schema\Blueprint;
// use Illuminate\Support\Facades\Schema;

// return new class extends Migration
// {
//     /**
//      * Run the migrations.
//      */
//     public function up(): void
//     {
//         Schema::create('teachers', function (Blueprint $table) {
//             $table->id();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('teachers');
//     }
// };
