<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_personels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_personel_id')->constrained('module_personels')->cascadeOnDelete();
            $table->foreignId('personel_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // A personel can only be assigned once per personel position in a project
            $table->unique(['project_id', 'module_personel_id', 'personel_id'], 'project_personel_personel_unique');
            $table->index('project_id');
            $table->index('module_personel_id');
            $table->index('personel_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_personels');
    }
};
