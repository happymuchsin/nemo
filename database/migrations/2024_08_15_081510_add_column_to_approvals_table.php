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
        Schema::table('approvals', function (Blueprint $table) {
            $table->string('tipe')->nullable()->after('needle_status');
            $table->unsignedBigInteger('master_needle_id')->nullable()->after('user_id');
            $table->foreign('master_needle_id')->references('id')->on('master_needles')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_line_id')->nullable()->after('master_counter_id');
            $table->foreign('master_line_id')->references('id')->on('master_lines')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_style_id')->nullable()->after('master_line_id');
            $table->foreign('master_style_id')->references('id')->on('master_styles')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('approvals', function (Blueprint $table) {
            $table->dropColumn('tipe');
            $table->dropConstrainedForeignId('master_needle_id');
            $table->dropConstrainedForeignId('master_line_id');
            $table->dropConstrainedForeignId('master_style_id');
        });
    }
};
