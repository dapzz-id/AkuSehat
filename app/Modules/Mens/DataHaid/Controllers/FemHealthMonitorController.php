<?php

namespace App\Modules\Mens\DataHaid\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Mens\DataHaid\Services\FemHealthService;
use App\Modules\Mens\DataHaid\Requests\FilterFemHealthRequest;
use App\Modules\Mens\DataHaid\Requests\RejectPitaRequest;
use App\Modules\Mens\DataHaid\Requests\StorePitaPeminjamanRequest;
use App\Modules\Mens\DataHaid\Requests\StoreDirectPitaRequest;
use App\Modules\Mens\DataHaid\Requests\UpdateSanksiDendaRequest;
use App\Modules\Mens\DataHaid\Requests\StoreDataHaidRequest;
use App\Modules\Mens\DataHaid\Requests\StoreDirectCounselingRequest;

class FemHealthMonitorController extends Controller
{
    public function __construct(
        protected FemHealthService $femHealthService
    ) {}

    public function index(FilterFemHealthRequest $request)
    {
        $data = $this->femHealthService->getDashboardData(Auth::user(), $request->validated());
        return view('admin.femhealth.index', $data);
    }

    public function verifyPita(int|string $id)
    {
        $this->femHealthService->verifyPita($id);
        return back()->with('success', 'Peminjaman pita berhasil disetujui.');
    }

    public function rejectVerifikasi(RejectPitaRequest $request, int|string $id)
    {
        $this->femHealthService->rejectPita($id, $request->validated('alasan'));
        return back()->with('success', 'Peminjaman pita telah ditolak.');
    }

    public function storePeminjaman(StorePitaPeminjamanRequest $request)
    {
        $this->femHealthService->recordDirectPeminjaman($request->validated());
        return back()->with('success', 'Data peminjaman pita berhasil dicatat.');
    }

    public function returnPita(int|string $id)
    {
        $this->femHealthService->returnPita($id);
        return back()->with('success', 'Pita telah berhasil dikembalikan ke UKS.');
    }

    public function storeCounselingDirect(StoreDirectCounselingRequest $request)
    {
        $this->femHealthService->recordDirectCounseling($request->validated(), Auth::user());
        return back()->with('success', 'Sesi konseling telah berhasil dicatat.');
    }

    public function storePita(StoreDirectPitaRequest $request)
    {
        $this->femHealthService->recordDirectPeminjaman($request->validated());
        return back()->with('success', 'Data peminjaman pita berhasil dicatat.');
    }

    public function updateSanksiDenda(UpdateSanksiDendaRequest $request, int|string $id)
    {
        $this->femHealthService->updateSanksiDenda(
            $id,
            $request->input('status_denda', 'lunas'),
            $request->input('catatan_sanksi')
        );

        return back()->with('success', 'Status sanksi / denda pita berhasil diperbarui.');
    }

    public function storeHaid(StoreDataHaidRequest $request)
    {
        try {
            $this->femHealthService->recordHaidManual($request->validated());
            return back()->with('success', 'Siklus haid siswi berhasil dicatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function stopHaid(int|string $id)
    {
        $this->femHealthService->stopHaid($id);
        return back()->with('success', 'Status haid berhasil diakhiri.');
    }
}
