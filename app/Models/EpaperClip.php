<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpaperClip extends Model
{
    use HasFactory;

    protected $table = 'epaper_clips';

    protected $fillable = [
        'page_id',
        'slug',
        'title',
        'box_x',
        'box_y',
        'box_width',
        'box_height',
        'clip_image_path',
        'article_text',
        'views_count',
    ];

    protected $casts = [
        'box_x' => 'float',
        'box_y' => 'float',
        'box_width' => 'float',
        'box_height' => 'float',
        'views_count' => 'integer',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(EpaperPage::class, 'page_id');
    }
}
