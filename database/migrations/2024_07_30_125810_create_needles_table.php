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
        Schema::create('needles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_line_id')->nullable();
            $table->foreign('master_line_id')->references('id')->on('master_lines')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_style_id')->nullable();
            $table->foreign('master_style_id')->references('id')->on('master_styles')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_box_id')->nullable();
            $table->foreign('master_box_id')->references('id')->on('master_boxes')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_needle_id')->nullable();
            $table->foreign('master_needle_id')->references('id')->on('master_needles')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_status_id')->nullable();
            $table->foreign('master_status_id')->references('id')->on('master_statuses')->onUpdate('cascade')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->text('remark')->nullable();
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
        Schema::dropIfExists('needles');
    }
};
