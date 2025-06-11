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
        Schema::create('dead_stocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('master_area_id')->nullable();
            $table->foreign('master_area_id')->references('id')->on('master_areas')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_needle_id')->nullable();
            $table->foreign('master_needle_id')->references('id')->on('master_needles')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('rfid')->nullable();
            $table->bigInteger('in')->default(0);
            $table->bigInteger('out')->default(0);
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
        Schema::dropIfExists('dead_stocks');
    }
};
