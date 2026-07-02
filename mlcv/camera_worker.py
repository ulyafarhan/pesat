import os
import time
import cv2
import requests
import logging
import threading

logger = logging.getLogger("pesat.worker")


class TelemetrySender:
    def __init__(self, api_url, api_key, camera_id, max_retries=5):
        self.api_url = api_url
        self.api_key = api_key
        self.camera_id = camera_id
        self.max_retries = max_retries
        self._queue = []
        self._lock = threading.Lock()
        self._running = False
        self._thread = None

    def enqueue(self, label, confidence, violation_category=None, snapshot=None):
        with self._lock:
            self._queue.append((label, confidence, 0, violation_category, snapshot))

    def _send(self, label, confidence, violation_category=None, snapshot=None):
        headers = {"Content-Type": "application/json"}
        if self.api_key and self.api_key != "GANTI_DENGAN_API_KEY_ANDA":
            headers["Authorization"] = f"Bearer {self.api_key}"

        payload = {
            "camera_id": self.camera_id,
            "label_detected": label,
            "confidence_score": confidence,
        }
        if violation_category:
            payload["violation_category"] = violation_category
        if snapshot:
            payload["snapshot"] = snapshot
        try:
            resp = requests.post(self.api_url, json=payload, headers=headers, timeout=5)
            if resp.status_code == 201:
                logger.info(f"[{self.camera_id}] Telemetri terkirim: {label} ({confidence:.2f})")
                return True
            logger.warning(f"[{self.camera_id}] Gagal: HTTP {resp.status_code}")
            return False
        except requests.RequestException as e:
            logger.warning(f"[{self.camera_id}] Network error: {e}")
            return False

    def _loop(self):
        while self._running:
            item = None
            with self._lock:
                if self._queue:
                    item = self._queue.pop(0)

            if item:
                label, confidence, attempt, category, snapshot = item
                ok = self._send(label, confidence, category, snapshot)
                if not ok and attempt < self.max_retries:
                    backoff = min(2 ** attempt, 60)
                    with self._lock:
                        self._queue.append((label, confidence, attempt + 1, category, snapshot))
                    logger.info(f"[{self.camera_id}] Retry #{attempt + 1} dalam {backoff}s")
                time.sleep(1)
            else:
                time.sleep(0.5)

    def start(self):
        self._running = True
        self._thread = threading.Thread(target=self._loop, daemon=True)
        self._thread.name = f"tx-{self.camera_id}"
        self._thread.start()

    def stop(self):
        self._running = False
        if self._thread and self._thread.is_alive():
            self._thread.join(timeout=5)


class CameraWorker:
    def __init__(self, camera_config, engine, api_url, api_key, inference_interval=15, report_url=None, detections_dir="storage/app/detections"):
        self.camera_id = camera_config["id"]
        self.stream_source = camera_config.get("stream_source", camera_config.get("source"))
        self.location_name = camera_config.get("location_name", self.camera_id)
        self.latitude = camera_config.get("latitude")
        self.longitude = camera_config.get("longitude")
        self.report_url = report_url
        self.engine = engine
        self.api_url = api_url
        self.api_key = api_key
        self.inference_interval = inference_interval
        self.detections_dir = detections_dir
        self.running = False
        self.thread = None
        self._frame_buffer = None
        self._buffer_lock = threading.Lock()
        self._cap = None
        self._tx = TelemetrySender(api_url, api_key, self.camera_id)
        self._last_report_time = 0
        self._last_category_time = {}
        self._report_cooldown = 180

    def _parse_source(self):
        source = self.stream_source
        if source.isdigit():
            return int(source)
        return source

    def _connect_stream(self):
        parsed = self._parse_source()
        
        if isinstance(parsed, str):
            _script_dir = os.path.dirname(os.path.abspath(__file__))
            _project_root = os.path.abspath(os.path.join(_script_dir, ".."))
            dir_path = parsed if os.path.isabs(parsed) else os.path.join(_project_root, parsed)
            if os.path.isdir(dir_path):
                self.is_directory = True
                self.stream_source = dir_path
                logger.info(f"[{self.camera_id}] Menggunakan direktori lokal: {dir_path}")
                return True

        self.is_directory = False
        cap = cv2.VideoCapture(parsed)
        if not cap.isOpened():
            last_char = self.camera_id[-1]
            last_digit = int(last_char) if last_char.isdigit() else 1
            mapped_digit = ((last_digit - 1) % 3) + 1
            _script_dir = os.path.dirname(os.path.abspath(__file__))
            fallback_dir = os.path.join(_script_dir, f"cam{mapped_digit}")
            
            if os.path.isdir(fallback_dir):
                logger.warning(f"[{self.camera_id}] Gagal membuka stream '{self.stream_source}'. Menggunakan fallback: {fallback_dir}")
                self.stream_source = fallback_dir
                self.is_directory = True
                return True
                
            logger.error(f"[{self.camera_id}] Gagal membuka stream: {self.stream_source}")
            return None
        return cap

    def _read_frames(self):
        reconnect_attempts = 0
        self._dir_index = 0
        while self.running:
            if self.is_directory:
                with self._buffer_lock:
                    has_frame = self._frame_buffer is not None
                    
                if not has_frame:
                    try:
                        all_items = os.listdir(self.stream_source)
                        files = sorted([
                            f for f in all_items
                            if f.lower().endswith((".jpg", ".jpeg", ".png"))
                        ], key=lambda x: os.path.getmtime(os.path.join(self.stream_source, x)))
                        if files:
                            idx = self._dir_index % len(files)
                            self._dir_index += 1
                            image_file = os.path.join(self.stream_source, files[idx])
                            frame = cv2.imread(image_file)
                            if frame is not None:
                                with self._buffer_lock:
                                    self._frame_buffer = (frame, image_file)
                                logger.info(f"[{self.camera_id}] Membaca frame: {files[idx]} ({frame.shape[1]}x{frame.shape[0]})")
                            else:
                                logger.warning(f"[{self.camera_id}] Gagal membaca file: {files[idx]}")
                        else:
                            logger.debug(f"[{self.camera_id}] Tidak ada file gambar di {self.stream_source}")
                    except FileNotFoundError:
                        logger.warning(f"[{self.camera_id}] Direktori tidak ditemukan: {self.stream_source}")
                    except Exception as e:
                        logger.error(f"[{self.camera_id}] Gagal membaca file direktori: {e}")
                time.sleep(1)
                continue

            if not self._cap or not self._cap.isOpened():
                break

            ret, frame = self._cap.read()
            if ret:
                reconnect_attempts = 0
                with self._buffer_lock:
                    self._frame_buffer = (frame, None)
            else:
                reconnect_attempts += 1
                logger.warning(f"[{self.camera_id}] Frame gagal ({reconnect_attempts}x)")
                self._cap.release()
                if reconnect_attempts > 10:
                    logger.error(f"[{self.camera_id}] Reconnect limit reached, berhenti")
                    self.running = False
                    break
                time.sleep(min(2 ** reconnect_attempts, 30))
                self._cap = self._connect_stream()
                if self._cap is None:
                    logger.error(f"[{self.camera_id}] Reconnect gagal total")
                    self.running = False
                    break

    def _inference_loop(self):
        logger.info(f"[{self.camera_id}] Inference loop dimulai (interval: {self.inference_interval}s)")
        next_deadline = time.monotonic()
        _cycle = 0

        while self.running:
            now = time.monotonic()
            sleep_time = next_deadline - now
            if sleep_time > 0:
                time.sleep(sleep_time)

            next_deadline = time.monotonic() + self.inference_interval
            _cycle += 1

            frame = None
            filepath = None
            with self._buffer_lock:
                if self._frame_buffer is not None:
                    if isinstance(self._frame_buffer, tuple):
                        frame, filepath = self._frame_buffer
                    else:
                        frame = self._frame_buffer
                        filepath = None

            if frame is None:
                logger.debug(f"[{self.camera_id}] Siklus #{_cycle}: menunggu frame...")
                continue

            logger.debug(f"[{self.camera_id}] Siklus #{_cycle}: memproses frame ({frame.shape[1]}x{frame.shape[0]})")

            try:
                with self.engine.lock:
                    alerts, annotated_frame = self.engine.process_frame(frame.copy())

                src_label = f" ({os.path.basename(filepath)})" if filepath else ""
                logger.info(f"[{self.camera_id}] Hasil inferensi: {len(alerts)} alert(s){src_label}")

                if alerts:
                    now = time.time()
                    deduped = []
                    for a in alerts:
                        cat = a.get("category", "Lainnya")
                        last = self._last_category_time.get(cat, 0)
                        if now - last >= 30:
                            deduped.append(a)
                            self._last_category_time[cat] = now

                    if not deduped:
                        logger.debug(f"[{self.camera_id}] Semua alert kena dedup, skip")
                        continue

                    os.makedirs(self.detections_dir, exist_ok=True)
                    ts = int(now)
                    snapshot_name = f"{self.camera_id}_{ts}.jpg"
                    img_path = f"{self.detections_dir}/{snapshot_name}"
                    cv2.imwrite(img_path, annotated_frame)
                    latest_path = f"{self.detections_dir}/latest_{self.camera_id}.jpg"
                    cv2.imwrite(latest_path, annotated_frame)

                    combined = " | ".join(a["label_text"] for a in deduped)
                    categories = list(set(a.get("category", "Lainnya") for a in deduped))
                    primary_category = categories[0] if categories else "Lainnya"
                    logger.info(f"[{self.camera_id}] Deteksi{src_label}: {combined}")

                    for alert in deduped:
                        self._tx.enqueue(alert["label_text"], alert["confidence"], alert.get("category"), snapshot_name)

                    if self.report_url and self._can_send_report():
                        self._last_report_time = now
                        self.send_citizen_report(combined, img_path, primary_category)

            except Exception as e:
                logger.error(f"[{self.camera_id}] Error inferensi: {e}", exc_info=True)

            finally:
                with self._buffer_lock:
                    self._frame_buffer = None

    def _can_send_report(self):
        return (time.time() - self._last_report_time) >= self._report_cooldown

    def send_citizen_report(self, combined_label, img_path, violation_category=None):
        location_field = f"{self.location_name} - {combined_label}"
        if len(location_field) > 250:
            location_field = location_field[:247] + "..."

        payload = {
            "location_name": location_field,
            "source": "ai_detection",
        }
        if violation_category:
            payload["violation_category"] = violation_category
        if self.latitude is not None:
            payload["latitude"] = float(self.latitude)
        if self.longitude is not None:
            payload["longitude"] = float(self.longitude)

        files = {}
        if os.path.exists(img_path):
            files["media"] = ("report.jpg", open(img_path, "rb"), "image/jpeg")

        headers = {}
        if self.api_key and self.api_key != "GANTI_DENGAN_API_KEY_ANDA":
            headers["Authorization"] = f"Bearer {self.api_key}"

        try:
            resp = requests.post(self.report_url, data=payload, files=files, headers=headers, timeout=10)
            if resp.status_code == 201:
                logger.info(f"[{self.camera_id}] Citizen Report terkirim: {location_field}")
            else:
                logger.warning(f"[{self.camera_id}] Gagal kirim Citizen Report: HTTP {resp.status_code}")
        except Exception as e:
            logger.warning(f"[{self.camera_id}] Gagal kirim Citizen Report: {e}")
        finally:
            if "media" in files:
                files["media"][1].close()

    def start(self):
        if self.running:
            return

        self.running = True
        self._cap = self._connect_stream()
        if self._cap is None:
            logger.error(f"[{self.camera_id}] Stream tidak tersedia")
            self.running = False
            return

        self._reader_thread = threading.Thread(target=self._read_frames, daemon=True)
        self._reader_thread.name = f"reader-{self.camera_id}"
        self._reader_thread.start()

        self.thread = threading.Thread(target=self._inference_loop, daemon=True)
        self.thread.name = f"inference-{self.camera_id}"
        self.thread.start()

        self._tx.start()
        logger.info(f"[{self.camera_id}] Worker dimulai")

    def stop(self):
        self.running = False
        self._tx.stop()
        if self._cap and not isinstance(self._cap, bool):
            self._cap.release()
        if self.thread and self.thread.is_alive():
            self.thread.join(timeout=5)
        logger.info(f"[{self.camera_id}] Worker dihentikan")

    def is_alive(self):
        return self.thread is not None and self.thread.is_alive()

    def get_status(self):
        return {
            "camera_id": self.camera_id,
            "source": self.stream_source,
            "running": self.running,
            "alive": self.is_alive()
        }
