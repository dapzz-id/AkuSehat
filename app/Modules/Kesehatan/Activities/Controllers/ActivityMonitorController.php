<?php

namespace App\Modules\Kesehatan\Activities\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Kesehatan\Activities\Services\ActivityService;
use App\Modules\Kesehatan\Activities\Requests\FilterActivityRequest;

class ActivityMonitorController extends Controller
{
    public function __construct(
        protected ActivityService $activityService
    ) {}

    public function index(FilterActivityRequest $request)
    {
        $data = $this->activityService->getDashboardData(Auth::user(), $request->validated());
        return view('admin.activities.index', $data);
    }
}
