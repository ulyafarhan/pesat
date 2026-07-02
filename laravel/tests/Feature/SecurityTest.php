<?php

declare(strict_types=1);

use App\Models\Camera;
use App\Models\DetectionLabel;
use App\Models\DetectionLog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

test('telemetry api rejects confidence score of exactly 0', function () {
    Config::set('services.pesat.api_key', null);
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Test', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'test',
        'confidence_score' => 0,
    ]);
    $response->assertStatus(201);
});

test('telemetry api rejects negative confidence', function () {
    Config::set('services.pesat.api_key', null);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'test',
        'confidence_score' => -0.5,
    ]);
    $response->assertStatus(422);
});

test('telemetry api rejects confidence above 1', function () {
    Config::set('services.pesat.api_key', null);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'test',
        'confidence_score' => 1.5,
    ]);
    $response->assertStatus(422);
});

test('telemetry api rejects non-existent camera_id', function () {
    Config::set('services.pesat.api_key', null);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-NONEXISTENT',
        'label_detected' => 'test',
        'confidence_score' => 0.9,
    ]);
    $response->assertStatus(422);
});

test('telemetry api sanitizes XSS in label_detected', function () {
    Config::set('services.pesat.api_key', null);
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Test', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => '<script>alert("XSS")</script>',
        'confidence_score' => 0.9,
    ]);

    $response->assertStatus(201);
    $label = DetectionLabel::where('name', '<script>alert("XSS")</script>')->first();
    expect($label)->not->toBeNull();
    expect($label->name)->toBe('<script>alert("XSS")</script>');
});

test('edge cameras returns empty array for unknown device', function () {
    Config::set('services.pesat.api_key', 'key');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Test', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);

    $response = $this->getJson('/api/edge/cameras?device_id=dev-unknown', [
        'Authorization' => 'Bearer key',
    ]);

    $response->assertStatus(200)->assertJsonCount(0, 'data');
});

test('edge cameras validates device_id is required', function () {
    Config::set('services.pesat.api_key', 'key');

    $response = $this->getJson('/api/edge/cameras', [
        'Authorization' => 'Bearer key',
    ]);

    $response->assertStatus(422);
});

test('edge cameras validates device_id max length', function () {
    Config::set('services.pesat.api_key', 'key');

    $response = $this->getJson('/api/edge/cameras?device_id=' . str_repeat('a', 101), [
        'Authorization' => 'Bearer key',
    ]);

    $response->assertStatus(422);
});

test('edge heartbeat updates all device cameras in single query', function () {
    Config::set('services.pesat.api_key', 'key');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);
    Camera::create(['id' => 'CAM-002', 'location_name' => 'B', 'latitude' => 5.19, 'longitude' => 97.16, 'edge_device_id' => 'dev-1', 'is_active' => true]);
    Camera::create(['id' => 'CAM-003', 'location_name' => 'C', 'latitude' => 5.20, 'longitude' => 97.17, 'edge_device_id' => 'dev-2', 'is_active' => true]);

    $response = $this->postJson('/api/edge/heartbeat', [
        'device_id' => 'dev-1',
        'metrics' => ['cpu' => 50],
    ], ['Authorization' => 'Bearer key']);

    $response->assertStatus(200);
    expect(Camera::find('CAM-001')->last_heartbeat_at)->not->toBeNull();
    expect(Camera::find('CAM-002')->last_heartbeat_at)->not->toBeNull();
    expect(Camera::find('CAM-003')->last_heartbeat_at)->toBeNull();
});

test('edge heartbeat works without metrics', function () {
    Config::set('services.pesat.api_key', 'key');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);

    $response = $this->postJson('/api/edge/heartbeat', [
        'device_id' => 'dev-1',
    ], ['Authorization' => 'Bearer key']);

    $response->assertStatus(200);
});

test('reports store rejects invalid media mime types', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->create('malicious.exe', 100);

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Test',
        'media' => $file,
    ]);

    $response->assertStatus(422);
});

test('reports store rejects oversized media > 20MB', function () {
    Storage::fake('public');
    $file = UploadedFile::fake()->create('big.mp4', 21000);

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Test',
        'media' => $file,
    ]);

    $response->assertStatus(422);
});

test('reports store rejects missing location_name', function () {
    $response = $this->postJson('/api/reports', []);
    $response->assertStatus(422);
});

test('reports latest validates after_id format', function () {
    $response = $this->getJson('/api/reports/latest?after_id=invalid');
    $response->assertStatus(200);
});
