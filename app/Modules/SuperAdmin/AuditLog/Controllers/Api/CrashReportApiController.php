<?php

namespace App\Modules\SuperAdmin\AuditLog\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use App\Enums\AuditCategory;
use App\Modules\SuperAdmin\AuditLog\Requests\StoreCrashReportRequest;
use Illuminate\Support\Facades\Log;

class CrashReportApiController extends Controller
{
    /**
     * Menerima kiriman log crash otomatis / crash report dari aplikasi mobile.
     */
    public function report(StoreCrashReportRequest $request)
    {
        try {
            $validated = $request->validated();

            $appVer = $validated['app_version'] ?? '1.1.4';
            $buildNum = $validated['build_number'] ?? '18';
            $platform = ucfirst($validated['platform'] ?? 'Mobile');
            $errorName = $validated['error_name'] ?? 'JavaScriptException';
            $msg = $validated['error_message'];

            $description = "Crash pada {$platform} [v{$appVer} Build {$buildNum}]: {$errorName} - {$msg}";

            $log = AuditLogger::log(
                action: 'mobile_app_crash',
                category: AuditCategory::CRASH,
                description: $description,
                properties: [
                    'error_name' => $errorName,
                    'error_message' => $msg,
                    'stack_trace' => $validated['stack_trace'] ?? null,
                    'component_stack' => $validated['component_stack'] ?? null,
                    'screen_name' => $validated['screen_name'] ?? null,
                    'app_version' => $appVer,
                    'build_number' => $buildNum,
                    'platform' => $platform,
                    'platform_version' => $validated['platform_version'] ?? null,
                    'device_model' => $validated['device_model'] ?? null,
                    'device_brand' => $validated['device_brand'] ?? null,
                    'reported_user' => [
                        'id' => $validated['user_id'] ?? null,
                        'nama' => $validated['user_name'] ?? null,
                        'level' => $validated['user_role'] ?? null,
                        'instansi' => $validated['instansi_name'] ?? null,
                    ],
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Laporan crash berhasil diterima dan dicatat ke Crash Analytics Superadmin.',
                'report_id' => $log?->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('CrashReportApiController Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses laporan crash: ' . $e->getMessage(),
            ], 500);
        }
    }
}
