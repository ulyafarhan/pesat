<?php

declare(strict_types=1);

use App\Events\NewDetectionTriggered;
use App\Models\AdminSetting;
use App\Models\Camera;
use App\Models\CitizenReport;
use App\Models\DetectionLabel;
use App\Models\DetectionLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('services.pesat.api_key', null);
});

/*
|--------------------------------------------------------------------------
| 1. VIOLATION CATEGORY — Storage & Retrieval
|--------------------------------------------------------------------------
*/

test('violation_category is stored and retrieved from detection_logs', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'test']);

    $log = DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.92,
        'violation_category' => 'Khalwat',
    ]);

    expect($log->violation_category)->toBe('Khalwat');

    $this->assertDatabaseHas('detection_logs', [
        'id' => $log->id,
        'violation_category' => 'Khalwat',
    ]);
});

test('violation_category is nullable in detection_logs', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'test']);

    $log = DetectionLog::create([
        'camera_id' => 'CAM-001',
        'label_id' => $label->id,
        'confidence_score' => 0.92,
    ]);

    expect($log->violation_category)->toBeNull();
});

test('telemetry api stores violation_category via API', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'hijab violation',
        'confidence_score' => 0.87,
        'violation_category' => 'Pakaian Tidak Syar\'i',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('detection_logs', [
        'camera_id' => 'CAM-001',
        'violation_category' => 'Pakaian Tidak Syar\'i',
        'confidence_score' => 0.870,
    ]);
});

test('telemetry api violation_category accepts all valid categories', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $categories = ['Pakaian Tidak Syar\'i', 'Khalwat', 'Celana Pendek', 'Pergaulan Bebas', 'Peringatan'];

    foreach ($categories as $cat) {
        $response = $this->postJson('/api/telemetry/log', [
            'camera_id' => 'CAM-001',
            'label_detected' => "test-$cat",
            'confidence_score' => 0.8,
            'violation_category' => $cat,
        ]);
        $response->assertStatus(201);
    }

    expect(DetectionLog::count())->toBe(count($categories));
});

/*
|--------------------------------------------------------------------------
| 2. SOURCE — Citizen Reports Source Label
|--------------------------------------------------------------------------
*/

test('source defaults to public for citizen reports', function () {
    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Taman Riyadhah',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('citizen_reports', [
        'location_name' => 'Taman Riyadhah',
        'source' => 'public',
    ]);
});

test('source can be set to ai_detection via API', function () {
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

test('source rejects invalid values', function () {
    $response = $this->postJson('/api/reports', [
        'location_name' => 'Test',
        'source' => 'invalid_source',
    ]);

    $response->assertStatus(422);
});

/*
|--------------------------------------------------------------------------
| 3. VIOLATION CATEGORY — Citizen Reports
|--------------------------------------------------------------------------
*/

test('citizen report violation_category is stored and retrieved', function () {
    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Pantai Ujong Blang',
        'source' => 'ai_detection',
        'violation_category' => 'Celana Pendek',
    ]);

    $response->assertStatus(201);
    $reportId = $response->json('data.id');

    $report = CitizenReport::find($reportId);
    expect($report->violation_category)->toBe('Celana Pendek');
    expect($report->source)->toBe('ai_detection');
});

/*
|--------------------------------------------------------------------------
| 4. DASHBOARD CONTROLLER — Inertia Rendering
|--------------------------------------------------------------------------
*/

test('dashboard index returns inertia page with expected props', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true, 'edge_device_id' => 'dev-1']);
    $label = DetectionLabel::create(['name' => 'flood']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.9]);

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Dashboard')
            ->has('initialLogs')
            ->has('totalToday')
            ->has('cameras')
            ->has('edgeDeviceIds')
        );
});

test('dashboard reports returns inertia page with expected props', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    CitizenReport::create(['location_name' => 'Test', 'reported_at' => now(), 'status' => 'pending']);

    $this->actingAs($user)
        ->get('/dashboard/reports')
        ->assertStatus(200)
        ->assertInertia(fn ($page) => $page
            ->component('Reports')
            ->has('pendingReports')
            ->has('historyReports')
        );
});

/*
|--------------------------------------------------------------------------
| 5. ROLE MIDDLEWARE — WH Admin Separation
|--------------------------------------------------------------------------
*/

test('wh officer can access both dashboard and reports', function () {
    $wh = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);

    $this->actingAs($wh)->get('/dashboard')->assertStatus(200);
    $this->actingAs($wh)->get('/dashboard/reports')->assertStatus(200);
});

test('admin is redirected from all dashboard routes to admin panel', function () {
    $admin = User::create(['name' => 'Admin', 'email' => 'admin@pesat.local', 'password' => bcrypt('p'), 'role' => 'admin']);

    $this->actingAs($admin)->get('/dashboard')->assertRedirect('/admin');
    $this->actingAs($admin)->get('/dashboard/reports')->assertRedirect('/admin');
});

test('unauthenticated users redirected to login from all dashboard routes', function () {
    $this->get('/dashboard')->assertRedirect(route('login'));
    $this->get('/dashboard/reports')->assertRedirect(route('login'));
});

test('guests can access landing page and login', function () {
    $this->get('/')->assertStatus(200);
    $this->get('/login')->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| 6. DETECTION LOG — Model Features
|--------------------------------------------------------------------------
*/

test('detection log label_detected accessor returns label name', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => '[Wanita] R-PKN-001: Tidak mengenakan hijab']);
    $log = DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.92]);

    expect($log->label_detected)->toBe('[Wanita] R-PKN-001: Tidak mengenakan hijab');
    expect($log->label->name)->toBe('[Wanita] R-PKN-001: Tidak mengenakan hijab');
});

test('detection log with relations scope loads camera and label', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'test']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.9]);

    $logs = DetectionLog::withRelations()->get();
    expect($logs->first()->relationLoaded('camera'))->toBeTrue();
    expect($logs->first()->relationLoaded('label'))->toBeTrue();
});

test('detection log byLabel scope filters by label id', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'Taman', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $l1 = DetectionLabel::create(['name' => 'a']);
    $l2 = DetectionLabel::create(['name' => 'b']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $l1->id, 'confidence_score' => 0.9]);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $l2->id, 'confidence_score' => 0.8]);

    expect(DetectionLog::byLabel($l1->id)->count())->toBe(1);
});

/*
|--------------------------------------------------------------------------
| 7. CITIZEN REPORT — Model Features
|--------------------------------------------------------------------------
*/

test('citizen report has fillable source and violation_category', function () {
    $report = CitizenReport::create([
        'location_name' => 'Test',
        'reported_at' => now(),
        'status' => 'pending',
        'source' => 'ai_detection',
        'violation_category' => 'Khalwat',
    ]);

    expect($report->source)->toBe('ai_detection');
    expect($report->violation_category)->toBe('Khalwat');
});

test('citizen report byLocation scope matches exact and prefixed location', function () {
    CitizenReport::create(['location_name' => 'Taman Riyadhah', 'reported_at' => now(), 'status' => 'pending']);
    CitizenReport::create(['location_name' => 'Taman Riyadhah - [Wanita] Deteksi', 'reported_at' => now(), 'status' => 'pending']);
    CitizenReport::create(['location_name' => 'Pantai Ujong Blang', 'reported_at' => now(), 'status' => 'pending']);

    expect(CitizenReport::byLocation('Taman Riyadhah')->count())->toBe(2);
});

test('citizen report scopes work with source filter combined', function () {
    CitizenReport::create(['location_name' => 'A', 'reported_at' => now(), 'status' => 'pending', 'source' => 'public']);
    CitizenReport::create(['location_name' => 'B', 'reported_at' => now(), 'status' => 'pending', 'source' => 'ai_detection']);
    CitizenReport::create(['location_name' => 'C', 'reported_at' => now(), 'status' => 'verified', 'source' => 'ai_detection']);

    expect(CitizenReport::pending()->count())->toBe(2);
    expect(CitizenReport::verified()->count())->toBe(1);
});

test('citizen report belongs to verifier', function () {
    $user = User::create(['name' => 'WH Officer', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    $report = CitizenReport::create([
        'location_name' => 'Test', 'reported_at' => now(),
        'status' => 'verified', 'verified_by' => $user->id,
    ]);

    expect($report->verifier->name)->toBe('WH Officer');
    expect($report->verifier->role)->toBe('wh_officer');
});

/*
|--------------------------------------------------------------------------
| 8. CAMERA — Model Edge Features
|--------------------------------------------------------------------------
*/

test('camera model handles edge_metrics JSON cast', function () {
    $camera = Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman',
        'latitude' => 5.18,
        'longitude' => 97.15,
        'is_active' => true,
        'edge_device_id' => 'dev-1',
        'edge_metrics' => ['cpu' => 45.2, 'ram' => 60.0],
    ]);

    expect($camera->edge_metrics)->toBeArray();
    expect($camera->edge_metrics['cpu'])->toBe(45.2);
    expect($camera->edge_metrics['ram'])->toBe(60);
});

test('camera active scope excludes inactive', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    Camera::create(['id' => 'CAM-002', 'location_name' => 'B', 'latitude' => 5.19, 'longitude' => 97.16, 'is_active' => false]);

    expect(Camera::active()->count())->toBe(1);
    expect(Camera::active()->first()->id)->toBe('CAM-001');
});

test('camera byEdgeDevice scope filters correctly', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);
    Camera::create(['id' => 'CAM-002', 'location_name' => 'B', 'latitude' => 5.19, 'longitude' => 97.16, 'edge_device_id' => 'dev-2', 'is_active' => true]);

    expect(Camera::byEdgeDevice('dev-1')->count())->toBe(1);
});

/*
|--------------------------------------------------------------------------
| 9. ADMIN SETTINGS — Break Mode
|--------------------------------------------------------------------------
*/

test('admin settings break mode manual active returns true via API', function () {
    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'true']);

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Test Location',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('citizen_reports', [
        'location_name' => 'Test Location',
        'is_break_dispatch' => true,
    ]);
});

test('admin settings break mode scheduled returns true during break time', function () {
    AdminSetting::create(['key' => 'break_mode_active', 'value' => 'false']);
    AdminSetting::create(['key' => 'break_start_time', 'value' => '12:00']);
    AdminSetting::create(['key' => 'break_end_time', 'value' => '14:00']);

    Carbon::setTestNow(Carbon::create(2026, 6, 19, 12, 30, 0));

    $response = $this->postJson('/api/reports', [
        'location_name' => 'Test Location',
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('citizen_reports', [
        'is_break_dispatch' => true,
    ]);

    Carbon::setTestNow();
});

/*
|--------------------------------------------------------------------------
| 10. EDGE API — Heartbeat & Cameras
|--------------------------------------------------------------------------
*/

test('edge cameras returns active cameras for device_id', function () {
    Config::set('services.pesat.api_key', 'edge-key');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);
    Camera::create(['id' => 'CAM-002', 'location_name' => 'B', 'latitude' => 5.19, 'longitude' => 97.16, 'edge_device_id' => 'dev-1', 'is_active' => true]);
    Camera::create(['id' => 'CAM-003', 'location_name' => 'C', 'latitude' => 5.20, 'longitude' => 97.17, 'edge_device_id' => 'dev-2', 'is_active' => true]);

    $response = $this->getJson('/api/edge/cameras?device_id=dev-1', [
        'Authorization' => 'Bearer edge-key',
    ]);

    $response->assertStatus(200)->assertJsonCount(2, 'data');
});

test('edge heartbeat updates timestamp and metrics', function () {
    Config::set('services.pesat.api_key', 'edge-key');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);

    $response = $this->postJson('/api/edge/heartbeat', [
        'device_id' => 'dev-1',
        'metrics' => ['cpu' => 55.5, 'ram' => 70.2],
    ], ['Authorization' => 'Bearer edge-key']);

    $response->assertStatus(200);
    $cam = Camera::find('CAM-001');
    expect($cam->last_heartbeat_at)->not->toBeNull();
    expect($cam->edge_metrics['cpu'])->toBe(55.5);
});

test('edge heartbeat works without metrics field', function () {
    Config::set('services.pesat.api_key', 'edge-key');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'edge_device_id' => 'dev-1', 'is_active' => true]);

    $response = $this->postJson('/api/edge/heartbeat', [
        'device_id' => 'dev-1',
    ], ['Authorization' => 'Bearer edge-key']);

    $response->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| 11. REPORTS POLLING — Latest+History
|--------------------------------------------------------------------------
*/

test('reports latest returns structured pending and history', function () {
    CitizenReport::create(['location_name' => 'Pending A', 'reported_at' => now(), 'status' => 'pending']);
    CitizenReport::create(['location_name' => 'Verified B', 'reported_at' => now(), 'status' => 'verified']);
    CitizenReport::create(['location_name' => 'Rejected C', 'reported_at' => now(), 'status' => 'rejected']);

    $response = $this->getJson('/api/reports/latest');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['pending', 'history'],
        ]);

    expect($response->json('data.pending'))->toHaveCount(1);
    expect($response->json('data.pending.0.location_name'))->toBe('Pending A');
});

test('reports latest supports after_id filter', function () {
    $r1 = CitizenReport::create(['location_name' => 'R1', 'reported_at' => now(), 'status' => 'pending']);
    CitizenReport::create(['location_name' => 'R2', 'reported_at' => now(), 'status' => 'pending']);

    $response = $this->getJson("/api/reports/latest?after_id={$r1->id}");

    $response->assertStatus(200);
    expect($response->json('data.pending'))->toHaveCount(1);
});

test('reports latest supports status filter', function () {
    CitizenReport::create(['location_name' => 'P', 'reported_at' => now(), 'status' => 'pending']);
    CitizenReport::create(['location_name' => 'V', 'reported_at' => now(), 'status' => 'verified']);

    $response = $this->getJson('/api/reports/latest?status=pending');
    $response->assertStatus(200);
});

test('wh reports returns only pending with verifier eager loaded', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    CitizenReport::create(['location_name' => 'A', 'reported_at' => now(), 'status' => 'pending']);
    CitizenReport::create(['location_name' => 'B', 'reported_at' => now(), 'status' => 'verified', 'verified_by' => $user->id]);

    $response = $this->getJson('/api/wh/reports');
    $response->assertStatus(200)->assertJsonCount(1, 'data');
});

/*
|--------------------------------------------------------------------------
| 12. VERIFICATION WORKFLOW
|--------------------------------------------------------------------------
*/

test('verify report updates status and stores verified_by', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    $report = CitizenReport::create(['location_name' => 'Verifiable', 'reported_at' => now(), 'status' => 'pending']);

    $this->actingAs($user)
        ->postJson("/api/wh/reports/{$report->id}/verify", [
            'status' => 'verified',
            'verification_notes' => 'Sudah ditertibkan',
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('citizen_reports', [
        'id' => $report->id,
        'status' => 'verified',
        'verified_by' => $user->id,
        'verification_notes' => 'Sudah ditertibkan',
    ]);
});

test('verify report can reject with notes', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh2@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    $report = CitizenReport::create(['location_name' => 'Fake', 'reported_at' => now(), 'status' => 'pending']);

    $this->actingAs($user)->postJson("/api/wh/reports/{$report->id}/verify", [
        'status' => 'rejected',
        'verification_notes' => 'Laporan tidak valid',
    ])->assertStatus(200);

    $this->assertDatabaseHas('citizen_reports', [
        'id' => $report->id,
        'status' => 'rejected',
    ]);
});

test('verify report requires valid status', function () {
    $report = CitizenReport::create(['location_name' => 'Test', 'reported_at' => now(), 'status' => 'pending']);

    $this->postJson("/api/wh/reports/{$report->id}/verify", [
        'status' => 'invalid',
    ])->assertStatus(422);
});

/*
|--------------------------------------------------------------------------
| 13. TELEMETRY LATEST — Filtering
|--------------------------------------------------------------------------
*/

test('telemetry latest filters by camera_id', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    Camera::create(['id' => 'CAM-002', 'location_name' => 'B', 'latitude' => 5.19, 'longitude' => 97.16, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'test']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.9]);
    DetectionLog::create(['camera_id' => 'CAM-002', 'label_id' => $label->id, 'confidence_score' => 0.8]);

    $response = $this->getJson('/api/telemetry/latest?camera_id=CAM-001');
    $response->assertStatus(200)->assertJsonCount(1, 'data');
});

test('telemetry latest respects limit parameter', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'test']);
    foreach (range(1, 5) as $i) {
        DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.5 + $i / 10]);
    }

    $response = $this->getJson('/api/telemetry/latest?limit=3');
    $response->assertStatus(200)->assertJsonCount(3, 'data');
});

test('telemetry latest returns meta fields', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'test']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.9]);

    $response = $this->getJson('/api/telemetry/latest');
    $response->assertStatus(200)
        ->assertJsonStructure(['meta' => ['total_today', 'latest_id']]);
});

/*
|--------------------------------------------------------------------------
| 14. CACHING BEHAVIOR
|--------------------------------------------------------------------------
*/

test('detection label reuses existing label without duplicates', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);

    $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'unique_label_test',
        'confidence_score' => 0.9,
    ])->assertStatus(201);

    $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'unique_label_test',
        'confidence_score' => 0.95,
    ])->assertStatus(201);

    expect(DetectionLabel::where('name', 'unique_label_test')->count())->toBe(1);
});

test('total today returns correct count', function () {
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);
    $label = DetectionLabel::create(['name' => 'test']);
    DetectionLog::create(['camera_id' => 'CAM-001', 'label_id' => $label->id, 'confidence_score' => 0.9, 'created_at' => now()]);

    $response = $this->getJson('/api/telemetry/latest');
    $response->assertStatus(200);
    expect($response->json('meta.total_today'))->toBe(1);
});

/*
|--------------------------------------------------------------------------
| 15. API AUTH — Bearer Token
|--------------------------------------------------------------------------
*/

test('telemetry api rejects request without auth when key is configured', function () {
    Config::set('services.pesat.api_key', 'secret');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'test',
        'confidence_score' => 0.9,
    ]);

    $response->assertStatus(401);
});

test('telemetry api rejects wrong bearer token', function () {
    Config::set('services.pesat.api_key', 'correct-key');

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'test',
        'confidence_score' => 0.9,
    ], ['Authorization' => 'Bearer wrong-key']);

    $response->assertStatus(401);
});

test('telemetry api accepts correct bearer token', function () {
    Config::set('services.pesat.api_key', 'correct-key');
    Camera::create(['id' => 'CAM-001', 'location_name' => 'A', 'latitude' => 5.18, 'longitude' => 97.15, 'is_active' => true]);

    $response = $this->postJson('/api/telemetry/log', [
        'camera_id' => 'CAM-001',
        'label_detected' => 'test',
        'confidence_score' => 0.9,
    ], ['Authorization' => 'Bearer correct-key']);

    $response->assertStatus(201);
});

/*
|--------------------------------------------------------------------------
| 16. DETECTION SNAPSHOT — X-Sendfile Route
|--------------------------------------------------------------------------
*/

test('detection snapshot returns 404 when file does not exist', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);

    $this->actingAs($user)
        ->get('/detections/CAM-NONEXISTENT')
        ->assertStatus(404);
});

test('detection snapshot returns file when exists', function () {
    $user = User::create(['name' => 'WH', 'email' => 'wh@pesat.local', 'password' => bcrypt('p'), 'role' => 'wh_officer']);
    $path = storage_path('app/detections');
    if (!is_dir($path)) mkdir($path, 0755, true);
    file_put_contents("{$path}/latest_CAM-001.jpg", 'fake-image-data');

    $this->actingAs($user)
        ->get('/detections/CAM-001')
        ->assertStatus(200);

    unlink("{$path}/latest_CAM-001.jpg");
});

test('detection snapshot requires auth', function () {
    $this->get('/detections/CAM-001')->assertRedirect(route('login'));
});
