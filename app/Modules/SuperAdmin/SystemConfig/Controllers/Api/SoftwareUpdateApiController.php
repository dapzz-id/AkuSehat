<?php

namespace App\Modules\SuperAdmin\SystemConfig\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\SystemConfig\Services\SoftwareAppService;
use App\Modules\SuperAdmin\SystemConfig\Requests\CheckUpdateApiRequest;
use App\Modules\SuperAdmin\SystemConfig\Requests\HistoryUpdateApiRequest;
use Illuminate\Http\JsonResponse;

class SoftwareUpdateApiController extends Controller
{
    public function __construct(
        protected SoftwareAppService $softwareAppService
    ) {}

    public function checkUpdate(CheckUpdateApiRequest $request): JsonResponse
    {
        $platform = $request->validated('platform') ?? 'android';
        $currentVersion = $request->validated('current_version') ?? '0.0.0';

        $globalVer = \Illuminate\Support\Facades\Cache::get('global_system_version', 'g_sys_1');
        $currentEtag = '"' . md5("{$globalVer}_{$platform}_{$currentVersion}") . '"';

        $clientEtag = $request->header('If-None-Match');
        if ($clientEtag && trim($clientEtag, '" ') === trim($currentEtag, '" ')) {
            return response()->json([], 304)->withHeaders([
                'ETag' => $currentEtag,
                'Cache-Control' => 'private, no-cache, must-revalidate',
            ]);
        }

        $result = $this->softwareAppService->checkUpdate($platform, $currentVersion);

        return response()->json($result)->withHeaders([
            'ETag' => $currentEtag,
            'Cache-Control' => 'private, no-cache, must-revalidate',
        ]);
    }

    public function latestWebVersion(): JsonResponse
    {
        $result = $this->softwareAppService->getLatestWebVersion();
        return response()->json($result);
    }

    public function history(HistoryUpdateApiRequest $request): JsonResponse
    {
        $platform = $request->validated('platform');
        $updates = $this->softwareAppService->getReleaseHistory($platform);

        return response()->json([
            'success' => true,
            'data' => $updates,
        ]);
    }
}
