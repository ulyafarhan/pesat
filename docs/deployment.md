# Panduan Deployment & Orkestrasi

## 1. Setup VPS Server (OpenLiteSpeed + MariaDB + PHP 8.3)

### 1.1 Prasyarat
- PHP 8.3+ & Composer 2.x
- Node.js 22+ & PNPM
- MariaDB 10.x+ (atau MySQL 8.x)
- OpenLiteSpeed (bawaan aaPanel/cPanel/Plesk/DirectAdmin)

### 1.2 Instalasi Aplikasi
```bash
git clone <repo-url> pesat && cd pesat
composer install --no-dev --optimize-autoloader
pnpm install && pnpm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --force
```

### 1.3 Konfigurasi .env untuk Produksi
```env
APP_ENV=production
APP_DEBUG=false
DB_HOST=/var/run/mysqld/mysqld.sock  # UNIX socket
DB_DATABASE=pesat
DB_USERNAME=pesat_user
DB_PASSWORD=****
CACHE_STORE=file
BROADCAST_CONNECTION=log
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
```

### 1.4 MariaDB Tuning (1GB RAM)
Baris berikut di `/etc/my.cnf.d/mariadb-server.cnf` atau `my.cnf`:
```ini
[mysqld]
innodb_buffer_pool_size=128M
innodb_flush_log_at_trx_commit=2
innodb_log_file_size=64M
innodb_flush_method=O_DIRECT
max_connections=50
query_cache_type=0
```

### 1.5 PHP-FPM & OpCache Tuning
```ini
; php.ini atau opcache.ini
opcache.enable=1
opcache.memory_consumption=64
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.preload=/path/to/pesat/preload.php
opcache.preload_user=www-data
opcache.jit=off          ; Aktifkan jika VPS >= 2GB: opcache.jit=tracing
```

### 1.6 X-Sendfile untuk OpenLiteSpeed
Pastikan mod `X-LiteSpeed-Location` aktif (bawaan OpenLiteSpeed):
```apache
# Rewrite rule di .htaccess sudah handle X-Sendfile header
# PHP cukup kirim header: X-LiteSpeed-Location: /full/path/file.jpg
# OLS serve file langsung, 0 PHP memory
```

### 1.7 Static Asset Cache (.htaccess)
File `public/.htaccess` sudah menyertakan:
```apache
<FilesMatch "\.(css|js|woff2|ttf|eot|svg|png|jpg|gif|ico|webp)$">
    Header set Cache-Control "public, immutable, max-age=31536000"
</FilesMatch>
```

### 1.8 Cron Job (Laravel Scheduler)
```bash
* * * * * cd /path/to/pesat && php artisan schedule:run >> /dev/null 2>&1
```

---

## 2. Setup Local Mini PC (Edge Device CCTV Pipeline)

### 2.1 Prasyarat
- Python 3.13+
- Kamera CCTV (RTSP/HTTP) atau webcam index
- Minimal 2GB RAM (untuk 3-7 stream)

### 2.2 Instalasi
```bash
cd mlcv
python -m venv venv
# Windows: venv\Scripts\activate
# Linux:   source venv/bin/activate
pip install -r requirements.txt
```

### 2.3 Konfigurasi (`mlcv/edge_config.yaml`)
```yaml
api_base: "https://your-vps.com"            # URL server Laravel
api_key: "your-api-key"                      # Kunci API (PESAT_API_KEY)

models:
  yolo: "best_yolo_pose.onnx"
  classifier: "best_deteksi_int8.onnx"
  rules: "sistem_pakar.yaml"

inference_interval_seconds: 5
min_confidence: 0.75
heartbeat_interval_seconds: 30
```

### 2.4 Jalankan Orchestrator
```bash
python orchestrator.py
```

### 2.5 Systemd Service (Linux)
```ini
[Unit]
Description=PESAT Edge Orchestrator
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/opt/pesat/mlcv
ExecStart=/opt/pesat/mlcv/venv/bin/python orchestrator.py
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

---

## 3. Upgrade Path ke 2GB VPS

Jika VPS di-upgrade ke 2GB RAM:
1. Aktifkan opcache.jit di php.ini
2. Install Redis: `CACHE_STORE=redis`, `SESSION_DRIVER=redis`
3. `BROADCAST_CONNECTION=reverb` + `QUEUE_CONNECTION=database`
4. Install dan jalankan queue worker: `php artisan queue:work`
5. **Zero code changes** — cukup .env + php.ini tuning.
