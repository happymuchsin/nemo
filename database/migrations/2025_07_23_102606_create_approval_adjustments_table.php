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
        Schema::create('approval_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('tanggal')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedBigInteger('master_approval_id')->nullable();
            $table->foreign('master_approval_id')->references('id')->on('master_approvals')->onUpdate('cascade')->onDelete('cascade');
            $table->string('tipe')->nullable();
            $table->string('reff_id')->nullable();
            $table->string('status')->nullable();
            $table->string('remark')->nullable();
            $table->timestamp('approve')->nullable();
            $table->timestamp('reject')->nullable();
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
        Schema::dropIfExists('approval_adjustments');
    }
};
