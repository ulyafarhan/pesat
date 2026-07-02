# QA Bug Report — SIWAS System Validation

Dokumen ini mencakup hasil pengujian otomatis sistem SIWAS menggunakan **Pest PHP** (back-end), **Vitest** (front-end), dan **pytest** (Python edge).

## Ringkasan Eksekusi Test

**109 tests, 0 failures** — 100% Passed.

| Suite | Bahasa/Framework | Jumlah Test | Status |
|---|---|---|---|
| Backend API | PHP / Pest | 62 (162 assertions) | ✅ PASSED |
| Frontend Component | JS / Vitest | 41 | ✅ PASSED |
| Edge Pipeline | Python / pytest | 6 | ✅ PASSED |

---

## Detail Backend Tests (62 PHP Pest)

| Kategori | File | Skenario | Status |
|---|---|---|---|
| **Camera Model** | ModelsTest | casts, fillable, relasi, scopeByEdgeDevice | ✅ PASSED |
| **Telemetry Store** | SiwasTest | Auth valid/invalid, validasi input, firstOrCreate label, broadcast event | ✅ PASSED |
| **Telemetry Latest** | SiwasTest | Empty data, after_id filter, camera_id filter, meta.total_today | ✅ PASSED |
| **Edge API** | SiwasTest | Camera list by device_id, heartbeat dengan/tanpa metrics | ✅ PASSED |
| **Security** | SecurityTest | Bypass token, XSS, negative confidence, oversized media, edge validation | ✅ PASSED |
| **Performance** | PerformanceTest | N+1 prevention (3 queries with eager loading), composite index (2 queries with `$with=['label']`), upsert via API | ✅ PASSED |
| **Citizen Reports** | SiwasTest | Simpan laporan, break mode manual & terjadwal | ✅ PASSED |
| **WH Officer** | SiwasTest | Filter antrean, verifikasi/tolak laporan | ✅ PASSED |
| **Admin Settings** | SiwasTest | Baca & simpan break mode | ✅ PASSED |
| **Middleware Auth** | SiwasTest | Guest redirect, WH only /dashboard/reports, Admin access | ✅ PASSED |

## Detail Frontend Tests (41 Vitest)

| Kategori | Skenario | Status |
|---|---|---|
| **Dashboard** | Camera markers, edge device filter, realtime polling merge | ✅ PASSED |
| **Devices** | Device list, expand camera details, online/offline badges | ✅ PASSED |
| **Components** | Inertia page rendering, Leaflet integration | ✅ PASSED |

## Detail Edge Tests (6 pytest)

| Kategori | Skenario | Status |
|---|---|---|
| **Inference Engine** | Thread safety (Lock), model loading | ✅ PASSED |
| **Camera Worker** | _parse_source (RTSP/webcam), deadline scheduling | ✅ PASSED |
| **Orchestrator** | Config loading, heartbeat construction | ✅ PASSED |

---

## Optimasi Terverifikasi

| Optimasi | Dampak | Status |
|---|---|---|
| X-Sendfile detection images | Zero PHP memory untuk JPEG | ✅ Verified |
| opcache.preload | Cold-start elimination | ✅ Verified (config) |
| Cache::remember total_today (60s) | 12× lebih jarang query count | ✅ Tested |
| Immutable cache headers (`.htaccess`) | Static asset cache 1 tahun | ✅ Verified |
| Composite indexes (3 indexes) | Query tanpa full scan | ✅ PerformanceTest |
| Eager loading (camera + label) | N+1 elimination (3 queries) | ✅ PerformanceTest |

## Rekomendasi QA

Sistem dalam kondisi stabil untuk production:
1. Skema database 3NF teruji tanpa N+1.
2. Edge device heartbeat & sync berjalan async.
3. X-Sendfile mencegah OOM pada VPS 1GB saat serve gambar deteksi.
4. Polling 5s dengan `after_id` zero duplikasi via `Set` client.
5. Cache TTL terukur (label 300s, total_today 60s).
