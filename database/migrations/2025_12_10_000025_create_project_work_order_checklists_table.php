<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_work_order_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('module_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_order_item_id')->constrained()->onDelete('cascade');
            $table->foreignId('work_order_subitem_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_checked')->default(false);
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'work_order_item_id', 'work_order_subitem_id'], 'powoc_unique');
            $table->index('project_id');
            $table->index('module_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_work_order_checklists');
    }
};
