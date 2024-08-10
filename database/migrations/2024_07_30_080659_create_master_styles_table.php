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
        Schema::create('master_styles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_buyer_id')->nullable();
            $table->foreign('master_buyer_id')->references('id')->on('master_buyers')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_category_id')->nullable();
            $table->foreign('master_category_id')->references('id')->on('master_categories')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_sample_id')->nullable();
            $table->foreign('master_sample_id')->references('id')->on('master_samples')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_fabric_id')->nullable();
            $table->foreign('master_fabric_id')->references('id')->on('master_fabrics')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name')->nullable();
            $table->date('start')->nullable();
            $table->date('end')->nullable();
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
        Schema::dropIfExists('master_styles');
    }
};
