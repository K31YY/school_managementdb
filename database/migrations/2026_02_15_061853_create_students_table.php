<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tblstudents', function (Blueprint $table) {
            $table->id('StuID');
            $table->foreignId('UserID')->nullable()->constrained('tblusers', 'UserID')->onDelete('cascade');
            $table->string('StuName');
            $table->string('StuNameKH')->nullable();
            $table->string('StuNameEN')->nullable();
            $table->string('Gender');
            $table->date('DOB');
            $table->string('POB')->nullable();
            $table->text('Address')->nullable();
            $table->string('Phone');
            $table->string('Email')->unique();
            $table->string('Promotion')->nullable();
            $table->string('Photo')->nullable();
            $table->string('FatherName')->nullable();
            $table->string('FatherJob')->nullable();
            $table->string('MotherName')->nullable();
            $table->string('MotherJob')->nullable();
            $table->string('FamilyContact')->nullable();
            $table->boolean('Status')->default(1);
            $table->boolean('IsDeleted')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tblstudents');
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
//         Schema::create('students', function (Blueprint $table) {
//             $table->id();
//             $table->timestamps();
//         });
//     }

//     /**
//      * Reverse the migrations.
//      */
//     public function down(): void
//     {
//         Schema::dropIfExists('students');
//     }
// };
