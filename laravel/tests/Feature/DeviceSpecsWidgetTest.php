<?php

declare(strict_types=1);

use App\Filament\Widgets\DeviceSpecsWidget;
use App\Models\Camera;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('widget can retrieve host server specifications automatically', function () {
    $widget = new DeviceSpecsWidget();
    $specs = $widget->getHostSpecs();

    expect($specs)->toBeArray();
    expect($specs)->toHaveKeys([
        'os',
        'cpu_name',
        'cpu_cores',
        'cpu_threads',
        'cpu_speed',
        'ram_total',
        'ram_used_percent',
        'disk_total',
        'disk_free',
        'disk_used_percent',
        'disk_type',
        'php_version',
        'laravel_version',
        'db_version',
        'web_server',
    ]);

    expect($specs['php_version'])->toBe(PHP_VERSION);
    expect($specs['laravel_version'])->toBe(app()->version());
});

test('widget can retrieve edge devices data from database correctly', function () {
    Camera::create([
        'id' => 'CAM-001',
        'location_name' => 'Taman Kota',
        'latitude' => 5.1802,
        'longitude' => 97.1507,
        'is_active' => true,
        'edge_device_id' => 'EDGE-PC-01',
        'last_heartbeat_at' => now(),
        'edge_metrics' => ['cpu' => 30.5, 'ram' => 45.0],
    ]);

    $widget = new DeviceSpecsWidget();
    $devices = $widget->getDevicesData();

    expect($devices)->toHaveCount(1);
    expect($devices[0]['device_id'])->toBe('EDGE-PC-01');
    expect($devices[0]['current_cpu'])->toBe(30.5);
    expect($devices[0]['current_ram'])->toBe(45.0);
    expect($devices[0]['is_online'])->toBeTrue();
    expect($devices[0]['cameras'])->toContain('Taman Kota (CAM-001)');
});
