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
        Schema::create('detail_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('adjustment_id', 36)->nullable();
            $table->foreign('adjustment_id')->references('id')->on('adjustments')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_area_id')->nullable();
            $table->foreign('master_area_id')->references('id')->on('master_areas')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_counter_id')->nullable();
            $table->foreign('master_counter_id')->references('id')->on('master_counters')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_box_id')->nullable();
            $table->foreign('master_box_id')->references('id')->on('master_boxes')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_needle_id')->nullable();
            $table->foreign('master_needle_id')->references('id')->on('master_needles')->onUpdate('cascade')->onDelete('cascade');
            $table->string('before')->nullable();
            $table->string('after')->nullable();
            $table->string('remark')->nullable();
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
        Schema::dropIfExists('detail_adjustments');
    }
};
