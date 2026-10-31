<?php

namespace App\Modules\Kesehatan\Ergonomics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\Ergonomics\Requests\FilterErgonomicsRequest;
use App\Modules\Kesehatan\Ergonomics\Services\ErgonomicsService;

class ErgonomicsMonitorController extends Controller
{
    public function __construct(
        protected ErgonomicsService $ergonomicsService
    ) {}

    public function index(FilterErgonomicsRequest $request)
    {
        $user = auth()->user();
        $instansiId = (string) $user->instansi_id;

        $data = $this->ergonomicsService->getDashboardData($instansiId, $request->validated());

        return view('admin.ergonomics.index', $data);
    }
}
