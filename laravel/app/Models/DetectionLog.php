<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetectionLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'camera_id',
        'label_id',
        'confidence_score',
        'violation_category',
        'snapshot',
        'created_at',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:3',
        'violation_category' => 'string',
        'snapshot' => 'string',
        'created_at' => 'datetime',
    ];

    protected $appends = [
        'label_detected',
    ];

    protected $with = ['label'];

    public function getLabelDetectedAttribute(): ?string
    {
        return $this->label?->name;
    }

    public function camera(): BelongsTo
    {
        return $this->belongsTo(Camera::class);
    }

    public function label(): BelongsTo
    {
        return $this->belongsTo(DetectionLabel::class, 'label_id');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', Carbon::today());
    }

    public function scopeCritical(Builder $query, float $threshold = 0.85): Builder
    {
        return $query->where('confidence_score', '>', $threshold);
    }

    public function scopeByCamera(Builder $query, string $cameraId): Builder
    {
        return $query->where('camera_id', $cameraId);
    }

    public function scopeByLabel(Builder $query, int $labelId): Builder
    {
        return $query->where('label_id', $labelId);
    }

    public function scopeRecent(Builder $query, int $limit = 30): Builder
    {
        return $query->orderByDesc('created_at')->take($limit);
    }

    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['camera', 'label']);
    }
}
