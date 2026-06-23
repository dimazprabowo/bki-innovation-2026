<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_peralatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_tool_id')->constrained()->cascadeOnDelete();
            $table->foreignId('peralatan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('slot')->default(1);
            $table->timestamps();

            $table->unique(['project_id', 'module_tool_id', 'slot'], 'project_tool_slot_unique');
            $table->index('project_id');
            $table->index('module_tool_id');
            $table->index('peralatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_peralatans');
    }
};
