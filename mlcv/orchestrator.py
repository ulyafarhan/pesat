import os
import sys
import time
import socket
import logging
import requests
import yaml
import psutil
from urllib.parse import urljoin

sys.path.insert(0, os.path.join(os.path.dirname(__file__), ".."))

from mlcv.inference_engine import InferenceEngine
from mlcv.camera_worker import CameraWorker

logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s [%(levelname)s] %(name)s: %(message)s'
)
logger = logging.getLogger("pesat.orchestrator")


class Orchestrator:
    def __init__(self, config_path=None, device_id=None):
        if config_path is None:
            config_path = os.path.join(os.path.dirname(__file__), "edge_config.yaml")
        config_dir = os.path.dirname(os.path.abspath(config_path))

        with open(config_path, "r") as f:
            self.config = yaml.safe_load(f)

        self.device_id = device_id or socket.gethostname()
        self.api_base = self.config.get("api_base", "http://localhost:8000")
        self.api_key = self.config.get("api_key", "")
        self.inference_interval = self.config.get("inference_interval_seconds", 5)
        self.heartbeat_interval = self.config.get("heartbeat_interval_seconds", 30)
        self.detections_dir = self.config.get("detections_dir", "storage/app/detections")

        self.engine = InferenceEngine(self.config, config_dir=config_dir)
        self.workers = {}
        self.running = False

    def _get_headers(self):
        headers = {"Accept": "application/json"}
        if self.api_key and self.api_key != "GANTI_DENGAN_API_KEY_ANDA":
            headers["Authorization"] = f"Bearer {self.api_key}"
        return headers

    def fetch_cameras(self):
        url = urljoin(self.api_base, "/api/edge/cameras")
        try:
            resp = requests.get(url, params={"device_id": self.device_id}, headers=self._get_headers(), timeout=10)
            if resp.status_code == 200:
                data = resp.json()
                return data.get("data", [])
            else:
                logger.error(f"Gagal mengambil daftar kamera: HTTP {resp.status_code}")
                return []
        except Exception as e:
            logger.error(f"Error mengambil daftar kamera: {e}")
            return []

    def send_heartbeat(self):
        url = urljoin(self.api_base, "/api/edge/heartbeat")
        metrics = {
            "cpu": psutil.cpu_percent(interval=1),
            "ram": psutil.virtual_memory().percent,
            "cameras": [w.get_status() for w in self.workers.values()]
        }
        try:
            requests.post(url, json={"device_id": self.device_id, "metrics": metrics}, headers=self._get_headers(), timeout=5)
            logger.debug("Heartbeat terkirim")
        except Exception as e:
            logger.warning(f"Gagal kirim heartbeat: {e}")

    def sync_workers(self):
        logger.info("Memulai sinkronisasi worker dengan server...")
        remote_cameras = self.fetch_cameras()
        logger.info(f"Sinkronisasi selesai. Menemukan {len(remote_cameras)} kamera aktif di server.")
        remote_ids = {c["id"]: c for c in remote_cameras}
        current_ids = set(self.workers.keys())

        to_start = set(remote_ids.keys()) - current_ids
        for cid in to_start:
            cam_data = remote_ids[cid]
            source = cam_data.get("stream_source", "0")
            logger.info(f"Memulai worker untuk {cid} (source: {source})")
            worker = CameraWorker(
                cam_data,
                self.engine,
                urljoin(self.api_base, "/api/telemetry/log"),
                self.api_key,
                self.inference_interval,
                report_url=urljoin(self.api_base, "/api/reports"),
                detections_dir=self.detections_dir
            )
            worker.start()
            self.workers[cid] = worker

        to_stop = current_ids - set(remote_ids.keys())
        for cid in to_stop:
            logger.info(f"Menghentikan worker untuk {cid}")
            worker = self.workers.pop(cid)
            worker.stop()

    def run(self):
        self.running = True
        logger.info(f"Memulai Edge Orchestrator untuk device_id: {self.device_id}")

        last_sync = 0
        last_heartbeat = 0

        try:
            while self.running:
                now = time.time()
                
                if now - last_sync >= 60:
                    self.sync_workers()
                    last_sync = now

                if now - last_heartbeat >= self.heartbeat_interval:
                    self.send_heartbeat()
                    last_heartbeat = now

                time.sleep(1)

        except KeyboardInterrupt:
            logger.info("Berhenti karena KeyboardInterrupt")
        finally:
            self.stop_all()

    def stop_all(self):
        self.running = False
        for cid, worker in self.workers.items():
            worker.stop()
        logger.info("Semua worker telah dihentikan")


if __name__ == "__main__":
    import socket
    default_device_id = socket.gethostname()
    device_id = os.environ.get("PESAT_DEVICE_ID", default_device_id)
    orchestrator = Orchestrator(device_id=device_id)
    orchestrator.run()
