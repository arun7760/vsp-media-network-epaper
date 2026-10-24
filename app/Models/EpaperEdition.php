<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class EpaperEdition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'epaper_editions';

    protected $fillable = [
        'region_id',
        'edition_date',
        'edition_code',
        'title',
        'title_en',
        'volume_number',
        'issue_number',
        'total_pages',
        'master_pdf_path',
        'master_pdf_size',
        'cover_image_path',
        'cover_thumbnail_path',
        'status',
        'scheduled_at',
        'published_at',
        'archived_at',
        'views_count',
        'downloads_count',
        'created_by',
        'approved_by',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'edition_date' => 'date',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
        'total_pages' => 'integer',
        'master_pdf_size' => 'integer',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
    ];

    public function region(): BelongsTo
    {
        return $this->belongsTo(EpaperRegion::class, 'region_id');
    }

    public function pages(): HasMany
    {
        return $this->hasMany(EpaperPage::class, 'edition_id')->orderBy('page_number');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function workflowLogs(): HasMany
    {
        return $this->hasMany(EpaperWorkflowLog::class, 'edition_id')->latest();
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('edition_date', $date);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('edition_date', '>=', now()->subDays($days));
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->published_at && $this->published_at->isPast();
    }

    public function getCoverUrlAttribute(): string
    {
        if ($this->cover_image_path) {
            if (str_starts_with($this->cover_image_path, 'http://') || str_starts_with($this->cover_image_path, 'https://')) {
                return $this->cover_image_path;
            }
            return '/' . ltrim($this->cover_image_path, '/');
        }

        $firstPage = $this->pages->first();
        if ($firstPage && $firstPage->image_path) {
            return $firstPage->image_url;
        }

        return '/images/epaper-default-cover.png';
    }

    public function getThumbnailUrlAttribute(): string
    {
        if ($this->cover_thumbnail_path) {
            if (str_starts_with($this->cover_thumbnail_path, 'http://') || str_starts_with($this->cover_thumbnail_path, 'https://')) {
                return $this->cover_thumbnail_path;
            }
            return '/' . ltrim($this->cover_thumbnail_path, '/');
        }

        return $this->cover_url;
    }

    public function getMasterPdfUrlAttribute(): ?string
    {
        if ($this->master_pdf_path) {
            if (str_starts_with($this->master_pdf_path, 'http://') || str_starts_with($this->master_pdf_path, 'https://')) {
                return $this->master_pdf_path;
            }
            return '/' . ltrim($this->master_pdf_path, '/');
        }

        return null;
    }

    public function getTeluguDateStringAttribute(): string
    {
        if (! $this->edition_date) {
            return '';
        }

        $days = [
            'Sunday' => 'ఆదివారం',
            'Monday' => 'సోమవారం',
            'Tuesday' => 'మంగళవారం',
            'Wednesday' => 'బుధవారం',
            'Thursday' => 'గురువారం',
            'Friday' => 'శుక్రవారం',
            'Saturday' => 'శనివారం',
        ];

        $months = [
            1 => 'జనవరి', 2 => 'ఫిబ్రవరి', 3 => 'మార్చి', 4 => 'ఏప్రిల్',
            5 => 'మే', 6 => 'జూన్', 7 => 'జూలై', 8 => 'ఆగస్టు',
            9 => 'సెప్టెంబర్', 10 => 'అక్టోబర్', 11 => 'నవంబర్', 12 => 'డిసెంబర్',
        ];

        $dayName = $days[$this->edition_date->format('l')] ?? '';
        $monthName = $months[$this->edition_date->month] ?? '';

        return "{$dayName}, {$this->edition_date->day} {$monthName} {$this->edition_date->year}";
    }

    public function recordWorkflow(string $action, ?string $fromStatus, ?string $toStatus, ?string $notes = null, ?int $userId = null): void
    {
        $this->workflowLogs()->create([
            'user_id' => $userId ?: auth()->id(),
            'action' => $action,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'notes' => $notes,
        ]);
    }
}
