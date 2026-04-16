<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'code',
        'language',
        'execution_result',
        'manual_chars',
        'pasted_chars',
        'paste_events',
        'tab_switches',
        'tab_switch_timestamps',
        'keystrokes_log',
        'score',
        'reviewer_notes',
        'defend_outcome',
    ];

    protected function casts(): array
    {
        return [
            'execution_result' => 'array',
            'tab_switch_timestamps' => 'array',
            'keystrokes_log' => 'array',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }
}
