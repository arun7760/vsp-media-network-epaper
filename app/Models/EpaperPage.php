<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EpaperPage extends Model
{
    use HasFactory;

    protected $table = 'epaper_pages';

    protected $fillable = [
        'edition_id',
        'page_number',
        'page_title',
        'page_title_en',
        'image_path',
        'thumbnail_path',
        'page_pdf_path',
        'width',
        'height',
        'file_size',
        'status',
        'error_message',
        'sort_order',
    ];

    protected $casts = [
        'page_number' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'file_size' => 'integer',
        'sort_order' => 'integer',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(EpaperEdition::class, 'edition_id');
    }

    public function clips(): HasMany
    {
        return $this->hasMany(EpaperClip::class, 'page_id');
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return str_starts_with($this->image_path, 'http')
                ? $this->image_path
                : asset($this->image_path);
        }

        return asset('images/epaper-page-placeholder.png');
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->thumbnail_path) {
            return str_starts_with($this->thumbnail_path, 'http')
                ? $this->thumbnail_path
                : asset($this->thumbnail_path);
        }

        return $this->image_url;
    }

    public function getPagePdfUrlAttribute(): ?string
    {
        if (! $this->page_pdf_path) {
            return null;
        }

        return str_starts_with($this->page_pdf_path, 'http')
            ? $this->page_pdf_path
            : asset($this->page_pdf_path);
    }
}
