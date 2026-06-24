<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_deliverables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_deliverable_id')->constrained()->onDelete('cascade');
            $table->string('file_path')->default('');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('file_status')->default('completed');
            $table->timestamp('file_processed_at')->nullable();
            $table->text('file_error')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('project_id');
            $table->index('module_id');
            $table->index('module_deliverable_id');
            $table->index('file_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_deliverables');
    }
};
