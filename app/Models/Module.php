<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'duration',
        'risk_level',
        'pricing_baseline',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'risk_level' => RiskLevel::class,
        'pricing_baseline' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_modules')
            ->withPivot(['quantity', 'unit_price', 'subtotal', 'notes'])
            ->withTimestamps();
    }

    public function workOrderItems()
    {
        return $this->hasMany(WorkOrderItem::class)->orderBy('order');
    }

    public function workOrderReferences()
    {
        return $this->hasMany(WorkOrderReference::class);
    }

    public function personels()
    {
        return $this->hasMany(ModulePersonel::class);
    }

    public function tools()
    {
        return $this->hasMany(ModuleTool::class);
    }

    public function deliverables()
    {
        return $this->hasMany(ModuleDeliverable::class)->orderBy('order');
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
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        return $query;
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
