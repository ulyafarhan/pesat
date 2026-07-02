@echo off
echo ==============================================
echo PESAT Fullstack Development Environment
echo ==============================================
echo Membuka layanan di jendela terpisah...

cd laravel

echo 1. Memulai Laravel Server...
start "PESAT Laravel Server" cmd /c "php artisan serve"

echo 2. Memulai Vite Server...
start "PESAT Vite Server" cmd /c "npm run dev"

echo 3. Memulai Reverb Server...
start "PESAT Reverb Server" cmd /c "php artisan reverb:start"

echo 4. Memulai Queue Worker...
start "PESAT Queue Worker" cmd /c "php artisan queue:listen"

cd ..

echo 5. Memulai Edge Orchestrator (AI)...
start "PESAT Edge Orchestrator" cmd /k "call start.bat"

echo.
echo Selesai! 5 jendela terminal telah dibuka.
echo Pastikan tidak ada pesan error di masing-masing jendela.
echo ==============================================
