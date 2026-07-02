@echo off
title PESAT Edge Device - Lhokseumawe Smart City

echo =============================================
echo   PESAT Edge Device - One-Click Installer
echo   Lhokseumawe Smart City Monitoring System
echo =============================================
echo.

:: Check Python
where python >nul 2>&1
if %ERRORLEVEL% neq 0 (
    echo [ERROR] Python tidak ditemukan! Install Python 3.13+ terlebih dahulu.
    pause
    exit /b 1
)

:: Check Python version
python --version 2>&1 | findstr "3.13" >nul
if %ERRORLEVEL% neq 0 (
    python --version 2>&1 | findstr "3.14" >nul
    if %ERRORLEVEL% neq 0 (
        echo [WARN] Python 3.13/3.14 disarankan. Versi saat ini:
        python --version
    )
)

:: Check or create virtual environment
if not exist "venv\" (
    echo [INFO] Membuat virtual environment...
    python -m venv venv
    if %ERRORLEVEL% neq 0 (
        echo [ERROR] Gagal membuat virtual environment!
        pause
        exit /b 1
    )
)

:: Activate virtual environment
call venv\Scripts\activate.bat

:: Install dependencies
echo [INFO] Menginstall dependensi Python...
pip install --upgrade pip -q
pip install -r mlcv\requirements.txt -q
if %ERRORLEVEL% neq 0 (
    echo [WARN] Beberapa dependensi gagal diinstall. Melanjutkan...
)

:: Check ONNX models
if not exist "mlcv\best_yolo_pose.onnx" (
    echo [WARN] File best_yolo_pose.onnx tidak ditemukan di mlcv/
)
if not exist "mlcv\best_deteksi_int8.onnx" (
    echo [WARN] File best_deteksi_int8.onnx tidak ditemukan di mlcv/
)

:: Check edge_config.yaml
if not exist "mlcv\edge_config.yaml" (
    echo [ERROR] edge_config.yaml tidak ditemukan!
    pause
    exit /b 1
)

:: Get config values for display
for /f "tokens=2 delims=: " %%a in ('findstr "api_base:" mlcv\edge_config.yaml') do set API_BASE=%%a
for /f "tokens=2 delims=: " %%a in ('findstr "api_key:" mlcv\edge_config.yaml') do set API_KEY=%%a

:: Validate API key
if "%API_KEY%"=="GANTI_DENGAN_API_KEY_ANDA" (
    echo.
    echo [PERINGATAN] API_KEY masih default! Edit mlcv\edge_config.yaml
    echo            untuk mengatur API_KEY dari server PESAT anda.
    echo.
)

echo [INFO] Server: %API_BASE%
echo [INFO] Device : %COMPUTERNAME%
echo [INFO] Starting orchestrator...
echo.

:: Run orchestrator
python mlcv\orchestrator.py

:: Deactivate on exit
deactivate

pause
