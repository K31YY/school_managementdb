<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tblreportlogs', function (Blueprint $table) {
            $table->id('LogID');
            $table->foreignId('UserID')->constrained('tblusers', 'UserID');
            $table->string('ReportType');
            $table->timestamp('GeneratedAt');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblreportlogs');
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
//         Schema::create('report_logs', function (Blueprint $table) {
//             $table->id();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('report_logs');
//     }
// };
