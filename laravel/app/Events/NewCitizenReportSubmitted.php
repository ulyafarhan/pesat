<?php

namespace App\Events;

use App\Models\CitizenReport;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewCitizenReportSubmitted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;

    public function __construct(CitizenReport $report)
    {
        $this->report = $report;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('pesat-reports'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'report.submitted';
    }
}
