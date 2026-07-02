<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DetectionLabel extends Model
{
    protected $fillable = [
        'name',
    ];

    public function detectionLogs(): HasMany
    {
        return $this->hasMany(DetectionLog::class, 'label_id');
    }
}
