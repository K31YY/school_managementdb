<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tblschedules', function (Blueprint $table) {
            $table->id('ScheduleID');
            $table->foreignId('YearID')->constrained('tblacademicyears', 'YearID');
            $table->foreignId('SectionID')->constrained('tblclasssections', 'SectionID');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblschedules');
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
//         Schema::create('schedules', function (Blueprint $table) {
//             $table->id();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('schedules');
//     }
// };
