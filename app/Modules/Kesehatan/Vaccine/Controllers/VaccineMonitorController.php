<?php

namespace App\Modules\Kesehatan\Vaccine\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\Vaccine\Requests\FilterVaccineRequest;
use App\Modules\Kesehatan\Vaccine\Services\VaccineService;

class VaccineMonitorController extends Controller
{
    public function __construct(
        protected VaccineService $vaccineService
    ) {}

    public function index(FilterVaccineRequest $request)
    {
        $user = auth()->user();
        $instansiId = (string) $user->instansi_id;

        $data = $this->vaccineService->getDashboardData($instansiId, $request->validated());

        return view('admin.vaccine.index', $data);
    }
}
