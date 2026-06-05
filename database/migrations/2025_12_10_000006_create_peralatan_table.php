<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peralatans', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->enum('calibration_status', ['calibrated', 'expired', 'pending', 'not_required'])->default('not_required');
            $table->date('calibration_expired_date')->nullable();
            $table->enum('condition', ['suitable', 'not_suitable'])->default('suitable');
            $table->enum('ownership_status', ['owned', 'rented', 'borrowed', 'leased'])->default('owned');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('is_active');
            $table->index('calibration_status');
            $table->index('condition');
            $table->index('ownership_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peralatans');
    }
};
