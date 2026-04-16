<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('scope')->nullable();
            $table->string('method')->nullable();
            $table->text('resource')->nullable();
            $table->string('duration')->nullable();
            $table->text('deliverable')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->decimal('pricing_baseline', 15, 2)->nullable();
            $table->enum('coe_control_level', ['none', 'standard', 'enhanced', 'full'])->default('none');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('risk_level');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};
