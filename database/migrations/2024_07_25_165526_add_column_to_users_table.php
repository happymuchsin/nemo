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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('master_division_id')->nullable();
            $table->foreign('master_division_id')->references('id')->on('master_divisions')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_position_id')->nullable();
            $table->foreign('master_position_id')->references('id')->on('master_positions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('master_division_id');
            $table->dropConstrainedForeignId('master_position_id');
        });
    }
};
