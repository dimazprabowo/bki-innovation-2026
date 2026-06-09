<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModuleTool extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module_id',
        'peralatan_id',
        'requires_calibration',
        'quantity',
    ];

    protected $casts = [
        'requires_calibration' => 'boolean',
        'quantity' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function peralatan()
    {
        return $this->belongsTo(Peralatan::class);
    }

    public function getRequiresCalibrationLabelAttribute(): string
    {
        return $this->requires_calibration ? 'Ya' : 'Tidak';
    }
}
