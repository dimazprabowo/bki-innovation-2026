<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectPersonel extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'module_id',
        'module_personel_id',
        'personel_id',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function personelSlot(): BelongsTo
    {
        return $this->belongsTo(ModulePersonel::class, 'module_personel_id');
    }

    public function personel(): BelongsTo
    {
        return $this->belongsTo(Personel::class);
    }
}
