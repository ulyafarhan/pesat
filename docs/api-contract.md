# Kontrak API Resmi PESAT

Semua API mengembalikan format JSON. Dokumentasi detail interaktif via Scribe pada `/docs`.

**Autentikasi:** Bearer Token (`Authorization: Bearer {api_key}`) untuk semua endpoint API internal kecuali yang bersifat publik.

---

## 1. Kirim Log Telemetri AI

* **Endpoint:** `POST /api/telemetry/log`
* **Keamanan:** Bearer Token (`PESAT_API_KEY`)
* **Rate Limit:** 60 req/menit

**Payload:**
```json
{
  "camera_id": "CAM-001",
  "label_detected": "Khalwat",
  "confidence_score": 0.925,
  "violation_category": "Khalwat",
  "snapshot": "CAM-001_1782723457.jpg"
}
```

**Response (201 Created):**
```json
{
  "status": "success",
  "data": {
    "id": 4820,
    "camera_id": "CAM-001",
    "label_id": 12,
    "confidence_score": "0.925",
    "violation_category": "Khalwat",
    "snapshot": "CAM-001_1782723457.jpg",
    "created_at": "2026-06-19T21:00:00.000000Z",
    "camera": {
      "id": "CAM-001",
      "location_name": "Taman Riyadhah"
    },
    "label": {
      "id": 12,
      "name": "Khalwat"
    }
  }
}
```

---

## 2. Polling Log Deteksi Terbaru

* **Endpoint:** `GET /api/telemetry/latest`
* **Keamanan:** Tidak (public read-only)

**Params:**
- `after_id` (int, opsional): ID log terakhir client
- `camera_id` (string, opsional): Filter per kamera
- `limit` (int, opsional, default: 30, max: 100)

**Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": 4821,
      "camera_id": "CAM-001",
      "label_id": 12,
      "confidence_score": "0.890",
      "violation_category": "Pergaulan Bebas",
      "snapshot": "CAM-001_1782723467.jpg",
      "created_at": "2026-06-19T21:01:00.000000Z",
      "camera": { "id": "CAM-001", "location_name": "Taman Riyadhah" },
      "label": { "id": 12, "name": "Pergaulan Bebas" }
    }
  ],
  "meta": {
    "total_today": 124,
    "latest_id": 4821
  }
}
```

---

## 3. Edge API — Daftar Kamera

* **Endpoint:** `GET /api/edge/cameras?device_id={id}`
* **Keamanan:** Bearer Token (`PESAT_API_KEY`)

**Response (200 OK):**
```json
{
  "status": "success",
  "data": [
    {
      "id": "CAM-001",
      "location_name": "Taman Riyadhah",
      "stream_source": "rtsp://192.168.1.100:554/stream1",
      "is_active": true,
      "edge_device_id": "EDGE-001",
      "last_heartbeat_at": "2026-06-20T10:00:00.000000Z",
      "edge_metrics": {
        "cpu": 45.2,
        "ram": 62.1
      }
    }
  ]
}
```

---

## 4. Edge API — Heartbeat

* **Endpoint:** `POST /api/edge/heartbeat`
* **Keamanan:** Bearer Token (`PESAT_API_KEY`)

**Payload:**
```json
{
  "device_id": "EDGE-001",
  "metrics": {
    "cpu": 45.2,
    "ram": 62.1
  }
}
```

**Response (200 OK):**
```json
{
  "status": "success",
  "message": "Heartbeat diterima"
}
```

---

## 5. CCTV Snapshot Image Access (Auth-Protected)

* **Endpoint 1 (Gambar Deteksi Spesifik):** `GET /detections/snap/{snapshot}`
* **Endpoint 2 (Tangkapan Terkini Kamera):** `GET /detections/{camera}`
* **Keamanan:** Laravel Auth Session (Middleware `auth`)
* **Serve Engine:** X-Sendfile (Header `X-LiteSpeed-Location` di produksi OpenLiteSpeed)

---

## 6. Kirim Laporan Warga / AI Event

* **Endpoint:** `POST /api/reports`
* **Keamanan:** Bearer Token / Web Auth Session
* **Content-Type:** multipart/form-data
* **Rate Limit Cooldown:** 180 detik per IP/perangkat

**Fields:**
- `location_name` (string, required)
- `latitude` (decimal, optional)
- `longitude` (decimal, optional)
- `media` (file: image/video, max 20MB, optional)
- `source` (string, optional, values: `'Masyarakat'`, `'Deteksi AI'`, default: `'Masyarakat'`)
- `violation_category` (string, optional, values: `'Pakaian Tidak Syar'i'`, `'Khalwat'`, `'Celana Pendek'`, `'Pergaulan Bebas'`)

**Response (201 Created):**
```json
{
  "status": "success",
  "data": {
    "id": 105,
    "location_name": "Taman Riyadhah",
    "status": "pending",
    "source": "Masyarakat",
    "violation_category": "Khalwat",
    "is_break_dispatch": false
  }
}
```

