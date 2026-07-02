<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AdminSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    /**
     * Ambil Semua Pengaturan Admin
     *
     * Mengembalikan semua pengaturan admin dalam format key-value.
     *
     * @group Pengaturan Admin
     *
     * @unauthenticated
     *
     * @response 200 scenario="Sukses" {"status":"success","data":{"break_mode_active":"false","break_start_time":"12:00","break_end_time":"14:00"}}
     */
    public function getSettings(): JsonResponse
    {
        $settings = AdminSetting::all()->pluck('value', 'key');

        return response()->json([
            'status' => 'success',
            'data' => $settings,
        ]);
    }

    /**
     * Update Pengaturan Admin
     *
     * Memperbarui pengaturan break mode dan jam istirahat.
     *
     * @group Pengaturan Admin
     *
     * @unauthenticated
     *
     * @bodyParam break_mode_active string required Aktifkan break mode manual (true/false). Example: false
     * @bodyParam break_start_time string required Jam mulai istirahat. Example: 12:00
     * @bodyParam break_end_time string required Jam selesai istirahat. Example: 14:00
     *
     * @response 200 scenario="Sukses" {"status":"success","data":{"break_mode_active":"false","break_start_time":"12:00","break_end_time":"14:00"}}
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'break_mode_active' => 'required|in:true,false',
            'break_start_time' => 'required|string',
            'break_end_time' => 'required|string',
        ]);

        foreach ($request->only(['break_mode_active', 'break_start_time', 'break_end_time']) as $key => $value) {
            AdminSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return response()->json([
            'status' => 'success',
            'data' => AdminSetting::all()->pluck('value', 'key'),
        ]);
    }
}
