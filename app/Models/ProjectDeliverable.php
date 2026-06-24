<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDeliverable extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'module_id',
        'module_deliverable_id',
        'file_path',
        'file_name',
        'file_size',
        'file_status',
        'file_processed_at',
        'file_error',
        'uploaded_by',
        'notes',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function moduleDeliverable(): BelongsTo
    {
        return $this->belongsTo(ModuleDeliverable::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
