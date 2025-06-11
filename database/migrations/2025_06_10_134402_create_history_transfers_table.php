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
        Schema::create('history_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('dari')->nullable();
            $table->string('dari_id')->nullable();
            $table->string('ke')->nullable();
            $table->string('ke_id')->nullable();
            $table->bigInteger('qty')->nullable();
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
        Schema::dropIfExists('history_transfers');
    }
};
