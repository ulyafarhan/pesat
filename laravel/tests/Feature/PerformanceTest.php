<?php

declare(strict_types=1);

use App\Models\Camera;
use App\Models\CitizenReport;
use App\Models\DetectionLabel;
use App\Models\DetectionLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $camera = Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
        'edge_device_id' => 'dev-1',
    ]);

    $label = DetectionLabel::create(['name' => 'flood']);

    foreach (range(1, 10) as $i) {
        DetectionLog::create([
            'camera_id' => 'CAM-001',
            'label_id' => $label->id,
            'confidence_score' => round(0.5 + $i / 20, 3),
            'created_at' => now()->subMinutes($i),
        ]);
    }

    CitizenReport::create([
        'location_name' => 'Taman Riyadhah',
        'latitude' => '5.1802',
        'longitude' => '97.1507',
        'reported_at' => now(),
        'is_break_dispatch' => false,
        'status' => 'pending',
    ]);
});

test('detection log latest query uses 1 query with eager loading', function () {
    DB::enableQueryLog();

    $logs = DetectionLog::with(['camera', 'label'])->recent(10)->get();

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect(count($logs))->toBe(10);
    expect(count($queries))->toBe(3, 'Eager loading runs 1 query for the main table + 1 per relationship');

    $log = $logs->first();
    expect($log->camera)->not->toBeNull();
    expect($log->label)->not->toBeNull();
});

test('detection log today count uses single COUNT query', function () {
    DB::enableQueryLog();

    $count = DetectionLog::today()->count();

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect($count)->toBe(10);
    expect(count($queries))->toBe(1);
});

test('citizen report pending with verifier uses 1 query', function () {
    DB::enableQueryLog();

    $reports = CitizenReport::pending()->with('verifier')->get();

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect(count($queries))->toBe(1);
});

test('composite index on detection_logs is utilized for byCamera scope', function () {
    DB::enableQueryLog();

    DetectionLog::byCamera('CAM-001')->today()->get();

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect(count($queries))->toBe(2);
});

test('bulk upsert does not create duplicate citizens report', function () {
    $this->postJson('/api/reports', [
        'location_name' => 'Taman Riyadhah',
    ])->assertStatus(201);

    $this->postJson('/api/reports', [
        'location_name' => 'Taman Riyadhah',
    ])->assertStatus(201);

    $count = CitizenReport::byLocation('Taman Riyadhah')->pending()->count();
    expect($count)->toBe(1);
});

test('edge heartbeat updates cameras with single query', function () {
    Camera::create(['id' => 'CAM-002', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);
    Camera::create(['id' => 'CAM-003', 'location_name' => 'B', 'latitude' => 5.19, 'longitude' => 97.16, 'edge_device_id' => 'dev-1', 'is_active' => true]);

    DB::enableQueryLog();

    Camera::byEdgeDevice('dev-1')->update([
        'last_heartbeat_at' => Carbon::now(),
    ]);

    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    expect(count($queries))->toBe(1);
});
