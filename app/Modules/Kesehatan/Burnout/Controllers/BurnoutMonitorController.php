<?php

namespace App\Modules\Kesehatan\Burnout\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\Burnout\Requests\FilterBurnoutRequest;
use App\Modules\Kesehatan\Burnout\Services\BurnoutService;

class BurnoutMonitorController extends Controller
{
    public function __construct(
        protected BurnoutService $burnoutService
    ) {}

    public function index(FilterBurnoutRequest $request)
    {
        $user = auth()->user();
        $instansiId = (string) $user->instansi_id;

        $data = $this->burnoutService->getDashboardData($instansiId, $request->validated());

        return view('admin.burnout.index', $data);
    }
}
