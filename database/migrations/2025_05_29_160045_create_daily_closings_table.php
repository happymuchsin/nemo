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
        Schema::create('daily_closings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('master_needle_id')->nullable();
            $table->foreign('master_needle_id')->references('id')->on('master_needles')->onUpdate('cascade')->onDelete('cascade');
            $table->date('tanggal')->nullable();
            $table->string('opening')->nullable();
            $table->string('in')->nullable();
            $table->string('out')->nullable();
            $table->string('closing')->nullable();
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closings');
    }
};
