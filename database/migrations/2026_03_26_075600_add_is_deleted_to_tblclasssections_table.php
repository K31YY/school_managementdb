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
    Schema::table('tblclasssections', function (Blueprint $table) {
        // Adding the column after YearID
        $table->tinyInteger('IsDeleted')->default(0)->after('YearID');
    });
    }

    public function down(): void
    {
    Schema::table('tblclasssections', function (Blueprint $table) {
        $table->dropColumn('IsDeleted');
    });
    }
};
