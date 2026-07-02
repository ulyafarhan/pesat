<?php

declare(strict_types=1);

use App\Events\CitizenReportStatusUpdated;
use App\Events\NewCitizenReportSubmitted;
use App\Events\NewDetectionTriggered;
use App\Models\AdminSetting;
use App\Models\Camera;
use App\Models\CitizenReport;
use App\Models\DetectionLabel;
use App\Models\DetectionLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Telemetry Ingestion API (POST /api/telemetry/log)
|--------------------------------------------------------------------------
*/

test('telemetry api checks bearer token if api key is configured', function () {
    Config::set('services.pesat.api_key', 'super-secret-key');

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'crowd',
        'confidence_score' => 0.85,
    ]);
    $response->assertStatus(401);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'crowd',
        'confidence_score' => 0.85,
    ], [
        'Authorization' => 'Bearer wrong-key',
    ]);
    $response->assertStatus(401);

    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    Event::fake([NewDetectionTriggered::class]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'crowd',
        'confidence_score' => 0.85,
    ], [
        'Authorization' => 'Bearer super-secret-key',
    ]);

    $response->assertStatus(201);
});

test('telemetry api validates required parameters', function () {
    Config::set('services.pesat.api_key', null);

    $response = $this->postJson('/api/telemetry/log', []);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['camera_id', 'label_detected', 'confidence_score']);
});

test('telemetry api stores detection log, creates label with firstOrCreate, and broadcasts event', function () {
    Config::set('services.pesat.api_key', null);
    Event::fake([NewDetectionTriggered::class]);

    $camera = Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'garbage',
        'confidence_score' => 0.92,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('detection_labels', ['name' => 'garbage']);
    $this->assertDatabaseHas('detection_logs', [
        'camera_id' => 'CAM-001',
        'confidence_score' => 0.920,
    ]);

    $labelId = DetectionLabel::where('name', 'garbage')->first()->id;
    $this->assertDatabaseHas('detection_logs', [
        'camera_id' => 'CAM-001',
        'label_id' => $labelId,
    ]);

    Event::assertDispatched(NewDetectionTriggered::class, function ($event) use ($labelId) {
        return $event->log->camera_id === 'CAM-001' && $event->log->label_id === $labelId;
    });

    $response2 = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'garbage',
        'confidence_score' => 0.88,
    ]);

    $response2->assertStatus(201);
    expect(DetectionLabel::where('name', 'garbage')->count())->toBe(1);
});

test('telemetry api accepts violation_category', function () {
    Config::set('services.pesat.api_key', null);

    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'hijab violation',
        'confidence_score' => 0.92,
        'violation_category' => 'Pakaian Tidak Syar\'i',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('detection_logs', [
        'camera_id' => 'CAM-001',
        'violation_category' => 'Pakaian Tidak Syar\'i',
    ]);
});

test('telemetry api rejects confidence score outside 0 to 1', function () {
    Config::set('services.pesat.api_key', null);

    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'crowd',
        'confidence_score' => 1.5,
    ]);
    $response->assertStatus(422);
});

/*
|--------------------------------------------------------------------------
| Polling API (GET /api/telemetry/latest)
|--------------------------------------------------------------------------
*/

test('telemetry latest returns empty data when no new logs after given id', function () {
    Config::set('services.pesat.api_key', null);

    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $label = DetectionLabel::create(['name' => 'flood']);
    $log = DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.88,
    ]);

    $response = $this->getJson("/api/telemetry/latest?after_id={$log->id}");

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(0, 'data');
});

test('telemetry latest returns only new logs after given id', function () {
    Config::set('services.pesat.api_key', null);

    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $label = DetectionLabel::create(['name' => 'flood']);
    $log1 = DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.88,
    ]);

    $log2 = DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.92,
    ]);

    $response = $this->getJson("/api/telemetry/latest?after_id={$log1->id}");

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $log2->id);
});

test('telemetry latest returns meta with total today count', function () {
    Config::set('services.pesat.api_key', null);

    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $label = DetectionLabel::create(['name' => 'flood']);
    DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.88,
    ]);

    $response = $this->getJson('/api/telemetry/latest?after_id=0');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'status',
            'data',
            'meta' => ['total_today', 'latest_id'],
        ]);

    expect($response->json('meta.total_today'))->toBeGreaterThanOrEqual(1);
});

/*
|--------------------------------------------------------------------------
| Citizen Reports API (POST /api/reports)
|--------------------------------------------------------------------------
*/

test('citizen report store sets source to public by default', function () {
    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1800,
        'longitude' => 97.1500,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('citizen_reports', [
        'location_name' => 'Taman Riyadhah',
        'source' => 'public',
    ]);
});

test('citizen report store accepts ai_detection source', function () {
    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Masjid Agung',
        'source' => 'ai_detection',
        'violation_category' => 'Khalwat',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('citizen_reports', [
        'location_name' => 'Masjid Agung',
        'source' => 'ai_detection',
        'violation_category' => 'Khalwat',
    ]);
});

test('citizen report store creates report under normal conditions', function () {
    Storage::fake('public');
    Event::fake([NewCitizenReportSubmitted::class]);

    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    Carbon::setTestNow(Carbon::create(2026, 6, 18, 10, 0, 0));

    $file = UploadedFile::fake()->image('report.jpg');

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Jalan Merdeka',
        'latitude' => 5.1800,
        'longitude' => 97.1500,
        'media' => $file,
    ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('citizen_reports', [
        'location_name' => 'Jalan Merdeka',
        'status' => 'pending',
        'is_break_dispatch' => false,
    ]);

    $report = CitizenReport::first();
    expect($report->media_path)->not->toBeNull();
    Storage::disk('public')->assertExists($report->media_path);

    Event::assertDispatched(NewCitizenReportSubmitted::class, function ($event) use ($report) {
        return $event->report->id === $report->id;
    });

    Carbon::setTestNow();
});

test('citizen report store sets is_break_dispatch to true when manual break mode is active', function () {
    Storage::fake('public');
    Event::fake([NewCitizenReportSubmitted::class]);

    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'true']);

    Carbon::setTestNow(Carbon::create(2026, 6, 18, 10, 0, 0));

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Jalan Merdeka',
        'latitude' => 5.1800,
        'longitude' => 97.1500,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('citizen_reports', [
        'location_name' => 'Jalan Merdeka',
        'is_break_dispatch' => true,
        'status' => 'pending',
    ]);

    Carbon::setTestNow();
});

test('citizen report store sets is_break_dispatch to true during scheduled break time', function () {
    Storage::fake('public');
    Event::fake([NewCitizenReportSubmitted::class]);

    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    Carbon::setTestNow(Carbon::create(2026, 6, 18, 12, 30, 0));

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Jalan Merdeka',
        'latitude' => 5.1800,
        'longitude' => 97.1500,
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('citizen_reports', [
        'location_name' => 'Jalan Merdeka',
        'is_break_dispatch' => true,
        'status' => 'pending',
    ]);

    Carbon::setTestNow();
});

/*
|--------------------------------------------------------------------------
| Reports Polling API (GET /api/reports/latest)
|--------------------------------------------------------------------------
*/

test('reports latest returns pending and history data structures', function () {
    CitizenReport::create([
        'location_name' => 'Pantai Ujong Blang',
        'reported_at' => now(),
        'status' => 'pending',
    ]);

    CitizenReport::create([
        'location_name' => 'Masjid Agung',
        'reported_at' => now(),
        'status' => 'verified',
    ]);

    $response = $this->getJson('/api/reports/latest');

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonStructure([
            'status',
            'data' => [
                'pending',
                'history',
            ],
        ]);

    $pending = $response->json('data.pending');
    $history = $response->json('data.history');

    expect(collect($pending)->pluck('status')->unique()->toArray())->toBe(['pending']);
    expect(count($history))->toBeGreaterThanOrEqual(1);
});

/*
|--------------------------------------------------------------------------
| WH Officer API (GET /api/wh/reports & POST /api/wh/reports/{id}/verify)
|--------------------------------------------------------------------------
*/

test('wh officer gets only pending reports', function () {
    CitizenReport::create([
        'location_name' => 'Location A',
        'reported_at' => now(),
        'status' => 'pending',
    ]);

    CitizenReport::create([
        'location_name' => 'Location B',
        'reported_at' => now(),
        'status' => 'verified',
    ]);

    CitizenReport::create([
        'location_name' => 'Location C',
        'reported_at' => now(),
        'status' => 'rejected',
    ]);

    $response = $this->getJson('/api/wh/reports');

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.location_name', 'Location A');
});

test('wh officer can verify a report', function () {
    Event::fake([CitizenReportStatusUpdated::class]);

    $report = CitizenReport::create([
        'location_name' => 'Report to Verify',
        'reported_at' => now(),
        'status' => 'pending',
    ]);

    $user = User::create([
        'name' => 'WH Officer',
        'email' => 'wh@pesat.local',
        'password' => bcrypt('password'),
        'role' => 'wh_officer',
    ]);

    $response = $this->actingAs($user)
        ->postJson("/api/wh/reports/{$report->id}/verify", [
            'status' => 'verified',
            'verification_notes' => 'Tindakan selesai dilakukan di lapangan.',
        ]);

    $response->assertStatus(200);
    $this->assertDatabaseHas('citizen_reports', [
        'id' => $report->id,
        'status' => 'verified',
        'verified_by' => $user->id,
        'verification_notes' => 'Tindakan selesai dilakukan di lapangan.',
    ]);

    Event::assertDispatched(CitizenReportStatusUpdated::class, function ($event) use ($report) {
        return $event->report->id === $report->id;
    });
});

/*
|--------------------------------------------------------------------------
| Admin Settings API (GET /api/admin/settings & POST /api/admin/settings)
|--------------------------------------------------------------------------
*/

test('admin settings get and update', function () {
    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    $response = $this->getJson('/api/admin/settings');
    $response->assertStatus(200)
        ->assertJsonPath('data.break_mode_active', 'false');

    $responseUpdate = $this->postJson('/api/admin/settings', [
        'break_mode_active' => 'true',
        'break_start_time' => '13:00',
        'break_end_time' => '15:00',
    ]);

    $responseUpdate->assertStatus(200)
        ->assertJsonPath('data.break_mode_active', 'true')
        ->assertJsonPath('data.break_start_time', '13:00')
        ->assertJsonPath('data.break_end_time', '15:00');

    $this->assertDatabaseHas('admin_settings', ['key' => 'break_mode_active', 'value' => 'true']);
});

/*
|--------------------------------------------------------------------------
| Middleware & Auth Routing Checks (RedirectIfNotWH)
|--------------------------------------------------------------------------
*/

test('unauthenticated users are redirected to login from dashboard', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
    $this->get('/dashboard/reports')->assertRedirect(route('login'));
});

test('wh officer can access dashboard and reports', function () {
    $wh = User::create([
        'name' => 'WH User',
        'email' => 'wh@pesat.local',
        'password' => bcrypt('password'),
        'role' => 'wh_officer',
    ]);

    $this->actingAs($wh)
        ->get('/dashboard')
        ->assertStatus(200);

    $this->actingAs($wh)
        ->get('/dashboard/reports')
        ->assertStatus(200);
});

test('admin is redirected from all dashboard routes to admin panel', function () {
    $admin = User::create([
        'name' => 'Admin User',
        'email' => 'admin@pesat.local',
        'password' => bcrypt('password'),
        'role' => 'admin',
    ]);

    $this->actingAs($admin)
        ->get('/dashboard')
        ->assertRedirect('/admin');

    $this->actingAs($admin)
        ->get('/dashboard/reports')
        ->assertRedirect('/admin');
});

/*
|--------------------------------------------------------------------------
| Camera Status API (GET /api/cameras/{id})
|--------------------------------------------------------------------------
*/

test('camera api returns camera details', function () {
    Camera::create([
        'id' => 'CAM-XYZ',
        'location_name' => 'Pasar Impres',
        'latitude' => 5.1812,
        'longitude' => 97.1422,
        'is_active' => true,
    ]);

    $response = $this->getJson('/api/cameras/CAM-XYZ');
    $response->assertStatus(200)
        ->assertJsonPath('id', 'CAM-XYZ')
        ->assertJsonPath('location_name', 'Pasar Impres');
});

test('camera api returns 404 for missing camera', function () {
    $response = $this->getJson('/api/cameras/CAM-NONEXISTENT');
    $response->assertStatus(404);
});

/*
|--------------------------------------------------------------------------
| Edge Device API (GET /api/edge/cameras & POST /api/edge/heartbeat)
|--------------------------------------------------------------------------
*/

test('edge cameras returns 401 without valid bearer token', function () {
    Config::set('services.pesat.api_key', 'edge-secret');

    $response = $this->getJson('/api/edge/cameras?device_id=test-device');
    $response->assertStatus(401);
});

test('edge cameras returns assigned active cameras for device', function () {
    Config::set('services.pesat.api_key', 'edge-secret');

    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'test-device', 'is_active' => true]);
    Camera::create(['id' => 'CAM-002', 'location_name' => 'Pantai', 'latitude' => 5.19, 'longitude' => 97.16, 'edge_device_id' => 'test-device', 'is_active' => true]);
    Camera::create(['id' => 'CAM-003', 'location_name' => 'Pasar', 'latitude' => 5.20, 'longitude' => 97.17, 'edge_device_id' => 'other-device', 'is_active' => true]);
    Camera::create(['id' => 'CAM-004', 'location_name' => 'Kampus', 'latitude' => 5.21, 'longitude' => 97.18, 'edge_device_id' => 'test-device', 'is_active' => false]);

    $response = $this->getJson('/api/edge/cameras?device_id=test-device', [
        'Authorization' => 'Bearer edge-secret',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonCount(2, 'data');
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain('CAM-001', 'CAM-002');
    expect($ids)->not->toContain('CAM-003', 'CAM-004');
});

test('edge heartbeat updates last_heartbeat_at and metrics', function () {
    Config::set('services.pesat.api_key', 'edge-secret');

    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'test-device', 'is_active' => true]);
    Camera::create(['id' => 'CAM-002', 'location_name' => 'Pantai', 'latitude' => 5.19, 'longitude' => 97.16, 'edge_device_id' => 'test-device', 'is_active' => true]);

    $response = $this->postJson('/api/edge/heartbeat', [
        'device_id' => 'test-device',
        'metrics' => ['cpu' => 45.2, 'ram' => 60.0, 'uptime' => 3600],
    ], ['Authorization' => 'Bearer edge-secret']);

    $response->assertStatus(200)
        ->assertJsonPath('status', 'success')
        ->assertJsonStructure(['timestamp']);

    $cam1 = Camera::find('CAM-001');
    expect($cam1->last_heartbeat_at)->not->toBeNull();
    expect($cam1->edge_metrics)->toBeArray();
    expect($cam1->edge_metrics['cpu'])->toBe(45.2);

    $cam2 = Camera::find('CAM-002');
    expect($cam2->last_heartbeat_at)->not->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Model Scopes
|--------------------------------------------------------------------------
*/

test('camera scope active returns only active cameras', function () {
    Camera::create([
        'id' => 'CAM-A',
        'location_name' => 'A',
        'latitude' => 5.18,
        'longitude' => 97.15,
        'is_active' => true,
    ]);

    Camera::create([
        'id' => 'CAM-B',
        'location_name' => 'B',
        'latitude' => 5.19,
        'longitude' => 97.16,
        'is_active' => false,
    ]);

    $active = Camera::active()->get();
    expect($active->count())->toBe(1);
    expect($active->first()->id)->toBe('CAM-A');
});

test('detection log scope today returns only todays logs', function () {
    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $label = DetectionLabel::create(['name' => 'flood']);

    DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.90,
        'created_at' => now(),
    ]);

    DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.80,
        'created_at' => now()->subDays(2),
    ]);

    $todayLogs = DetectionLog::today()->count();
    expect($todayLogs)->toBe(1);
});

test('detection log scope critical returns only high confidence logs', function () {
    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Riyadhah',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
    ]);

    $label = DetectionLabel::create(['name' => 'flood']);

    DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.92,
    ]);

    DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.60,
    ]);

    $critical = DetectionLog::critical()->get();
    expect($critical->count())->toBe(1);
    expect((float) $critical->first()->confidence_score)->toBeGreaterThan(0.85);
});

test('citizen report scope pending returns only pending reports', function () {
    CitizenReport::create([
        'location_name' => 'Pending Location',
        'reported_at' => now(),
        'status' => 'pending',
    ]);

    CitizenReport::create([
        'location_name' => 'Verified Location',
        'reported_at' => now(),
        'status' => 'verified',
    ]);

    $pending = CitizenReport::pending()->get();
    expect($pending->count())->toBe(1);
    expect($pending->first()->status)->toBe('pending');
});

test('citizen report scope by location matches exact and prefix variants', function () {
    CitizenReport::create([
        'location_name' => 'Taman Riyadhah',
        'reported_at' => now(),
        'status' => 'pending',
    ]);

    CitizenReport::create([
        'location_name' => 'Taman Riyadhah - [Wanita] R-PKN-001',
        'reported_at' => now(),
        'status' => 'pending',
    ]);

    CitizenReport::create([
        'location_name' => 'Pantai Ujong Blang',
        'reported_at' => now(),
        'status' => 'pending',
    ]);

    $byLocation = CitizenReport::byLocation('Taman Riyadhah')->get();
    expect($byLocation->count())->toBe(2);
});
