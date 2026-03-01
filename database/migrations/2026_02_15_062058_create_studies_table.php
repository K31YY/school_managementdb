<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tblstudies', function (Blueprint $table) {
            $table->id('StudyID');
            $table->foreignId('StuID')->constrained('tblstudents', 'StuID');
            $table->foreignId('SubID')->constrained('tblsubjects', 'SubID');
            $table->foreignId('YearID')->constrained('tblacademicyears', 'YearID');
            $table->float('Quiz')->default(0);
            $table->float('Homework')->default(0);
            $table->float('AttendanceScore')->default(0);
            $table->float('Participation')->default(0);
            $table->float('Midterm')->default(0);
            $table->float('Final')->default(0);
            $table->float('TotalScore')->default(0);
            $table->integer('Semester');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblstudies');
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
//         Schema::create('studies', function (Blueprint $table) {
//             $table->id();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('studies');
//     }
// };
