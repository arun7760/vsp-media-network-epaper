<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EpaperWorkflowLog extends Model
{
    use HasFactory;

    protected $table = 'epaper_workflow_logs';

    protected $fillable = [
        'edition_id',
        'user_id',
        'action',
        'from_status',
        'to_status',
        'notes',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(EpaperEdition::class, 'edition_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
