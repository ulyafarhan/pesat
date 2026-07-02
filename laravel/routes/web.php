<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\LoginController;
use App\Http\Middleware\RedirectIfNotWH;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('home');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/detections/snap/{snapshot}', function (string $snapshot) {
    $path = storage_path("app/detections/{$snapshot}");
    if (! file_exists($path)) {
        abort(404);
    }

    return response()->file($path, [
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0',
    ]);
})->middleware('auth');

Route::get('/detections/{camera}', function (string $camera) {
    if ($camera === 'snap') {
        abort(404);
    }
    $path = storage_path("app/detections/latest_{$camera}.jpg");
    if (! file_exists($path)) {
        abort(404);
    }

    $isLiteSpeed = isset($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], 'litespeed') !== false;

    if ($isLiteSpeed) {
        return response('', 200, [
            'X-LiteSpeed-Location' => $path,
            'Cache-Control' => 'no-cache, must-revalidate',
            'Content-Type' => 'image/jpeg',
        ]);
    }

    return response()->file($path, [
        'Cache-Control' => 'no-cache, must-revalidate',
    ]);
})->middleware('auth');

Route::middleware(['auth', RedirectIfNotWH::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/detections', [DashboardController::class, 'detections'])->name('dashboard.detections');
    Route::get('/dashboard/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');
});

Route::get('/media/{path}', function (string $path) {
    $fullPath = storage_path("app/public/{$path}");
    if (! file_exists($fullPath)) {
        abort(404);
    }
    return response()->file($fullPath);
})->where('path', '.*');
