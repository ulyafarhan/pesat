# PESAT - Sistem Informasi Pengawasan Smart City
### Dokumentasi Teknis Sistem (GEMASTIK 2026)

PESAT adalah Command Center platform pengawasan smart city terpusat yang menggabungkan integrasi AI Vision (Edge Detection) dengan visualisasi spasial peta dan telemetry log feed secara real-time.

---

## Spesifikasi Teknis / Stack Version

| Komponen | Framework / Library | Versi | Deskripsi |
|---|---|---|---|
| **Backend** | Laravel | `^13.7` | Inti engine REST API dan routing |
| **Frontend** | Vue.js | `^3.5` | Reactive UI view layer |
| **Monolith Bridge** | InertiaJS | `^3.0` | Server-driven routing & hydration |
| **Realtime** | HTTP Polling (5s) | — | Incremental polling via `after_id`, hemat RAM VPS |
| **Map Engine** | Leaflet | `^1.9` | Spatial map rendering |
| **Styling** | Tailwind CSS | `^4.1` | Utility CSS framework |
| **Database** | SQLite (test) / MariaDB (prod) | PHP ^8.3 | Relasi 3NF & composite indexes |
| **Edge AI** | Python 3.13 + ultralytics + onnxruntime | 8.4.71 / 1.27.0 | YOLO + classifier pipeline lokal |
| **API Docs Generator** | Knuckles Scribe | `^5.11` | Auto-generasi OpenAPI & Postman spec |
| **Testing Backend** | Pest PHP | `^4.7` | Unit/feature tests (62 tests) |
| **Testing Frontend** | Vitest / JSDOM | `^4.1` / `^29.1` | JS test execution (41 tests) |
| **Testing Edge** | pytest | — | Python pipeline tests (6 tests) |

---

## Menu Dokumentasi

1. **[Arsitektur Sistem (architecture.md)](architecture.md)** — Alur VPS (Laravel) + Edge (Python threading), polling vs WebSocket.
2. **[Kontrak API Resmi (api-contract.md)](api-contract.md)** — Spesifikasi JSON REST payloads, edge API, dan endpoint dokumentasi.
3. **[Skema Database & Indeks (database-schema.md)](database-schema.md)** — Struktur DDL relasi 3NF, kolom edge device, dan strategi indeks.
4. **[Panduan Deployment & Orkestrasi (deployment.md)](deployment.md)** — Setup VPS OpenLiteSpeed + MariaDB, tuning opcache, dan edge worker Python.

---

## Fitur Utama

- **AI Edge Detection** — YOLO + classifier ONNX pipeline per kamera (3 thread: reader, inference deadline, telemetry async).
- **Device Management** — Daftar edge device dengan CPU/RAM metrics, online/offline status, dan daftar kamera.
- **Dashboard Filter** — Dropdown filter kamera per edge device, marker peta otomatis rebuild.
- **Incremental Polling** — 5s interval dengan `after_id`, zero duplikasi via `Set` client-side.
- **X-Sendfile** — Serving gambar deteksi langsung dari OpenLiteSpeed tanpa beban PHP memory.
- **109 Test Suites** — 62 PHP Pest + 41 Vitest + 6 pytest = 100% passing.
