<?php

namespace App\Events;

use App\Models\DetectionLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewDetectionTriggered implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $log;

    public function __construct(DetectionLog $log)
    {
        $this->log = $log->load(['camera', 'label']);
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('pesat-telemetry'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'telemetry.updated';
    }
}
