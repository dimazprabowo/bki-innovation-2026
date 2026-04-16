<?php

namespace App\Models;

use App\Enums\CoEControlLevel;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'scope',
        'method',
        'duration',
        'deliverable',
        'risk_level',
        'coe_control_level',
        'status',
        'created_by',
        'approved_by',
        'submitted_at',
        'approved_at',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'risk_level' => RiskLevel::class,
        'coe_control_level' => CoEControlLevel::class,
        'status' => ProjectStatus::class,
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'project_modules')
            ->withPivot(['quantity', 'unit_price', 'subtotal', 'notes'])
            ->withTimestamps();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function resources()
    {
        return $this->belongsToMany(User::class, 'project_resources')
            ->withTimestamps();
    }

    public function equipments()
    {
        return $this->hasMany(ProjectEquipment::class);
    }

    public function accommodations()
    {
        return $this->hasMany(ProjectAccommodation::class);
    }

    public function getTotalEstimateAttribute()
    {
        return $this->modules->sum('pivot.subtotal') ?? 0;
    }

    public function requiresCoEControl(): bool
    {
        return $this->risk_level === RiskLevel::High;
    }

    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    public function scopeByRiskLevel($query, $riskLevel)
    {
        if ($riskLevel) {
            return $query->where('risk_level', $riskLevel);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('scope', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeRequiresCoE($query)
    {
        return $query->where('risk_level', RiskLevel::High->value);
    }
}
