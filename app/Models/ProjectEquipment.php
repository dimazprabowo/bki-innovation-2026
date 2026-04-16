<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectEquipment extends Model
{
    use HasFactory;

    protected $table = 'project_equipments';

    protected $fillable = [
        'project_id',
        'name',
        'specification',
        'quantity',
        'unit',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
