# Arsitektur Sistem PESAT

PESAT dibangun menggunakan pendekatan **Regular Monolith (VPS) + Edge Worker (Python threading)**.

## Topologi Fisik & Runtime

```
                        INTERNET
                           │
              HTTPS API Requests (JSON + snapshot)
              ┌────────────▼──────────────────────────┐
              │          VPS (1GB RAM / 1 core)        │
              │  OpenLiteSpeed + PHP 8.3 FPM + MariaDB │
              │                                        │
              │  ┌─ Laravel Monolith ─────────────────┐ │
              │  │  - Dashboard Web (Inertia Vue 3)    │ │
              │  │  - Telemetry Ingestion API (POST)   │ │
              │  │  - Telemetry Polling API (GET)      │ │
              │  │  - Edge Heartbeat API (POST)        │ │
              │  │  - Device Management UI             │ │
              │  │  - Detection Images (X-Sendfile)    │ │
              │  └────────────────────────────────────┘ │
              │                                        │
              │  Cache: File (default), 60s TTL        │
              │  Queue: sync (no worker needed)         │
              │  Broadcast: log (no Reverb)             │
              └────────────────┬───────────────────────┘
                               │
                               │ POST /api/telemetry/log (7-10ms)
                               │ POST /api/edge/heartbeat (30s)
                               │ GET /api/edge/cameras
                               │
              ┌────────────────▼───────────────────────┐
              │    Edge Device / Local Mini PC          │
              │  Python 3.13 + OpenCV + ONNX Runtime    │
              │                                        │
              │  orchestrator.py                        │
              │   ├─ Sync cameras from API              │
              │   ├─ Spawn per-camera workers           │
              │   └─ Heartbeat every 30s (psutil)       │
              │                                        │
              │  camera_worker.py (per camera)          │
              │   ├─ Thread 1: Reader (buffer flush)    │
              │   ├─ Thread 2: Inference (deadline)     │
              │   └─ Thread 3: TelemetrySender (async)  │
              └────────────────────────────────────────┘
```

## Mekanisme Real-time: Polling (Bukan WebSocket)

**Polling dipilih karena RAM budget VPS 1GB**, bukan keterbatasan PHP.

| Aspek | Polling (5s) | WebSocket/Reverb |
|---|---|---|
| RAM tambahan | 0 MB | 200-300 MB |
| Kompleksitas | Rendah | Tinggi (queue worker) |
| Cocok untuk | VPS 1GB | VPS >= 2GB |
| PHP 8.3 support | Native | Reverb via ReactPHP |

### Alur Incremental Polling
1. **State Tracking**: Dashboard melacak ID log terakhir (`after_id`).
2. **Fetch**: Tiap 5 detik, browser GET `/api/telemetry/latest?after_id=X`.
3. **Deduplication**: `seenLogIds` (Set) di client mencegah duplikasi render.
4. **Total Today**: Di-cache 60s via `Cache::remember`.
5. **Label Cache**: `detection_label_{name}` di-cache 300s.

## Edge Device Architecture

### Per-Camera 3 Threads

Setiap kamera mendapat 3 thread independen agar satu stream lambat tidak memblokir yang lain:

1. **Reader Thread** — Baca frame dari CCTV/RTSP/webcam, flush buffer.
2. **Inference Thread** — Deadline scheduling dengan `time.monotonic()`. Jika inference 2s, jadwal berikutnya +3s untuk maintain interval 5s.
3. **TelemetrySender Thread** — Async queue dengan exponential backoff (`2^n`, max 60s, 5 retries). Kirim JSON + snapshot ke VPS.

### Heartbeat & Metrics

`orchestrator.py` mengirim heartbeat setiap 30s dengan:
- CPU usage (`psutil.cpu_percent()`)
- RAM usage (`psutil.virtual_memory()`)
- Per-camera status (fps, queue depth, last inference time)

VPS menyimpan di kolom `edge_metrics` (JSON) pada tabel `cameras`.

## Optimasi Zero-RAM-Waste

| Optimasi | Detail |
|---|---|
| **X-Sendfile** | Header `X-LiteSpeed-Location` → OLS serve file langsung, 0 PHP memory untuk gambar |
| **opcache.preload** | `preload.php` di root proyek, cold-start elimination |
| **Cache::remember** | `total_today` 60s, label 300s — kurangi query berulang |
| **Immutable Cache** | `.htaccess` set `Cache-Control: immutable` untuk `css\|js\|woff2` build assets |
| **Composite Indexes** | `idx_dl_date_confidence`, `idx_dl_camera_label_date`, `idx_cr_location_status` |
| **Upsert** | `DetectionLog::upsert()` untuk batch insert |

## Device Management & System Monitoring

Semua pemantauan infrastruktur dan perangkat keras tepi kini dikelola secara terpusat dan aman pada panel administrasi Filament:
* **Device Monitoring (`DeviceResource.php`)**: Halaman khusus untuk memantau daftar perangkat edge, status *online/offline* (berdasarkan selisih *heartbeat* 5 menit), serta status CPU/RAM dari edge device di lapangan.
* **Widget Spesifikasi Host (`DeviceSpecsWidget.php`)**: Widget khusus pada halaman dasbor Filament untuk mendeteksi dan menampilkan spesifikasi nyata (RAM, CPU Cores/Threads, Disk Space, edisi OS, jenis web server, dan versi framework) secara langsung dari server host. Pembaruan dilakukan setiap 5 detik secara terisolasi (*Island Architecture*) via Livewire v4 dengan pengamanan cache 5 detik untuk menghemat CPU.

## Skalabilitas Vertikal

Sistem bisa pindah ke **2GB/2core VPS tanpa perubahan kode**:

| Komponen | 1GB VPS (saat ini) | 2GB VPS |
|---|---|---|
| Broadcast | `log` | `reverb` (WebSocket native) |
| Cache | `file` | `redis` |
| Queue | `sync` | `database` (queue worker) |
| JIT | off | `opcache.jit` aktif |

Cukup ubah `.env` + php.ini, **zero code changes**.
