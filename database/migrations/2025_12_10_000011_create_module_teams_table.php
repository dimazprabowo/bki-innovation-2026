<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_personels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->string('position_name');
            $table->integer('quantity')->default(1);
            $table->enum('nature', ['mandatory', 'optional'])->default('mandatory');
            $table->timestamps();
            $table->softDeletes();

            $table->index('module_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_personels');
    }
};
