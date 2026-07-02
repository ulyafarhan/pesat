@echo off
title PESAT Test Suite Runner
chcp 65001 >nul

echo ========================================
echo   PESAT - Test Suite Automation
echo ========================================
echo.

set "PASS=0"
set "FAIL=0"
set "TOTAL=0"

call :run_test "Pint (PHP Lint)" composer lint:check
call :run_test "Pest (PHP Backend)" php artisan test --compact
call :run_test "Prettier (Frontend Format)" npm run format:check
call :run_test "ESLint (Frontend Lint)" npm run lint:check
call :run_test "vue-tsc (TypeScript Check)" npm run types:check
call :run_test "Vitest (Frontend Unit)" npm run test
call :run_test "pytest (Python Edge)" python -m pytest mlcv\tests -v

echo.
echo ========================================
echo   PESAT Test Results: %PASS% passed, %FAIL% failed
echo ========================================

if %FAIL% gtr 0 (
    exit /b 1
)
exit /b 0

:run_test
setlocal
set "name=%~1"
set "command=%~2"

echo.
echo [RUNNING] %name%
echo   $ %command%
echo.

%command%
if %errorlevel% equ 0 (
    echo [PASS] %name%
    set /a PASS+=1
) else (
    echo [FAIL] %name%
    set /a FAIL+=1
)
set /a TOTAL+=1
echo.
endlocal & set PASS=%PASS%& set FAIL=%FAIL%
goto :eof
