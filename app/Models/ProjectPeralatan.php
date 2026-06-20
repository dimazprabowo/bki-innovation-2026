<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPeralatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'module_id',
        'module_tool_id',
        'peralatan_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(ModuleTool::class, 'module_tool_id');
    }

    public function peralatan(): BelongsTo
    {
        return $this->belongsTo(Peralatan::class);
    }
}
