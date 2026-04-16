<?php

namespace App\Models;

use App\Enums\CoEControlLevel;
use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'scope',
        'method',
        'resource',
        'duration',
        'deliverable',
        'risk_level',
        'pricing_baseline',
        'coe_control_level',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'risk_level' => RiskLevel::class,
        'coe_control_level' => CoEControlLevel::class,
        'pricing_baseline' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_modules')
            ->withPivot(['quantity', 'unit_price', 'subtotal', 'notes'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
}
