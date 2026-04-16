<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('scope')->nullable();
            $table->string('method')->nullable();
            $table->text('resource')->nullable();
            $table->string('duration')->nullable();
            $table->text('deliverable')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->enum('coe_control_level', ['none', 'standard', 'enhanced', 'full'])->default('none');
            $table->enum('status', ['draft', 'coe_review', 'approved', 'rejected', 'stopped'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('code');
            $table->index('risk_level');
            $table->index('status');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
