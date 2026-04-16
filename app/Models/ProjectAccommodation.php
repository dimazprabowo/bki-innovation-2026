<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectAccommodation extends Model
{
    use HasFactory;

    protected $table = 'project_accommodations';

    protected $fillable = [
        'project_id',
        'type',
        'description',
        'quantity',
        'unit',
        'estimated_cost',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'estimated_cost' => 'decimal:2',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
