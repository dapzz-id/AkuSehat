<?php

namespace App\Modules\Kesehatan\VitalLogs\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Kesehatan\VitalLogs\Services\VitalLogService;
use App\Modules\Kesehatan\VitalLogs\Requests\FilterVitalLogsRequest;

class VitalMonitorController extends Controller
{
    public function __construct(
        protected VitalLogService $vitalLogService
    ) {}

    public function index(FilterVitalLogsRequest $request)
    {
        $dateFilter = $request->query('date');
        $data = $this->vitalLogService->getDashboardData(Auth::user(), $request->validated(), $dateFilter);
        return view('admin.vital-logs.index', $data);
    }
}
