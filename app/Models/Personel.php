<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class Personel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function competencies()
    {
        return $this->belongsToMany(Competency::class, 'personel_competency')
            ->withPivot([
                'id',
                'certificate_file_path',
                'certificate_file_name',
                'certificate_file_size',
                'certificate_file_status',
                'certificate_file_error',
                'certificate_file_processed_at',
                'issuer',
                'expired_date'
            ])
            ->withTimestamps();
    }

    public function projectPersonels()
    {
        return $this->hasMany(ProjectPersonel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
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
