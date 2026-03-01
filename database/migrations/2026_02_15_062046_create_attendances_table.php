<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tblattendances', function (Blueprint $table) {
            $table->id('AttID');
            $table->foreignId('StuID')->constrained('tblstudents', 'StuID');
            $table->foreignId('DetailID')->constrained('tblscheduledetails', 'DetailID');
            $table->date('AttDate');
            $table->enum('Status', ['P', 'A', 'P_Auth']);
            $table->text('Reason')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblattendances');
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
//         Schema::create('attendances', function (Blueprint $table) {
//             $table->id();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('attendances');
//     }
// };
