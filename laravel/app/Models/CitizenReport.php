<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\CitizenReportStatusUpdated;
use App\Events\NewCitizenReportSubmitted;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CitizenReport extends Model
{
    use HasUlids;

    protected $fillable = [
        'location_name',
        'latitude',
        'longitude',
        'reported_at',
        'media_path',
        'is_break_dispatch',
        'status',
        'verified_by',
        'verification_notes',
        'source',
        'violation_category',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'is_break_dispatch' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::created(function (CitizenReport $report): void {
            event(new NewCitizenReportSubmitted($report));
        });

        static::updated(function (CitizenReport $report): void {
            event(new CitizenReportStatusUpdated($report));
        });
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeBreakDispatch(Builder $query): Builder
    {
        return $query->where('is_break_dispatch', true);
    }

    public function scopeByLocation(Builder $query, string $name): Builder
    {
        return $query->where(function (Builder $q) use ($name): void {
            $q->where('location_name', $name)
                ->orWhere('location_name', 'like', $name.' - %');
        });
    }
}
