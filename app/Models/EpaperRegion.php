<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EpaperRegion extends Model
{
    use HasFactory;

    protected $table = 'epaper_regions';

    protected $fillable = [
        'name',
        'name_en',
        'slug',
        'code',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'display_order' => 'integer',
    ];

    public function editions(): HasMany
    {
        return $this->hasMany(EpaperEdition::class, 'region_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }

    public function getDisplayNameAttribute(): string
    {
        if (app()->getLocale() === 'en' && ! empty($this->name_en)) {
            return $this->name_en;
        }

        return $this->name;
    }
}
