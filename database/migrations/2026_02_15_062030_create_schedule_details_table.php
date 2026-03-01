<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tblscheduledetails', function (Blueprint $table) {
            $table->id('DetailID');
            $table->foreignId('ScheduleID')->constrained('tblschedules', 'ScheduleID');
            $table->foreignId('TeacherID')->constrained('tblteachers', 'TeacherID');
            $table->foreignId('SubID')->constrained('tblsubjects', 'SubID');
            $table->foreignId('RoomID')->constrained('tblrooms', 'RoomID');
            $table->string('DayOfWeek');
            $table->time('StartTime');
            $table->time('EndTime');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblscheduledetails');
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
//         Schema::create('schedule_details', function (Blueprint $table) {
//             $table->id();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('schedule_details');
//     }
// };
