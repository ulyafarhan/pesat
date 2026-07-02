<x-filament-widgets::widget>
    @once
    <style>
        .specs-grid-4 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.25rem;
            margin-top: 0.75rem;
            width: 100%;
        }
        .specs-grid-3 {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.25rem;
            margin-top: 0.75rem;
            width: 100%;
        }
        .specs-card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        :is(.dark) .specs-card {
            background-color: rgba(24, 24, 27, 0.55);
            border-color: #27272a;
        }
        .specs-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }
        :is(.dark) .specs-card:hover {
            border-color: #3f3f46;
        }
        .specs-flex-align {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .specs-flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .specs-icon-container {
            padding: 0.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .specs-bg-indigo { background-color: #eef2ff; color: #4f46e5; }
        :is(.dark) .specs-bg-indigo { background-color: rgba(79, 70, 229, 0.15); color: #818cf8; }
        .specs-bg-emerald { background-color: #ecfdf5; color: #059669; }
        :is(.dark) .specs-bg-emerald { background-color: rgba(5, 150, 105, 0.15); color: #34d399; }
        .specs-bg-amber { background-color: #fffbeb; color: #d97706; }
        :is(.dark) .specs-bg-amber { background-color: rgba(217, 119, 6, 0.15); color: #fbbf24; }
        .specs-bg-purple { background-color: #faf5ff; color: #7c3aed; }
        :is(.dark) .specs-bg-purple { background-color: rgba(124, 58, 237, 0.15); color: #a78bfa; }
        
        .specs-text-title {
            color: #0f172a;
            font-weight: 700;
            font-size: 0.875rem;
            margin: 0;
            line-height: 1.25;
        }
        :is(.dark) .specs-text-title {
            color: #f4f4f5;
        }
        .specs-text-value {
            color: #1e293b;
            font-weight: 800;
            font-size: 0.875rem;
            margin: 0;
        }
        :is(.dark) .specs-text-value {
            color: #fafafa;
        }
        .specs-text-muted {
            color: #64748b;
            font-size: 0.75rem;
            margin: 0;
        }
        :is(.dark) .specs-text-muted {
            color: #a1a1aa;
        }
        .specs-text-small {
            font-size: 0.7rem;
            color: #64748b;
        }
        :is(.dark) .specs-text-small {
            color: #a1a1aa;
        }
        
        .specs-progress-container {
            margin-top: 0.75rem;
        }
        .specs-progress-track {
            width: 100%;
            background-color: #e2e8f0;
            border-radius: 9999px;
            height: 0.375rem;
            overflow: hidden;
            margin-top: 0.25rem;
        }
        :is(.dark) .specs-progress-track {
            background-color: #27272a;
        }
        .specs-progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 0.8s ease-in-out;
        }
        .specs-fill-indigo { background-color: #6366f1; }
        .specs-fill-emerald { background-color: #10b981; }
        .specs-fill-amber { background-color: #f59e0b; }
        .specs-fill-rose { background-color: #f43f5e; }
        
        .specs-divider {
            border: 0;
            border-top: 1px solid #e2e8f0;
            margin: 0.75rem 0;
        }
        :is(.dark) .specs-divider {
            border-color: #27272a;
        }
        
        .specs-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.125rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            border: 1px solid transparent;
        }
        .specs-badge-online {
            background-color: #ecfdf5;
            color: #065f46;
            border-color: #a7f3d0;
        }
        :is(.dark) .specs-badge-online {
            background-color: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.25);
        }
        .specs-badge-offline {
            background-color: #fdf2f8;
            color: #9d174d;
            border-color: #fbcfe8;
        }
        :is(.dark) .specs-badge-offline {
            background-color: rgba(244, 63, 94, 0.15);
            color: #f43f5e;
            border-color: rgba(244, 63, 94, 0.25);
        }
        .specs-badge-dot {
            width: 0.375rem;
            height: 0.375rem;
            border-radius: 9999px;
        }
        .specs-badge-dot-online { background-color: #10b981; }
        .specs-badge-dot-offline { background-color: #f43f5e; }
        
        .specs-meta-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.75rem;
            line-height: 1.5;
        }
        
        .specs-tag-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.375rem;
            margin-top: 0.5rem;
        }
        .specs-tag {
            font-size: 0.7rem;
            padding: 0.125rem 0.375rem;
            border-radius: 0.25rem;
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 500;
        }
        :is(.dark) .specs-tag {
            background-color: #27272a;
            border-color: #3f3f46;
            color: #d4d4d8;
        }
        
        .specs-section-header {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 0.5rem;
            margin-bottom: 0.75rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        :is(.dark) .specs-section-header {
            border-color: #27272a;
        }
    </style>
    @endonce

    <div class="space-y-6" style="display: flex; flex-direction: column; gap: 1.5rem; width: 100%;">
        @php
            $host = $this->getHostSpecs();
            $devices = $this->getDevicesData();
        @endphp

        <x-filament::section icon="heroicon-o-server" icon-color="primary">
            <x-slot name="heading">
                Deteksi Spesifikasi Komputasi
            </x-slot>

            <x-slot name="description">
                Spesifikasi komputasi aplikasi PESAT saat ini.
            </x-slot>

            <div class="specs-grid-4">
                <!-- Card CPU -->
                <div class="specs-card">
                    <div class="specs-flex-align" style="margin-bottom: 0.75rem;">
                        <div class="specs-icon-container specs-bg-indigo">
                            <x-filament::icon icon="heroicon-o-cpu-chip" style="width: 1.25rem; height: 1.25rem;" />
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <p class="specs-text-muted" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Prosesor (CPU)</p>
                            <h4 class="specs-text-title" style="word-break: break-word;">{{ $host['cpu_name'] }}</h4>
                        </div>
                    </div>
                    <div class="specs-divider"></div>
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <div class="specs-meta-row">
                            <span class="specs-text-muted">Jumlah Core:</span>
                            <span class="specs-text-value">{{ $host['cpu_cores'] }} Cores ({{ $host['cpu_threads'] }} Threads)</span>
                        </div>
                        <div class="specs-meta-row">
                            <span class="specs-text-muted">Kecepatan:</span>
                            <span class="specs-text-value">{{ $host['cpu_speed'] }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card RAM -->
                <div class="specs-card">
                    <div class="specs-flex-align" style="margin-bottom: 0.75rem;">
                        <div class="specs-icon-container specs-bg-emerald">
                            <x-filament::icon icon="heroicon-o-bolt" style="width: 1.25rem; height: 1.25rem;" />
                        </div>
                        <div>
                            <p class="specs-text-muted" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Memori (RAM)</p>
                            <h4 class="specs-text-title">Total: {{ $host['ram_total'] }} GB</h4>
                        </div>
                    </div>
                    <div class="specs-divider"></div>
                    <div class="specs-progress-container">
                        <div class="specs-flex-between">
                            <span class="specs-text-muted">Beban RAM</span>
                            <span class="specs-text-value">{{ $host['ram_used_percent'] }}%</span>
                        </div>
                        <div class="specs-progress-track">
                            <div class="specs-progress-fill specs-fill-emerald" style="width: {{ $host['ram_used_percent'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card Disk -->
                <div class="specs-card">
                    <div class="specs-flex-align" style="margin-bottom: 0.75rem;">
                        <div class="specs-icon-container specs-bg-amber">
                            <x-filament::icon icon="heroicon-o-circle-stack" style="width: 1.25rem; height: 1.25rem;" />
                        </div>
                        <div>
                            <p class="specs-text-muted" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Penyimpanan (Disk)</p>
                            <h4 class="specs-text-title">Tipe: {{ $host['disk_type'] }}</h4>
                        </div>
                    </div>
                    <div class="specs-divider"></div>
                    <div class="specs-progress-container">
                        <div class="specs-flex-between">
                            <span class="specs-text-small" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">Terpakai: {{ number_format($host['disk_total'] - $host['disk_free'], 1) }} GB / {{ $host['disk_total'] }} GB</span>
                            <span class="specs-text-value">{{ $host['disk_used_percent'] }}%</span>
                        </div>
                        <div class="specs-progress-track">
                            <div class="specs-progress-fill specs-fill-amber" style="width: {{ $host['disk_used_percent'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Card OS -->
                <div class="specs-card">
                    <div class="specs-flex-align" style="margin-bottom: 0.75rem;">
                        <div class="specs-icon-container specs-bg-purple">
                            <x-filament::icon icon="heroicon-o-command-line" style="width: 1.25rem; height: 1.25rem;" />
                        </div>
                        <div style="min-width: 0; flex: 1;">
                            <p class="specs-text-muted" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">Sistem Operasi</p>
                            <h4 class="specs-text-title" style="word-break: break-word;">{{ $host['os'] }}</h4>
                        </div>
                    </div>
                    <div class="specs-divider"></div>
                    <div style="display: flex; flex-direction: column; gap: 0.15rem;">
                        <div class="specs-meta-row">
                            <span class="specs-text-muted">PHP:</span>
                            <span class="specs-text-value">v{{ $host['php_version'] }}</span>
                        </div>
                        <div class="specs-meta-row">
                            <span class="specs-text-muted">Laravel:</span>
                            <span class="specs-text-value">v{{ $host['laravel_version'] }}</span>
                        </div>
                        <div class="specs-meta-row">
                            <span class="specs-text-muted">Database:</span>
                            <span class="specs-text-value" style="text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-w-[80px];">{{ Str::limit($host['db_version'], 10) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section icon="heroicon-o-cpu-chip" icon-color="success">
            <x-slot name="heading">
                Deteksi Status & Beban Node Perangkat Edge
            </x-slot>

            <x-slot name="description">
                Monitoring beban komputasi real-time untuk masing-masing Mini PC/perangkat edge yang terdaftar.
            </x-slot>

            @if(empty($devices))
                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 2.5rem 0; text-align: center;">
                    <div class="specs-icon-container" style="background-color: #f1f5f9; color: #94a3b8; margin-bottom: 0.75rem;">
                        <x-filament::icon icon="heroicon-o-cpu-chip" style="width: 2rem; height: 2rem;" />
                    </div>
                    <h3 class="specs-text-title">Tidak Ada Perangkat Edge yang Terdaftar</h3>
                    <p class="specs-text-muted" style="margin-top: 0.25rem;">Node edge akan muncul secara otomatis setelah mengirimkan heartbeat.</p>
                </div>
            @else
                <div class="specs-grid-3">
                    @foreach($devices as $device)
                        <div class="specs-card">
                            <div class="specs-section-header">
                                <div class="specs-flex-align">
                                    <div class="specs-icon-container" style="background-color: #f1f5f9; color: #475569;">
                                        <x-filament::icon icon="heroicon-o-computer-desktop" style="width: 1rem; height: 1rem;" />
                                    </div>
                                    <div>
                                        <h4 class="specs-text-title">{{ $device['device_id'] }}</h4>
                                        <p class="specs-text-muted" style="font-size: 0.65rem;">Edge Device Node</p>
                                    </div>
                                </div>

                                <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem;">
                                    @if($device['is_online'])
                                        <span class="specs-badge specs-badge-online">
                                            <span class="specs-badge-dot specs-badge-dot-online" style="animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;"></span>
                                            Online
                                        </span>
                                    @else
                                        <span class="specs-badge specs-badge-offline">
                                            <span class="specs-badge-dot specs-badge-dot-offline"></span>
                                            Offline
                                        </span>
                                    @endif
                                    <span class="specs-text-small" style="font-size: 0.6rem;">
                                        {{ $device['last_heartbeat_at'] ? \Carbon\Carbon::parse($device['last_heartbeat_at'])->diffForHumans() : 'N/A' }}
                                    </span>
                                </div>
                            </div>

                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <div class="specs-progress-container">
                                    <div class="specs-flex-between">
                                        <span class="specs-text-muted">Beban CPU Edge</span>
                                        <span class="specs-text-value">
                                            {{ $device['current_cpu'] !== null ? number_format($device['current_cpu'], 1) . '%' : 'Offline/Idle' }}
                                        </span>
                                    </div>
                                    <div class="specs-progress-track">
                                        <div class="specs-progress-fill specs-fill-indigo" style="width: {{ $device['current_cpu'] ?? 0 }}%"></div>
                                    </div>
                                </div>

                                <div class="specs-progress-container">
                                    <div class="specs-flex-between">
                                        <span class="specs-text-muted">Beban RAM Edge</span>
                                        <span class="specs-text-value">
                                            {{ $device['current_ram'] !== null ? number_format($device['current_ram'], 1) . '%' : 'Offline/Idle' }}
                                        </span>
                                    </div>
                                    <div class="specs-progress-track">
                                        <div class="specs-progress-fill specs-fill-emerald" style="width: {{ $device['current_ram'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="specs-divider"></div>

                            <div>
                                <h5 class="specs-text-muted" style="font-size: 0.65rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; margin-bottom: 0.35rem;">Kamera Aktif di Node ({{ count($device['cameras']) }})</h5>
                                @if(empty($device['cameras']))
                                    <p class="specs-text-muted" style="font-style: italic; font-size: 0.7rem;">Tidak ada kamera terhubung</p>
                                @else
                                    <div class="specs-tag-container">
                                        @foreach($device['cameras'] as $camName)
                                            <span class="specs-tag">
                                                {{ $camName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-widgets::widget>
