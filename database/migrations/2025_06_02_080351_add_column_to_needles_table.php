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
        Schema::table('needles', function (Blueprint $table) {
            $table->timestamp('scan_rfid')->nullable()->after('master_status_id');
            $table->timestamp('scan_box')->nullable()->after('scan_rfid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('needles', function (Blueprint $table) {
            $table->dropColumn('scan_rfid');
            $table->dropColumn('scan_box');
        });
    }
};
