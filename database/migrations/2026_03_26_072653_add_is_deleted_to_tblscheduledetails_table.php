<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
    Schema::table('tblscheduledetails', function (Blueprint $table) {
        // Adding the column as a tinyInteger (0 or 1)
        // We place it after 'EndTime' based on your table structure
        $table->tinyInteger('IsDeleted')->default(0)->after('EndTime');
    });
    }

    public function down()
    {
    Schema::table('tblscheduledetails', function (Blueprint $table) {
        $table->dropColumn('IsDeleted');
    }); 
    }
};
