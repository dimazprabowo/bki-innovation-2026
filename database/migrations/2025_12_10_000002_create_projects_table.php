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
            $table->text('description')->nullable();

            // Lifecycle status (Draft, Aktif, On Progress, Selesai, Ditutup)
            $table->enum('status', ['draft', 'active', 'on_progress', 'completed', 'closed'])->default('draft');
            // Approval / process status (CoE Review flow)
            $table->enum('approval_status', ['none', 'coe_review', 'approved', 'rejected'])->default('none');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('actual_end_date')->nullable();

            // Derived from selected modules
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->enum('coe_control_level', ['none', 'standard', 'enhanced', 'full'])->default('none');

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
            $table->index('approval_status');
            $table->index('priority');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
