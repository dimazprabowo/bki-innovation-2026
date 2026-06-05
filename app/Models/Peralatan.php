<?php

namespace App\Models;

use App\Enums\CalibrationStatus;
use App\Enums\EquipmentCondition;
use App\Enums\OwnershipStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Peralatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'peralatans';

    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
        'calibration_status',
        'calibration_expired_date',
        'condition',
        'ownership_status',
        'is_active',
    ];

    protected $casts = [
        'calibration_status' => CalibrationStatus::class,
        'condition' => EquipmentCondition::class,
        'ownership_status' => OwnershipStatus::class,
        'calibration_expired_date' => 'date',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function evidences(): HasMany
    {
        return $this->hasMany(PeralatanEvidence::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeByCalibrationStatus($query, $status)
    {
        if ($status) {
            return $query->where('calibration_status', $status);
        }
        return $query;
    }

    public function scopeByCondition($query, $condition)
    {
        if ($condition) {
            return $query->where('condition', $condition);
        }
        return $query;
    }

    public function scopeByOwnershipStatus($query, $status)
    {
        if ($status) {
            return $query->where('ownership_status', $status);
        }
        return $query;
    }

    // Accessors
    public function getIsActiveAttribute(): bool
    {
        return $this->attributes['is_active'] ?? true;
    }

    public function getCalibrationStatusExpiredAttribute(): bool
    {
        if (!$this->calibration_expired_date) {
            return false;
        }
        return $this->calibration_expired_date->isPast();
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }
}
