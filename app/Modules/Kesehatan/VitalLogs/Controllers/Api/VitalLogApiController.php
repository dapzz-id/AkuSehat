<?php

namespace App\Modules\Kesehatan\VitalLogs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\VitalLogs\Repositories\VitalLogRepository;
use App\Modules\Kesehatan\VitalLogs\Services\VitalLogService;
use App\Modules\Kesehatan\VitalLogs\Requests\GetTodayVitalLogRequest;
use App\Modules\Kesehatan\VitalLogs\Requests\AddWaterRequest;
use App\Modules\Kesehatan\VitalLogs\Requests\UpdateVitalLogRequest;
use App\Modules\Kesehatan\VitalLogs\Requests\GetVitalHistoryRequest;
use Illuminate\Http\JsonResponse;

class VitalLogApiController extends Controller
{
    public function __construct(
        protected VitalLogRepository $vitalLogRepository,
        protected VitalLogService $vitalLogService
    ) {}

    public function getToday(GetTodayVitalLogRequest $request): JsonResponse
    {
        $res = $this->vitalLogService->getToday($request->user());

        return response()->json([
            'success' => true,
            'data' => $res['data'],
            'meta' => $res['meta'],
        ]);
    }

    public function addWater(AddWaterRequest $request): JsonResponse
    {
        $amount = (int) $request->validated('amount', 250);
        $log = $this->vitalLogService->addWater($request->user(), $amount);

        return response()->json([
            'success' => true,
            'message' => "+{$amount}ml air berhasil dicatat",
            'data' => $log,
        ]);
    }

    public function updateLog(UpdateVitalLogRequest $request): JsonResponse
    {
        $log = $this->vitalLogService->updateLog($request->user(), $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data vitalitas harian berhasil diperbarui',
            'data' => $log,
        ]);
    }

    public function getHistory(GetVitalHistoryRequest $request): JsonResponse
    {
        $days = (int) $request->validated('days', 7);
        $logs = $this->vitalLogService->getHistory($request->user(), $days);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }

    public function updateToday(UpdateVitalLogRequest $request): JsonResponse
    {
        return $this->updateLog($request);
    }

    public function history(GetVitalHistoryRequest $request): JsonResponse
    {
        return $this->getHistory($request);
    }
}
