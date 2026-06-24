<?php

namespace App\Models;

use App\Enums\ApprovalStatus;
use App\Enums\CoEControlLevel;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'description',
        'status',
        'approval_status',
        'priority',
        'start_date',
        'end_date',
        'actual_end_date',
        'risk_level',
        'coe_control_level',
        'created_by',
        'approved_by',
        'submitted_at',
        'approved_at',
        'notes',
        'rejection_reason',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'approval_status' => ApprovalStatus::class,
        'priority' => ProjectPriority::class,
        'risk_level' => RiskLevel::class,
        'coe_control_level' => CoEControlLevel::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'actual_end_date' => 'date',
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

    public function personels()
    {
        return $this->belongsToMany(Personel::class, 'project_personels')
            ->withPivot(['module_id', 'module_personel_id'])
            ->withTimestamps();
    }

    public function projectPersonels()
    {
        return $this->hasMany(ProjectPersonel::class);
    }

    public function additionalCosts()
    {
        return $this->hasMany(ProjectAdditionalCost::class);
    }

    public function projectPeralatans()
    {
        return $this->hasMany(ProjectPeralatan::class);
    }

    public function peralatans()
    {
        return $this->belongsToMany(Peralatan::class, 'project_peralatans')
            ->withPivot(['module_id', 'module_tool_id', 'slot'])
            ->withTimestamps();
    }

    public function workOrderChecklists()
    {
        return $this->hasMany(ProjectWorkOrderChecklist::class);
    }

    public function projectDeliverables()
    {
        return $this->hasMany(ProjectDeliverable::class);
    }

    public function getBaseCostAttribute(): float
    {
        return (float) ($this->modules->sum('pivot.subtotal') ?? 0);
    }

    public function getAdditionalCostTotalAttribute(): float
    {
        return (float) ($this->additionalCosts->sum('amount') ?? 0);
    }

    public function getTotalCostAttribute(): float
    {
        return $this->base_cost + $this->additional_cost_total;
    }

    public function getTotalEstimateAttribute(): float
    {
        return $this->total_cost;
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

    public function scopeByApprovalStatus($query, $approvalStatus)
    {
        if ($approvalStatus) {
            return $query->where('approval_status', $approvalStatus);
        }
        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    public function scopeRequiresCoE($query)
    {
        return $query->where('risk_level', RiskLevel::High->value);
    }

    public function getRouteKey()
    {
        return Crypt::encryptString($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        try {
            $decryptedId = Crypt::decryptString($value);
            return $this->where($this->getRouteKeyName(), $decryptedId)->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }
    }
}
