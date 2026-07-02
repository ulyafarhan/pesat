<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Camera;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EdgeApiController extends Controller
{
    private function verifyAuth(Request $request): ?JsonResponse
    {
        $apiKey = config('services.pesat.api_key');
        if ($apiKey) {
            $token = $request->bearerToken();
            if (! $token || $token !== $apiKey) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            }
        }

        return null;
    }

    public function cameras(Request $request): JsonResponse
    {
        $authError = $this->verifyAuth($request);
        if ($authError) {
            return $authError;
        }

        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
        ]);

        $cameras = Camera::active()
            ->byEdgeDevice($validated['device_id'])
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $cameras,
        ]);
    }

    public function heartbeat(Request $request): JsonResponse
    {
        $authError = $this->verifyAuth($request);
        if ($authError) {
            return $authError;
        }

        $validated = $request->validate([
            'device_id' => 'required|string|max:100',
            'metrics' => 'nullable|array',
        ]);

        $now = Carbon::now();
        Camera::byEdgeDevice($validated['device_id'])
            ->update([
                'last_heartbeat_at' => $now,
                'edge_metrics' => $request->has('metrics') ? ($validated['metrics'] ? json_encode($validated['metrics']) : null) : null,
            ]);

        return response()->json([
            'status' => 'success',
            'timestamp' => $now->toIso8601String(),
        ]);
    }
}
