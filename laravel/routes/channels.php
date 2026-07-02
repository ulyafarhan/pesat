<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('pesat-telemetry', function ($user) {
    return in_array($user->role, ['admin', 'wh_officer']);
});

Broadcast::channel('pesat-reports', function ($user) {
    return in_array($user->role, ['admin', 'wh_officer']);
});
