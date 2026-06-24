<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $status = 'healthy';
        $checks = [];

        // 1. Check Database
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Throwable $e) {
            $checks['database'] = 'down ('.$e->getMessage().')';
            $status = 'unhealthy';
        }

        // 2. Check Redis
        try {
            Redis::connection()->ping();
            $checks['redis'] = 'ok';
        } catch (\Throwable $e) {
            $checks['redis'] = 'down ('.$e->getMessage().')';
            $status = 'unhealthy';
        }

        // 3. Check Reverb Port Status
        $reverbHost = config('reverb.apps.0.host') ?: 'reverb-server';
        $reverbPort = 8080;

        $fp = @fsockopen($reverbHost, $reverbPort, $errno, $errstr, 2);
        if ($fp) {
            fclose($fp);
            $checks['reverb'] = 'ok';
        } else {
            $checks['reverb'] = "down (Failed to connect to {$reverbHost}:{$reverbPort} - {$errstr})";
            $status = 'unhealthy';
        }

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toIso8601String(),
            'services' => $checks,
        ], $status === 'healthy' ? 200 : 503);
    }
}
