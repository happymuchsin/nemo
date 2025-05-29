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
        Schema::create('history_out_stocks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('stock_id', 36)->nullable();
            $table->foreign('stock_id')->references('id')->on('stocks')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('stock_before')->nullable();
            $table->bigInteger('qty')->nullable();
            $table->bigInteger('stock_after')->nullable();
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
        Schema::dropIfExists('history_out_stocks');
    }
};
