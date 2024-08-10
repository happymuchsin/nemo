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
        Schema::create('master_boxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_counter_id')->nullable();
            $table->foreign('master_counter_id')->references('id')->on('master_counters')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('rfid')->nullable();
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
        Schema::dropIfExists('master_boxes');
    }
};
