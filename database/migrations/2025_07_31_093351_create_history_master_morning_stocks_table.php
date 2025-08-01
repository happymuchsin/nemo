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
        Schema::create('history_master_morning_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reff_id')->nullable();
            $table->foreign('reff_id')->references('id')->on('master_morning_stocks')->onDelete('cascade');
            $table->unsignedBigInteger('master_needle_id')->nullable();
            $table->foreign('master_needle_id')->references('id')->on('master_needles')->onDelete('cascade');
            $table->string('value')->nullable();
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
        Schema::dropIfExists('history_master_morning_stocks');
    }
};
