<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeralatanEvidence extends Model
{
    use HasFactory;

    protected $fillable = [
        'peralatan_id',
        'name',
        'file_path',
        'file_name',
        'file_size',
        'file_status',
        'file_error',
        'file_processed_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'file_processed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function peralatan(): BelongsTo
    {
        return $this->belongsTo(Peralatan::class);
    }
}
