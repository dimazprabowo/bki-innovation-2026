<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peralatan_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peralatan_id')->constrained('peralatans')->onDelete('cascade');
            $table->string('name');
            $table->string('file_path')->nullable();
            $table->string('file_name')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_status')->default('pending')->comment('pending, processing, completed, failed');
            $table->string('file_error')->nullable();
            $table->timestamp('file_processed_at')->nullable();
            $table->timestamps();

            $table->index('peralatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peralatan_evidence');
    }
};
