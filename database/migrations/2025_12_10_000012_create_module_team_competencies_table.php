<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_personel_competencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_personel_id')->constrained('module_personels')->onDelete('cascade');
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['module_personel_id', 'competency_id']);
            $table->index('module_personel_id');
            $table->index('competency_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_personel_competencies');
    }
};
