<?php

namespace App\Modules\Kesehatan\Counseling\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\CounselingSession;
use App\Modules\Kesehatan\Counseling\Services\CounselingService;
use App\Modules\Kesehatan\Counseling\Requests\FilterCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\StoreCounselingSessionRequest;
use App\Modules\Kesehatan\Counseling\Requests\ApproveCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\RejectCounselingRequest;
use App\Modules\Kesehatan\Counseling\Requests\UpdateCounselingStatusRequest;

class CounselingMonitorController extends Controller
{
    public function __construct(
        protected CounselingService $counselingService
    ) {}

    public function index(FilterCounselingRequest $request)
    {
        $data = $this->counselingService->getDashboardData(Auth::user(), $request->validated());
        return view('admin.counseling.index', $data);
    }

    public function store(StoreCounselingSessionRequest $request)
    {
        $this->counselingService->storeAdminSession(Auth::user(), $request->validated());
        return back()->with('success', 'Jadwal konseling berhasil dibuat.');
    }

    public function approve(ApproveCounselingRequest $request, CounselingSession $counseling)
    {
        if ($counseling->instansi_id !== Auth::user()->instansi_id) {
            abort(404);
        }

        $validated = $request->validated();
        $this->counselingService->approveSession(
            $counseling,
            Auth::user(),
            $validated['scheduled_at'] ?? null,
            $validated['notes'] ?? null
        );

        return back()->with('success', 'Sesi konseling telah disetujui.');
    }

    public function reject(RejectCounselingRequest $request, CounselingSession $counseling)
    {
        if ($counseling->instansi_id !== Auth::user()->instansi_id) {
            abort(404);
        }

        $this->counselingService->rejectSession(
            $counseling,
            Auth::user(),
            $request->validated('alasan')
        );

        return back()->with('success', 'Sesi konseling telah ditolak.');
    }

    public function updateStatus(UpdateCounselingStatusRequest $request, CounselingSession $counseling)
    {
        if ($counseling->instansi_id !== Auth::user()->instansi_id) {
            abort(404);
        }

        $this->counselingService->updateSessionStatus($counseling, $request->validated());

        return back()->with('success', 'Status konseling berhasil diperbarui.');
    }
}
