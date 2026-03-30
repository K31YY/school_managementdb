<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tblrooms', function (Blueprint $table) {
            // Adds IsDeleted as a boolean, default is 0 (not deleted)
            $table->tinyInteger('IsDeleted')->default(0)->after('Status');
        });
    }

    public function down(): void
    {
        Schema::table('tblrooms', function (Blueprint $table) {
            $table->dropColumn('IsDeleted');
        });
    }
};