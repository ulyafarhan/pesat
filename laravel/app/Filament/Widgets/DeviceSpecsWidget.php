<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Camera;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Isolate;
use Livewire\Attributes\Lazy;

#[Lazy]
#[Isolate]
class DeviceSpecsWidget extends Widget
{
    protected string $view = 'filament.widgets.device-specs-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function getListeners(): array
    {
        if (config('broadcasting.default') !== 'reverb') {
            return [];
        }

        return [
            'echo:pesat-telemetry,.telemetry.updated' => '$refresh',
        ];
    }

    protected function getPollingInterval(): ?string
    {
        return '5s';
    }

    public function getHostSpecs(): array
    {
        return Cache::remember('host_specs_cache', 5, function () {
            $isWindows = str_contains(strtoupper(PHP_OS), 'WIN');

            $static = Cache::remember('host_static_specs', 3600, function () use ($isWindows) {
                $os = php_uname('s') . ' ' . php_uname('r');
                $cpuName = 'Unknown';
                $cpuCores = 1;
                $cpuThreads = 1;
                $cpuSpeed = 'Unknown';
                $ramTotal = 0;
                $diskTotal = disk_total_space('/') ?: 1;
                $diskType = 'SSD';

                if ($isWindows) {
                    try {
                        $osOut = shell_exec('wmic os get Caption /value 2>&1');
                        if ($osOut && preg_match('/Caption=(.+)/i', $osOut, $m)) {
                            $os = str_replace('Microsoft ', '', trim($m[1]));
                        }

                        $cpuOut = shell_exec('wmic cpu get Name,NumberOfCores,NumberOfLogicalProcessors,MaxClockSpeed /value 2>&1');
                        if ($cpuOut) {
                            if (preg_match('/Name=(.+)/i', $cpuOut, $m)) {
                                $cpuName = trim($m[1]);
                            }
                            if (preg_match('/NumberOfCores=(\d+)/i', $cpuOut, $m)) {
                                $cpuCores = (int)$m[1];
                            }
                            if (preg_match('/NumberOfLogicalProcessors=(\d+)/i', $cpuOut, $m)) {
                                $cpuThreads = (int)$m[1];
                            }
                            if (preg_match('/MaxClockSpeed=(\d+)/i', $cpuOut, $m)) {
                                $cpuSpeed = round((float)$m[1] / 1000, 2) . ' GHz';
                            }
                        }

                        $ramTotalOut = shell_exec('wmic computersystem get TotalPhysicalMemory /value 2>&1');
                        if ($ramTotalOut && preg_match('/TotalPhysicalMemory=(\d+)/i', $ramTotalOut, $m)) {
                            $ramTotal = (float)$m[1];
                        }

                        $diskTypeOut = shell_exec('powershell -Command "Get-PhysicalDisk | Select-Object -First 1 -Property MediaType" 2>&1');
                        if ($diskTypeOut && preg_match('/(SSD|HDD)/i', $diskTypeOut, $m)) {
                            $diskType = strtoupper($m[1]);
                        }
                    } catch (\Throwable $e) {
                    }
                } else {
                    try {
                        if (file_exists('/etc/os-release')) {
                            $osRelease = file_get_contents('/etc/os-release');
                            if (preg_match('/PRETTY_NAME="(.+)"/i', $osRelease, $m)) {
                                $os = $m[1];
                            }
                        }

                        if (file_exists('/proc/cpuinfo')) {
                            $cpuinfo = file_get_contents('/proc/cpuinfo');
                            if (preg_match('/model name\s+:\s+(.+)/i', $cpuinfo, $m)) {
                                $cpuName = trim($m[1]);
                            }
                            $cpuThreads = substr_count($cpuinfo, 'processor') ?: 1;
                            if (preg_match('/cpu cores\s+:\s+(\d+)/i', $cpuinfo, $m)) {
                                $cpuCores = (int)$m[1];
                            } else {
                                $cpuCores = $cpuThreads;
                            }
                            if (preg_match('/cpu MHz\s+:\s+(.+)/i', $cpuinfo, $m)) {
                                $cpuSpeed = round((float)$m[1] / 1000, 2) . ' GHz';
                            }
                        }

                        if (file_exists('/proc/meminfo')) {
                            $meminfo = file_get_contents('/proc/meminfo');
                            if (preg_match('/MemTotal:\s+(\d+)/i', $meminfo, $m)) {
                                $ramTotal = (float)$m[1] * 1024;
                            }
                        }

                        $diskType = 'SSD';
                        $devices = glob('/sys/block/sd*');
                        if (!empty($devices)) {
                            $firstDev = $devices[0];
                            if (file_exists($firstDev . '/queue/rotational')) {
                                $rot = trim(file_get_contents($firstDev . '/queue/rotational'));
                                $diskType = $rot === '0' ? 'SSD' : 'HDD';
                            }
                        }
                    } catch (\Throwable $e) {
                    }
                }

                if ($ramTotal === 0) {
                    $ramTotal = 8 * 1024 * 1024 * 1024;
                }

                $dbVersion = 'Unknown';
                try {
                    $dbVersion = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION);
                } catch (\Throwable $e) {
                }

                return [
                    'os' => $os,
                    'cpu_name' => $cpuName,
                    'cpu_cores' => $cpuCores,
                    'cpu_threads' => $cpuThreads,
                    'cpu_speed' => $cpuSpeed !== 'Unknown' ? $cpuSpeed : 'N/A',
                    'ram_total' => $ramTotal,
                    'disk_total' => $diskTotal,
                    'disk_type' => $diskType,
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    'db_version' => $dbVersion,
                    'web_server' => $_SERVER['SERVER_SOFTWARE'] ?? 'OpenLiteSpeed (aaPanel)',
                ];
            });

            $ramTotal = $static['ram_total'];
            $ramUsedPercent = 0;

            if ($isWindows) {
                $ramUsedPercent = Cache::remember('host_ram_used_percent', 5, function () use ($ramTotal) {
                    try {
                        $ramFreeOut = shell_exec('wmic os get FreePhysicalMemory /value 2>&1');
                        if ($ramFreeOut && preg_match('/FreePhysicalMemory=(\d+)/i', $ramFreeOut, $m)) {
                            $ramFree = (float)$m[1] * 1024;
                            $ramUsed = $ramTotal - $ramFree;
                            return $ramTotal > 0 ? round(($ramUsed / $ramTotal) * 100, 1) : 50;
                        }
                    } catch (\Throwable $e) {
                    }
                    return 50;
                });
            } else {
                try {
                    if (file_exists('/proc/meminfo')) {
                        $meminfo = file_get_contents('/proc/meminfo');
                        if (preg_match('/MemAvailable:\s+(\d+)/i', $meminfo, $m)) {
                            $ramFree = (float)$m[1] * 1024;
                            $ramUsed = $ramTotal - $ramFree;
                            $ramUsedPercent = $ramTotal > 0 ? round(($ramUsed / $ramTotal) * 100, 1) : 0;
                        }
                    }
                } catch (\Throwable $e) {
                }
            }

            $diskTotal = $static['disk_total'];
            $diskFree = disk_free_space('/') ?: 0;
            $diskUsed = $diskTotal - $diskFree;
            $diskUsedPercent = $diskTotal > 0 ? round(($diskUsed / $diskTotal) * 100, 1) : 0;

            return [
                'os' => $static['os'],
                'cpu_name' => $static['cpu_name'],
                'cpu_cores' => $static['cpu_cores'],
                'cpu_threads' => $static['cpu_threads'],
                'cpu_speed' => $static['cpu_speed'],
                'ram_total' => round($ramTotal / (1024 * 1024 * 1024), 2),
                'ram_used_percent' => $ramUsedPercent,
                'disk_total' => round($diskTotal / (1024 * 1024 * 1024), 2),
                'disk_free' => round($diskFree / (1024 * 1024 * 1024), 2),
                'disk_used_percent' => $diskUsedPercent,
                'disk_type' => $static['disk_type'],
                'php_version' => $static['php_version'],
                'laravel_version' => $static['laravel_version'],
                'db_version' => $static['db_version'],
                'web_server' => $static['web_server'],
            ];
        });
    }

    public function getDevicesData(): array
    {
        return Cache::remember('devices_data_cache', 5, function () {
            $cameras = Camera::whereNotNull('edge_device_id')
                ->where('edge_device_id', '!=', '')
                ->get();

            $grouped = $cameras->groupBy('edge_device_id');
            $devices = [];

            foreach ($grouped as $deviceId => $deviceCameras) {
                $firstCamera = $deviceCameras->first();
                $metrics = [];
                
                if ($firstCamera && $firstCamera->edge_metrics) {
                    $metrics = is_string($firstCamera->edge_metrics) 
                        ? json_decode($firstCamera->edge_metrics, true) 
                        : $firstCamera->edge_metrics;
                }

                $lastHeartbeat = $deviceCameras->max('last_heartbeat_at');
                $isOnline = false;
                
                if ($lastHeartbeat) {
                    $lastHeartbeatDateTime = is_string($lastHeartbeat) 
                        ? \Carbon\Carbon::parse($lastHeartbeat) 
                        : $lastHeartbeat;
                    $isOnline = $lastHeartbeatDateTime->diffInMinutes() < 5;
                }

                $cameraNames = $deviceCameras->map(fn($c) => "{$c->location_name} ({$c->id})")->toArray();

                $devices[] = [
                    'device_id' => $deviceId,
                    'current_cpu' => $metrics['cpu'] ?? null,
                    'current_ram' => $metrics['ram'] ?? null,
                    'cameras' => $cameraNames,
                    'is_online' => $isOnline,
                    'last_heartbeat_at' => $lastHeartbeat,
                ];
            }

            return $devices;
        });
    }
}
