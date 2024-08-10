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
        Schema::create('approvals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->char('needle_id', 36)->nullable();
            $table->foreign('needle_id')->references('id')->on('needles')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('approval_id')->nullable();
            $table->foreign('approval_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_area_id')->nullable();
            $table->foreign('master_area_id')->references('id')->on('master_areas')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_counter_id')->nullable();
            $table->foreign('master_counter_id')->references('id')->on('master_counters')->onUpdate('cascade')->onDelete('cascade');
            $table->string('needle_status')->nullable();
            $table->string('status')->nullable();
            $table->string('remark')->nullable();
            $table->timestamp('approve')->nullable();
            $table->timestamp('reject')->nullable();
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
        Schema::dropIfExists('approvals');
    }
};
