<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personel_competency', function (Blueprint $table) {
            $table->id();
            $table->foreignId('personel_id')->constrained()->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->string('certificate_file_path')->nullable();
            $table->string('certificate_file_name')->nullable();
            $table->unsignedBigInteger('certificate_file_size')->nullable();
            $table->string('certificate_file_status')->default('pending');
            $table->text('certificate_file_error')->nullable();
            $table->timestamp('certificate_file_processed_at')->nullable();
            $table->string('issuer')->nullable();
            $table->date('expired_date')->nullable();
            $table->timestamps();

            $table->unique(['personel_id', 'competency_id']);
            $table->index('personel_id');
            $table->index('competency_id');
            $table->index('expired_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personel_competency');
    }
};
