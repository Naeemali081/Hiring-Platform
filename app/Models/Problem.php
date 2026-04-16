<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Problem extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'difficulty',
        'role_level',
        'suggested_time_minutes',
        'tags',
        'is_public',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'is_public' => 'boolean',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }
}
