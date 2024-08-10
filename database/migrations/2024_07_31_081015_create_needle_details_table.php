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
        Schema::create('needle_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('needle_id', 36)->nullable();
            $table->foreign('needle_id')->references('id')->on('needles')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_status_id')->nullable();
            $table->foreign('master_status_id')->references('id')->on('master_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->string('filename')->nullable();
            $table->string('ext')->nullable();
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
        Schema::dropIfExists('needle_details');
    }
};
