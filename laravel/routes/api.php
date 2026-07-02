<?php

use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\Api\EdgeApiController;
use App\Http\Controllers\Api\TelemetryApiController;
use App\Http\Controllers\CitizenReportController;
use App\Models\Camera;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:500,1')->group(function () {
    Route::post('/telemetry/log', [TelemetryApiController::class, 'store']);
    Route::get('/telemetry/latest', [TelemetryApiController::class, 'latest']);

    Route::get('/edge/cameras', [EdgeApiController::class, 'cameras']);
    Route::post('/edge/heartbeat', [EdgeApiController::class, 'heartbeat']);

    Route::post('/reports', [CitizenReportController::class, 'store']);
    Route::get('/reports/latest', [CitizenReportController::class, 'latest']);
    Route::get('/wh/reports', [CitizenReportController::class, 'getPendingWH']);
    Route::post('/wh/reports/{id}/verify', [CitizenReportController::class, 'verifyReport']);
    Route::get('/admin/settings', [AdminSettingController::class, 'getSettings']);
    Route::post('/admin/settings', [AdminSettingController::class, 'updateSettings']);

    Route::get('/cameras/{id}', function (string $id) {
        $camera = Camera::find($id);
        if (! $camera) {
            return response()->json(['error' => 'Camera not found'], 404);
        }

        return response()->json($camera);
    });
});


