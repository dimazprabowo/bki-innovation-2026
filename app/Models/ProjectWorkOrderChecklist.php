<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectWorkOrderChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'module_id',
        'work_order_item_id',
        'work_order_subitem_id',
        'is_checked',
        'checked_by',
        'checked_at',
        'notes',
    ];

    protected $casts = [
        'is_checked' => 'boolean',
        'checked_at' => 'datetime',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function workOrderItem(): BelongsTo
    {
        return $this->belongsTo(WorkOrderItem::class);
    }

    public function workOrderSubitem(): BelongsTo
    {
        return $this->belongsTo(WorkOrderSubitem::class);
    }

    public function checker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }
}
