# Skema Database & Strategi Indeks

PESAT dinormalisasi ke tingkat **3rd Normal Form (3NF)** untuk integritas data log frekuensi tinggi.

## Relasi ERD Ringkas

```
  ┌─────────────┐          ┌────────────────┐          ┌─────────────────┐
  │   CAMERAS   │1        *│ DETECTION_LOGS │*        1│DETECTION_LABELS │
  ├─────────────┤─────────►├────────────────┤◄─────────├─────────────────┤
  │ PK: id      │          │ PK: id         │          │ PK: id          │
  │ location    │          │ FK: camera_id  │          │ name (unique)   │
  │ lat, lng    │          │ FK: label_id   │          └─────────────────┘
  │ is_active   │          │ confidence     │
  │ edge_device_id*│       │ created_at     │
  │ last_heartbeat*│       └────────────────┘
  │ edge_metrics*│
  └─────────────┘
```

---

## Detail Struktur Tabel Utama

### 1. Tabel `users`
Mewakili pengguna sistem (Admin, Petugas Wilayatul Hisbah, dan Masyarakat).
* `id` (unsignedBigInteger, PK): ID Unik Pengguna.
* `name` (string): Nama lengkap.
* `email` (string, unique): Surel untuk login.
* `password` (string): Kata sandi terenkripsi.
* `role` (string, default: `'citizen'`): Peran akses. Nilai yang diperbolehkan: `'admin'`, `'wh_officer'`, `'citizen'`.

### 2. Tabel `cameras`
Menyimpan data kamera pemantau (CCTV) dan status telemetri edge device.
* `id` (string, PK): ID Kamera kustom (contoh: `CAM-001`).
* `location_name` (string): Nama lokasi penempatan kamera.
* `latitude` / `longitude` (decimal): Koordinat geografis peta.
* `stream_source` (string): Sumber RTSP/HTTP feed simulasi.
* `is_active` (boolean): Status aktif/tidaknya kamera.
* `edge_device_id` (string, nullable): ID mesin Edge AI orkestrator pelaksana analisis.
* `last_heartbeat_at` (timestamp, nullable): Waktu detak jantung terakhir dari edge device.
* `edge_metrics` (json, nullable): Data beban spesifikasi edge (`{"cpu": 45.2, "ram": 62.1}`).

### 3. Tabel `detection_logs`
Menyimpan log deteksi anomali yang dikirim secara otomatis oleh skrip inferensi AI.
* `id` (bigint, PK): ID log otomatis.
* `camera_id` (string, FK -> `cameras.id`): Relasi ke kamera.
* `label_id` (unsignedBigInteger, FK -> `detection_labels.id`): Relasi ke label deteksi.
* `confidence_score` (decimal): Nilai akurasi inferensi model AI (0.00 hingga 1.00).
* `violation_category` (string, nullable): Kategori pelanggaran syariat.
* `snapshot` (string, nullable): Nama file gambar bukti visual (`{camera_id}_{timestamp}.jpg`).
* `created_at` (timestamp): Waktu terjadinya deteksi.

### 4. Tabel `citizen_reports`
Menyimpan laporan pengaduan pelanggaran yang dikirim oleh masyarakat atau dari *AI trigger events*.
* `id` (bigint, PK): ID Laporan.
* `location_name` (string): Deskripsi lokasi kejadian.
* `latitude` / `longitude` (decimal): Koordinat peta lokasi kejadian.
* `media_path` (string, nullable): Lokasi file foto/video bukti laporan.
* `source` (string): Asal laporan (`Masyarakat` atau `Deteksi AI`).
* `violation_category` (string): Kategori pelanggaran syariat.
* `status` (string, default: `'pending'`): Status peninjauan laporan (`pending`, `verified`, `rejected`).
* `verified_by` (unsignedBigInteger, FK -> `users.id`, nullable): Petugas verifikator laporan.
* `verification_notes` (text, nullable): Catatan verifikasi/alasan penolakan dari petugas.
* `reported_at` (timestamp): Waktu pengiriman laporan.

---

## Optimasi Index DDL MariaDB

```sql
-- Indeks utama untuk polling (range scan + filter per camera)
CREATE INDEX idx_dl_date_confidence ON detection_logs (created_at DESC, confidence_score DESC);

-- Indeks komposit untuk join dengan eager loading (model pakai $with=['label'])
CREATE INDEX idx_dl_camera_label_date ON detection_logs (camera_id, label_id, created_at DESC);

-- Indeks untuk citizen_reports status lookup
CREATE INDEX idx_cr_location_status ON citizen_reports (location_name(100), status);

-- Indeks untuk edge device heartbeat queries
CREATE INDEX idx_cameras_edge_device ON cameras (edge_device_id);
CREATE INDEX idx_cameras_heartbeat ON cameras (last_heartbeat_at) WHERE edge_device_id IS NOT NULL;
```

---

## MariaDB Tuning (1GB VPS)

```ini
[mysqld]
innodb_buffer_pool_size=128M
innodb_flush_log_at_trx_commit=2
innodb_log_file_size=64M
innodb_flush_method=O_DIRECT
max_connections=50
```

`innodb_flush_log_at_trx_commit=2` meningkatkan insert speed 2-5× dengan resiko kehilangan 1 detik data saat database crash — sangat aman dan direkomendasikan untuk telemetry logs.

---

## Query Pattern

### Telemetry Store (insert)
```sql
INSERT INTO detection_logs (camera_id, label_id, confidence_score, violation_category, snapshot) VALUES (?, ?, ?, ?, ?);
```
~7-10ms per POST di VPS 1GB.

### Telemetry Latest (polling)
```sql
SELECT * FROM detection_logs
WHERE id > ? AND camera_id = ?
ORDER BY id DESC LIMIT 30;
```
~5-15ms per GET. Composite index `idx_dl_camera_label_date` mencakup filter kamera secara instan.

