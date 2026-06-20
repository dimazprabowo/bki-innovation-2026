<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulePersonel extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'module_personels';

    protected $fillable = [
        'module_id',
        'position_name',
        'quantity',
        'nature',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'module_personel_competencies');
    }

    public function getNatureLabelAttribute(): string
    {
        return $this->nature === 'mandatory' ? 'Wajib' : 'Opsional';
    }
}
