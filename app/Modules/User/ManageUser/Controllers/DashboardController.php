<?php

namespace App\Modules\User\ManageUser\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\User\ManageUser\Services\DashboardService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isSuperAdmin()) {
            $data = $this->dashboardService->getSuperAdminDashboardData();
            $data['roleView'] = 'superadmin';
            return view('dashboard.theme', $data);
        }

        if (!app()->isDownForMaintenance()) {
            if ($user->isAdmin() || $user->isAdminInstansi() || $user->isHealthMonitor() || $user->isHealthConsultant()) {
                $data = $this->dashboardService->getAdminDashboardData($user);
                $data['roleView'] = 'admin';
                return view('dashboard.theme', $data);
            }

            if ($user->isMember()) {
                $data = $this->dashboardService->getMemberDashboardData($user);
                $data['roleView'] = 'member';
                return view('dashboard.theme', $data);
            }

            return redirect('/login');
        } else {
            throw new HttpException(503, 'Website sedang dalam pemeliharaan.');
        }
    }
}
