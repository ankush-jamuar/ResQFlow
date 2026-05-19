<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use App\Models\EmergencyRequest;
use App\Models\Ambulance;
use App\Models\Hospital;
use Exception;

class SystemHealthController extends Controller
{
    public function index()
    {
        return view('admin.health.index');
    }

    public function stats()
    {
        $stats = [
            'queue' => $this->getQueueStats(),
            'database' => $this->getDatabaseStats(),
            'redis' => $this->getRedisStats(),
            'operational' => $this->getOperationalStats(),
            'system' => [
                'php_version' => PHP_VERSION,
                'environment' => app()->environment(),
                'debug_mode' => config('app.debug'),
            ]
        ];

        return response()->json($stats);
    }

    private function getQueueStats()
    {
        try {
            return [
                'pending' => DB::table('jobs')->count(),
                'failed' => DB::table('failed_jobs')->count(),
                'connection' => config('queue.default'),
            ];
        } catch (Exception $e) {
            return ['error' => 'Queue monitoring unavailable'];
        }
    }

    private function getDatabaseStats()
    {
        $start = microtime(true);
        try {
            DB::select('SELECT 1');
            $latency = round((microtime(true) - $start) * 1000, 2);
            return [
                'status' => 'online',
                'latency_ms' => $latency,
                'connection' => config('database.default'),
            ];
        } catch (Exception $e) {
            return ['status' => 'offline', 'error' => $e->getMessage()];
        }
    }

    private function getRedisStats()
    {
        try {
            $info = Redis::info();
            return [
                'status' => 'online',
                'memory_used' => $info['used_memory_human'] ?? 'Unknown',
                'uptime_days' => $info['uptime_in_days'] ?? 0,
            ];
        } catch (Exception $e) {
            return ['status' => 'offline', 'error' => 'Redis connection failed'];
        }
    }

    private function getOperationalStats()
    {
        return [
            'active_emergencies' => EmergencyRequest::whereIn('status', ['pending', 'accepted', 'dispatched', 'en_route', 'arrived'])->count(),
            'available_ambulances' => Ambulance::where('status', 'available')->count(),
            'online_hospitals' => Hospital::count(), // Simplified
        ];
    }

    public function failedJobs()
    {
        $failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->get();
        return response()->json($failedJobs);
    }

    public function retryJob($id)
    {
        // Simple retry logic
        \Illuminate\Support\Facades\Artisan::call('queue:retry', ['id' => [$id]]);
        return response()->json(['success' => true]);
    }

    public function deleteJob($id)
    {
        DB::table('failed_jobs')->where('id', $id)->delete();
        return response()->json(['success' => true]);
    }
}
