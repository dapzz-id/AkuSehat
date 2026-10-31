<?php

namespace App\Modules\Kesehatan\Nutrition\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Kesehatan\Nutrition\Requests\FilterNutritionRequest;
use App\Modules\Kesehatan\Nutrition\Services\NutritionService;

class NutritionMonitorController extends Controller
{
    public function __construct(
        protected NutritionService $nutritionService
    ) {}

    public function index(FilterNutritionRequest $request)
    {
        $user = auth()->user();
        $instansiId = (string) $user->instansi_id;

        $data = $this->nutritionService->getDashboardData($instansiId, $request->validated());

        return view('admin.nutrition.index', $data);
    }
}
