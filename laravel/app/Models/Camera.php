<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Camera extends Model
{
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'location_name',
        'latitude',
        'longitude',
        'is_active',
        'stream_source',
        'edge_device_id',
        'last_heartbeat_at',
        'edge_metrics',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'last_heartbeat_at' => 'datetime',
        'edge_metrics' => 'array',
    ];

    public function detectionLogs(): HasMany
    {
        return $this->hasMany(DetectionLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeByEdgeDevice(Builder $query, string $deviceId): Builder
    {
        return $query->where('edge_device_id', $deviceId);
    }

    public function scopeByLocation(Builder $query, string $name): Builder
    {
        return $query->where('location_name', 'like', "%{$name}%");
    }
}
