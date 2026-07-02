import os
import cv2
import numpy as np
import yaml
import threading
import logging
from ultralytics import YOLO

from onnxruntime import InferenceSession


class InferenceEngine:
    def __init__(self, config, config_dir=None):
        self.config = config
        self.lock = threading.Lock()

        def resolve(path):
            if config_dir and not os.path.isabs(path):
                return os.path.join(config_dir, path)
            return path

        yolo_path = resolve(config["models"]["yolo"])
        classifier_path = resolve(config["models"]["classifier"])
        rules_path = resolve(config["models"]["rules"])

        self.yolo = YOLO(yolo_path, task="pose")

        sess = InferenceSession(classifier_path, providers=["CPUExecutionProvider"])
        self.classifier_sess = sess
        self.classifier_input_name = sess.get_inputs()[0].name

        with open(rules_path, "r") as f:
            self.rules = yaml.safe_load(f)

        self.label_columns = [
            "subjek_pria", "rambut_panjang", "hijab_kain", "penutup_pria",
            "baju_terusan", "atasan_ketat", "atasan_pendek", "pakai_rok",
            "celana_longgar", "bawahan_ketat", "bawahan_pendek"
        ]
        self.min_confidence = config.get("min_confidence", 0.7)

    def classify_attributes(self, crop_img):
        input_data = self._preprocess_crop(crop_img)
        preds = self.classifier_sess.run(None, {self.classifier_input_name: input_data})[0][0]
        return {self.label_columns[i]: float(preds[i]) for i in range(len(self.label_columns))}

    @staticmethod
    def _preprocess_crop(crop_img):
        img = cv2.resize(crop_img, (224, 224))
        img = img.astype(np.float32) / 255.0
        img = np.expand_dims(img, axis=0)
        return img

    def _get_violation_category(self, rule_id):
        if not rule_id:
            return "Lainnya"
        if "R-PKN" in rule_id:
            return "Pakaian Tidak Syar'i"
        if "R-KHL" in rule_id:
            return "Khalwat"
        if "R-HYB" in rule_id:
            return "Pergaulan Bebas"
        if "R-LKS" in rule_id:
            return "Peringatan"
        return "Lainnya"

    @staticmethod
    def _infer_shorts_from_keypoints(person):
        kps = person.get("keypoints")
        if kps is None or len(kps) < 17:
            return None
        box = person["box"]
        box_h = box[3] - box[1]
        if box_h < 1:
            return None
        dists = []
        for knee_idx, ankle_idx in [(13, 15), (14, 16)]:
            knee = kps[knee_idx]
            ankle = kps[ankle_idx]
            if knee[0] > 0 and ankle[0] > 0:
                dists.append(abs(ankle[1] - knee[1]))
        if not dists:
            return None
        avg_dist = sum(dists) / len(dists)
        ratio = avg_dist / box_h
        if ratio < 0.25:
            return min(1.0, 0.5 + 0.5 * (1.0 - ratio / 0.25))
        return None

    def evaluate_single_person_rules(self, person):
        triggered_alerts = []
        attrs = person["attributes"]
        gender = "male" if attrs["subjek_pria"] > 0.5 else "female"

        for rule in self.rules.get("aturan_pelanggaran", []):
            rule_id = rule.get("id")
            if not rule_id or "R-PKN" not in rule_id:
                continue

            logic = rule.get("logic", {})
            rule_gender = logic.get("gender")
            attribute = logic.get("attribute")
            category = self._get_violation_category(rule_id)

            if rule_gender == "female" and gender == "female":
                if attribute == "head_cover_absent" and attrs["hijab_kain"] < 0.5:
                    conf = float(1.0 - attrs["hijab_kain"])
                    if conf >= self.min_confidence:
                        triggered_alerts.append({
                            "rule_id": rule_id,
                            "description": rule.get("deskripsi"),
                            "confidence": conf,
                            "category": category
                        })
                elif attribute == "upper_body_non_loose" and attrs["atasan_ketat"] > 0.5:
                    conf = float(attrs["atasan_ketat"])
                    if conf >= self.min_confidence:
                        triggered_alerts.append({
                            "rule_id": rule_id,
                            "description": rule.get("deskripsi"),
                            "confidence": conf,
                            "category": category
                        })
                elif attribute == "lower_garment_short" and attrs["bawahan_pendek"] > 0.5:
                    conf = float(attrs["bawahan_pendek"])
                    if conf >= self.min_confidence:
                        triggered_alerts.append({
                            "rule_id": rule_id,
                            "description": rule.get("deskripsi"),
                            "confidence": conf,
                            "category": category
                        })
            elif rule_gender == "male" and gender == "male":
                if attribute == "lower_garment_short":
                    conf = float(attrs["bawahan_pendek"])
                    if conf < 0.5:
                        kp_conf = self._infer_shorts_from_keypoints(person)
                        if kp_conf is not None and kp_conf > conf:
                            conf = kp_conf
                    if conf >= self.min_confidence:
                        alert_category = "Celana Pendek" if self._get_violation_category(rule_id) == "Pakaian Tidak Syar'i" else category
                        triggered_alerts.append({
                            "rule_id": rule_id,
                            "description": rule.get("deskripsi"),
                            "confidence": conf,
                            "category": alert_category
                        })
        return triggered_alerts

    def evaluate_multi_person_rules(self, p1, p2):
        triggered_alerts = []
        g1 = "male" if p1["attributes"]["subjek_pria"] > 0.5 else "female"
        g2 = "male" if p2["attributes"]["subjek_pria"] > 0.5 else "female"
        gender_diff = (g1 != g2)

        h1 = p1["box"][3] - p1["box"][1]
        h2 = p2["box"][3] - p2["box"][1]
        avg_h = (h1 + h2) / 2.0
        pixels_per_meter = avg_h / 1.7

        c1 = [(p1["box"][0] + p1["box"][2]) / 2.0, (p1["box"][1] + p1["box"][3]) / 2.0]
        c2 = [(p2["box"][0] + p2["box"][2]) / 2.0, (p2["box"][1] + p2["box"][3]) / 2.0]
        center_dist_px = np.sqrt((c1[0] - c2[0])**2 + (c1[1] - c2[1])**2)
        center_dist_m = center_dist_px / pixels_per_meter
        center_dist_norm = center_dist_px / avg_h

        if center_dist_m > 2.2:
            return []

        for rule in self.rules.get("aturan_pelanggaran", []):
            rule_id = rule.get("id")
            if not rule_id or ("R-KHL" not in rule_id and "R-HYB" not in rule_id and "R-LKS" not in rule_id):
                continue

            logic = rule.get("logic", {})
            req_gender_diff = logic.get("require_gender_diff", False)
            if req_gender_diff and not gender_diff:
                continue

            gender_follower = logic.get("gender_follower")
            gender_target = logic.get("gender_target")
            if gender_follower and gender_target:
                if not ((g1 == gender_follower and g2 == gender_target) or (g2 == gender_follower and g1 == gender_target)):
                    continue

            target_attr = logic.get("attribute_target")
            if target_attr:
                target_person = p2 if g2 == gender_target else p1
                target_attrs = target_person["attributes"]
                if target_attr == "head_cover_absent" and target_attrs["hijab_kain"] >= 0.5:
                    continue

            triggered = False
            trigger_conf = 0.5

            kps_trigger = logic.get("kps_trigger")
            dist_threshold_norm = logic.get("dist_threshold_norm")
            dist_threshold_m = logic.get("dist_threshold_m")

            if kps_trigger and p1["keypoints"] is not None and p2["keypoints"] is not None:
                min_kp_dist_px = float("inf")
                for kp_idx in kps_trigger:
                    if kp_idx < len(p1["keypoints"]) and kp_idx < len(p2["keypoints"]):
                        pt1 = p1["keypoints"][kp_idx]
                        pt2 = p2["keypoints"][kp_idx]
                        if pt1[0] > 0 and pt2[0] > 0:
                            dist = np.sqrt((pt1[0] - pt2[0])**2 + (pt1[1] - pt2[1])**2)
                            if dist < min_kp_dist_px:
                                min_kp_dist_px = dist

                if min_kp_dist_px != float("inf"):
                    kp_dist_norm = min_kp_dist_px / avg_h
                    if dist_threshold_norm and kp_dist_norm <= dist_threshold_norm:
                        triggered = True
                        trigger_conf = float(1.0 - min(kp_dist_norm / dist_threshold_norm, 1.0))
                    elif dist_threshold_m and (min_kp_dist_px / pixels_per_meter) <= dist_threshold_m:
                        triggered = True
                        trigger_conf = float(1.0 - min((min_kp_dist_px / pixels_per_meter) / dist_threshold_m, 1.0))
            elif dist_threshold_m and center_dist_m <= dist_threshold_m:
                triggered = True
                trigger_conf = float(1.0 - min(center_dist_m / dist_threshold_m, 1.0))
            elif dist_threshold_norm and center_dist_norm <= dist_threshold_norm:
                triggered = True
                trigger_conf = float(1.0 - min(center_dist_norm / dist_threshold_norm, 1.0))

            if triggered:
                if "R-HYB" in rule_id:
                    female_attrs = None
                    if g1 == "female":
                        female_attrs = p1["attributes"]
                    elif g2 == "female":
                        female_attrs = p2["attributes"]
                    if female_attrs:
                        if logic.get("attribute") == "head_cover_absent":
                            if female_attrs["hijab_kain"] >= 0.5:
                                continue
                            trigger_conf = float((trigger_conf + (1.0 - female_attrs["hijab_kain"])) / 2.0)
                        elif logic.get("attribute") == "lower_garment_short":
                            if female_attrs["bawahan_pendek"] < 0.5:
                                continue
                            trigger_conf = float((trigger_conf + female_attrs["bawahan_pendek"]) / 2.0)

                trigger_conf = max(0.1, min(1.0, trigger_conf))
                if trigger_conf >= self.min_confidence:
                    category = self._get_violation_category(rule_id)
                    triggered_alerts.append({
                        "rule_id": rule_id,
                        "description": rule.get("deskripsi"),
                        "confidence": trigger_conf,
                        "category": category
                    })
        return triggered_alerts

    def process_frame(self, frame):
        results = self.yolo.predict(source=frame, conf=0.5, device="cpu", verbose=False)
        if not results:
            logger = logging.getLogger("pesat.inference")
            logger.debug("YOLO: tidak ada results")
            return [], frame

        people = []
        for result in results:
            boxes = result.boxes
            keypoints = result.keypoints

            if boxes is None or len(boxes) == 0:
                continue

            for i, box in enumerate(boxes):
                xyxy = box.xyxy[0].cpu().numpy().astype(int)
                x1, y1, x2, y2 = xyxy

                h, w, _ = frame.shape
                box_h = y2 - y1
                box_w = x2 - x1
                expand_ver = int(box_h * 0.25)
                expand_hor = int(box_w * 0.1)
                x1 = max(0, x1 - expand_hor)
                y1 = max(0, y1 - int(box_h * 0.1))
                x2 = min(w, x2 + expand_hor)
                y2 = min(h, y2 + expand_ver)

                if x2 - x1 < 10 or y2 - y1 < 10:
                    continue

                crop_img = frame[y1:y2, x1:x2]
                attributes = self.classify_attributes(crop_img)

                kps = None
                if keypoints is not None and len(keypoints) > i:
                    kps = keypoints[i].xy[0].cpu().numpy()

                people.append({
                    "box": [x1, y1, x2, y2],
                    "attributes": attributes,
                    "keypoints": kps
                })

        _log = logging.getLogger("pesat.inference")
        _log.info(f"YOLO: {len(people)} orang terdeteksi")

        alerts = []
        for p in people:
            single_alerts = self.evaluate_single_person_rules(p)
            gender_label = "Pria" if p["attributes"]["subjek_pria"] > 0.5 else "Wanita"
            for alert in single_alerts:
                label_text = f"[{gender_label}] {alert['rule_id']}: {alert['description']}"
                if len(label_text) > 95:
                    label_text = label_text[:92] + "..."
                alerts.append({
                    "rule_id": alert["rule_id"],
                    "confidence": alert["confidence"],
                    "label_text": label_text,
                    "category": alert.get("category", "Lainnya")
                })

        for i in range(len(people)):
            for j in range(i + 1, len(people)):
                multi_alerts = self.evaluate_multi_person_rules(people[i], people[j])
                g1_label = "Pria" if people[i]["attributes"]["subjek_pria"] > 0.5 else "Wanita"
                g2_label = "Pria" if people[j]["attributes"]["subjek_pria"] > 0.5 else "Wanita"
                for alert in multi_alerts:
                    label_text = f"[{g1_label} & {g2_label}] {alert['rule_id']}: {alert['description']}"
                    if len(label_text) > 95:
                        label_text = label_text[:92] + "..."
                    alerts.append({
                        "rule_id": alert["rule_id"],
                        "confidence": alert["confidence"],
                        "label_text": label_text,
                        "category": alert.get("category", "Lainnya")
                    })

        self.draw_visuals(frame, people)
        return alerts, frame

    def draw_visuals(self, frame, people):
        for p in people:
            x1, y1, x2, y2 = p["box"]
            is_male = p["attributes"]["subjek_pria"] > 0.5
            gender = "Pria" if is_male else "Wanita"
            box_color = (240, 140, 0) if is_male else (140, 0, 240)
            cv2.rectangle(frame, (x1, y1), (x2, y2), box_color, 2)

            label = gender
            if p["attributes"]["hijab_kain"] >= 0.5:
                label += " (Hijab)"

            text_size = cv2.getTextSize(label, cv2.FONT_HERSHEY_SIMPLEX, 0.45, 1)[0]
            text_w, text_h = text_size
            cv2.rectangle(frame, (x1, y1 - text_h - 10), (x1 + text_w + 10, y1), box_color, -1)
            cv2.putText(frame, label, (x1 + 5, y1 - 5), cv2.FONT_HERSHEY_SIMPLEX, 0.45, (255, 255, 255), 1, cv2.LINE_AA)

            if p["keypoints"] is not None:
                skeleton = [
                    (5, 6), (5, 7), (7, 9), (6, 8), (8, 10),
                    (5, 11), (6, 12), (11, 12), (11, 13),
                    (13, 15), (12, 14), (14, 16)
                ]
                for pt1_idx, pt2_idx in skeleton:
                    if pt1_idx < len(p["keypoints"]) and pt2_idx < len(p["keypoints"]):
                        pt1 = p["keypoints"][pt1_idx]
                        pt2 = p["keypoints"][pt2_idx]
                        if pt1[0] > 0 and pt2[0] > 0:
                            cv2.line(frame, (int(pt1[0]), int(pt1[1])), (int(pt2[0]), int(pt2[1])), (100, 220, 100), 2)

                for pt in p["keypoints"]:
                    if pt[0] > 0:
                        cv2.circle(frame, (int(pt[0]), int(pt[1])), 4, (100, 100, 220), -1)

        for i in range(len(people)):
            for j in range(i + 1, len(people)):
                p1, p2 = people[i], people[j]
                c1 = [(p1["box"][0] + p1["box"][2]) / 2.0, (p1["box"][1] + p1["box"][3]) / 2.0]
                c2 = [(p2["box"][0] + p2["box"][2]) / 2.0, (p2["box"][1] + p2["box"][3]) / 2.0]
                h1 = p1["box"][3] - p1["box"][1]
                h2 = p2["box"][3] - p2["box"][1]
                avg_h = (h1 + h2) / 2.0
                dist_px = np.sqrt((c1[0] - c2[0])**2 + (c1[1] - c2[1])**2)
                dist_m = dist_px / (avg_h / 1.7)

                if dist_m <= 2.0:
                    cv2.line(frame, (int(c1[0]), int(c1[1])), (int(c2[0]), int(c2[1])), (100, 100, 220), 1)
                    mid_x = int((c1[0] + c2[0]) / 2.0)
                    mid_y = int((c1[1] + c2[1]) / 2.0)
                    cv2.putText(frame, f"{dist_m:.1f}m", (mid_x, mid_y - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.4, (100, 100, 220), 1)
