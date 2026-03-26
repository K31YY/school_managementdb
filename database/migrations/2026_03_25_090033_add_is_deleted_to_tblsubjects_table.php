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
    Schema::table('tblsubjects', function (Blueprint $table) {
        // Add the missing column here
        $table->tinyInteger('IsDeleted')->default(0)->after('Description');
    });
}

/**
 * Reverse the migrations.
 */
public function down(): void
{
    Schema::table('tblsubjects', function (Blueprint $table) {
        // Drop the column if we rollback the migration
        $table->dropColumn('IsDeleted');
    });
}
};
