<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // add new column 'password' to students table after 'Email'
        Schema::table('tblstudents', function (Blueprint $table) {
            // we make it nullable for now 
            $table->string('password')->after('Email')->nullable();
        });

        // add new column 'password' to teachers table after 'Email'
        Schema::table('tblteachers', function (Blueprint $table) {
            $table->string('password')->after('Email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tblstudents', function (Blueprint $table) {
            $table->dropColumn('password');
        });

        Schema::table('tblteachers', function (Blueprint $table) {
            $table->dropColumn('password');
        });
    }
};