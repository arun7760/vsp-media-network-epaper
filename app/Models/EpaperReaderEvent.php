<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpaperReaderEvent extends Model
{
    use HasFactory;

    protected $table = 'epaper_reader_events';

    protected $fillable = [
        'edition_id',
        'visitor_token',
        'event_type',
        'page_number',
        'event_date',
        'device_type',
        'traffic_source',
        'referrer_host',
    ];

    protected $casts = [
        'event_date' => 'date',
        'page_number' => 'integer',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(EpaperEdition::class, 'edition_id');
    }
}
