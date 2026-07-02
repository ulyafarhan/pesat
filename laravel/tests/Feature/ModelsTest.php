<?php

declare(strict_types=1);

use App\Models\AdminSetting;
use App\Models\Camera;
use App\Models\CitizenReport;
use App\Models\DetectionLabel;
use App\Models\DetectionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    Carbon::setTestNow(Carbon::create(2026, 6, 19, 12, 0, 0));
});

afterEach(function () {
    Carbon::setTestNow();
});

test('user model has fillable and casts', function () {
    $user = User::create([
        'name' => 'Test User',
        'email' => 'test@pesat.local',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    expect($user->role)->toBe('admin');
    expect($user->name)->toBe('Test User');
});

test('camera model casts and key type', function () {
    $camera = Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Test Location',
        'latitude' => 5.18020000,
        'longitude' => 97.15070000,
        'is_active' => true,
        'stream_source' => 'rtsp://test/stream',
        'edge_device_id' => 'device-1',
    ]);

    expect($camera->getKeyType())->toBe('string');
    expect($camera->incrementing)->toBeFalse();
    expect($camera->is_active)->toBeTrue();
    expect((float) $camera->latitude)->toBe(5.1802);
    expect($camera->edge_device_id)->toBe('device-1');
});

test('camera has many detection logs', function () {
    $camera = Camera::create([
        'id' => 'CAM-001', 'location_name' => 'Test',
        'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true,
    ]);
    $label = DetectionLabel::create(['name' => 'test']);
    $log = DetectionLog::create([
        'camera_id' => 'CAM-001', 'label_id' => $label->id,
        'confidence_score' => 0.9,
    ]);

    expect($camera->detectionLogs)->toHaveCount(1);
    expect($camera->detectionLogs->first()->id)->toBe($log->id);
});

test('camera scopes: byEdgeDevice', function () {
    Camera::create(['id' => 'CAM-A', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);
    Camera::create(['id' => 'CAM-B', 'location_name' => 'B', 'latitude' => 5.19, 'longitude' => 97.16, 'edge_device_id' => 'dev-2', 'is_active' => true]);

    $result = Camera::byEdgeDevice('dev-1')->get();
    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe('CAM-A');
});

test('camera scope byLocation uses LIKE', function () {
    Camera::create(['id' => 'CAM-A', 'location_name' => 'Taman Riyadhah', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    Camera::create(['id' => 'CAM-B', 'location_name' => 'Pantai Ujong Blang', 'latitude' => 5.19, 'longitude' => 97.16, 'is_active' => true]);

    $result = Camera::byLocation('Taman')->get();
    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe('CAM-A');
});

test('detection label has many detection logs', function () {
    $camera = Camera::create(['id' => 'CAM-001', 'location_name' => 'Test', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'anomaly']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.85]);

    expect($label->detectionLogs)->toHaveCount(1);
});

test('detection log accessor label_detected returns label name', function () {
    $camera = Camera::create(['id' => 'CAM-001', 'location_name' => 'Test', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'flood']);
    $log = DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.9]);

    expect($log->label_detected)->toBe('flood');
    expect($log->label->name)->toBe('flood');
});

test('detection log casts confidence_score as decimal', function () {
    $camera = Camera::create(['id' => 'CAM-001', 'location_name' => 'Test', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'flood']);
    $log = DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.9256]);

    expect((float) $log->confidence_score)->toBe(0.926);
});

test('detection log scopes chained: today, critical, byCamera', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Test', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'flood']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.92, 'created_at' => now()]);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.50, 'created_at' => now()]);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.95, 'created_at' => now()->subDays(3)]);

    $result = DetectionLog::today()->critical()->byCamera('CAM-001')->get();
    expect($result)->toHaveCount(1);
});

test('citizen report casts and strings', function () {
    $report = CitizenReport::create([
        'location_name' => 'Test Location',
        'latitude' => '5.1802',
        'longitude' => '97.1507',
        'reported_at' => now(),
        'is_break_dispatch' => true,
        'status' => 'pending',
    ]);

    expect($report->id)->toBeString();
    expect(strlen($report->id))->toBe(26);
    expect($report->is_break_dispatch)->toBeTrue();
    expect($report->latitude)->toBeString();
    expect($report->longitude)->toBeString();
});

test('citizen report scopes: verified, rejected, breakDispatch', function () {
    CitizenReport::create(['location_name' => 'A', 'reported_at' => now(), 'status' => 'verified']);
    CitizenReport::create(['location_name' => 'B', 'reported_at' => now(), 'status' => 'rejected']);
    CitizenReport::create(['location_name' => 'C', 'reported_at' => now(), 'status' => 'pending', 'is_break_dispatch' => true]);
    CitizenReport::create(['location_name' => 'D', 'reported_at' => now(), 'status' => 'pending', 'is_break_dispatch' => false]);

    expect(CitizenReport::verified()->count())->toBe(1);
    expect(CitizenReport::rejected()->count())->toBe(1);
    expect(CitizenReport::breakDispatch()->count())->toBe(1);
    expect(CitizenReport::pending()->count())->toBe(2);
});

test('citizen report belongs to verifier', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    $report = CitizenReport::create([
        'location_name' => 'Test', 'reported_at' => now(),
        'status' => 'verified', 'verified_by' => $user->id,
    ]);

    expect($report->verifier->name)->toBe('WH');
    expect($report->verifier->role)->toBe('wh_officer');
});

test('admin setting key-value model', function () {
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    $setting = AdminSetting::where('key', 'break_start_time')->first();
    expect($setting->value)->toBe('12:00');
});
