<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module_tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('peralatan_id')->nullable()->constrained('peralatans')->onDelete('set null');
            $table->boolean('requires_calibration')->default(false);
            $table->integer('quantity')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->index('module_id');
            $table->index('peralatan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module_tools');
    }
};
