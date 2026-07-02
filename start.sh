#!/usr/bin/env bash
set -e

echo "============================================="
echo "  PESAT Edge Device - One-Click Installer"
echo "  Lhokseumawe Smart City Monitoring System"
echo "============================================="
echo ""

# Colors
RED='\033[0;31m'
YELLOW='\033[1;33m'
GREEN='\033[0;32m'
NC='\033[0m'

# Check Python
if ! command -v python3 &>/dev/null; then
    echo -e "${RED}[ERROR] Python3 tidak ditemukan! Install Python 3.13+ terlebih dahulu.${NC}"
    exit 1
fi

PY_VER=$(python3 --version 2>&1 | grep -oP '\d+\.\d+')
if (( $(echo "$PY_VER < 3.13" | bc -l) )); then
    echo -e "${YELLOW}[WARN] Python 3.13+ disarankan. Versi saat ini: $(python3 --version)${NC}"
fi

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$SCRIPT_DIR"

# Create virtual environment
if [ ! -d "venv" ]; then
    echo -e "${GREEN}[INFO]${NC} Membuat virtual environment..."
    python3 -m venv venv
fi

# Activate and install
source venv/bin/activate
pip install --upgrade pip -q
pip install -r mlcv/requirements.txt -q

# Check ONNX models
[ ! -f "mlcv/best_yolo_pose.onnx" ] && echo -e "${YELLOW}[WARN] best_yolo_pose.onnx tidak ditemukan${NC}"
[ ! -f "mlcv/best_deteksi_int8.onnx" ] && echo -e "${YELLOW}[WARN] best_deteksi_int8.onnx tidak ditemukan${NC}"

# Validate config
if [ ! -f "mlcv/edge_config.yaml" ]; then
    echo -e "${RED}[ERROR] edge_config.yaml tidak ditemukan!${NC}"
    exit 1
fi

API_BASE=$(grep 'api_base:' mlcv/edge_config.yaml | awk '{print $2}')
API_KEY=$(grep 'api_key:' mlcv/edge_config.yaml | awk '{print $2}')

if [ "$API_KEY" = "GANTI_DENGAN_API_KEY_ANDA" ]; then
    echo ""
    echo -e "${YELLOW}[PERINGATAN] API_KEY masih default!${NC}"
    echo -e "${YELLOW}            Edit mlcv/edge_config.yaml dengan API_KEY dari server.${NC}"
    echo ""
fi

echo -e "${GREEN}[INFO]${NC} Server: $API_BASE"
echo -e "${GREEN}[INFO]${NC} Device : $(hostname)"
echo -e "${GREEN}[INFO]${NC} Starting orchestrator..."
echo ""

python3 mlcv/orchestrator.py
