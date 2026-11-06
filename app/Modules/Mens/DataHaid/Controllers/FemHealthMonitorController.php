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
        if (!Auth::user()->canUpdate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk memverifikasi peminjaman pita.');
        }

        try {
            $this->femHealthService->verifyPita($id, Auth::user()->instansi_id);
            return back()->with('success', 'Peminjaman pita berhasil disetujui.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function rejectVerifikasi(RejectPitaRequest $request, int|string $id)
    {
        if (!Auth::user()->canUpdate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk menolak peminjaman pita.');
        }

        try {
            $this->femHealthService->rejectPita($id, $request->validated('alasan'), Auth::user()->instansi_id);
            return back()->with('success', 'Peminjaman pita telah ditolak.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storePeminjaman(StorePitaPeminjamanRequest $request)
    {
        if (!Auth::user()->canCreate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mencatat peminjaman pita.');
        }

        try {
            $this->femHealthService->recordDirectPeminjaman($request->validated(), Auth::user());
            return back()->with('success', 'Data peminjaman pita berhasil dicatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function returnPita(int|string $id)
    {
        if (!Auth::user()->canUpdate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk memproses pengembalian pita.');
        }

        try {
            $this->femHealthService->returnPita($id, Auth::user()->instansi_id);
            return back()->with('success', 'Pita telah berhasil dikembalikan ke UKS.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeCounselingDirect(StoreDirectCounselingRequest $request)
    {
        if (!Auth::user()->canCreate('konsultasi', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mencatat sesi konseling.');
        }

        try {
            $this->femHealthService->recordDirectCounseling($request->validated(), Auth::user());
            return back()->with('success', 'Sesi konseling telah berhasil dicatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storePita(StoreDirectPitaRequest $request)
    {
        if (!Auth::user()->canCreate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mencatat peminjaman pita anggota.');
        }

        try {
            $this->femHealthService->recordDirectPeminjaman($request->validated(), Auth::user());
            return back()->with('success', 'Data peminjaman pita berhasil dicatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateSanksiDenda(UpdateSanksiDendaRequest $request, int|string $id)
    {
        if (!Auth::user()->canUpdate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk memperbarui status sanksi / denda pita.');
        }

        try {
            $this->femHealthService->updateSanksiDenda(
                $id,
                $request->input('status_denda', 'lunas'),
                $request->input('catatan_sanksi'),
                Auth::user()->instansi_id
            );

            return back()->with('success', 'Status sanksi / denda pita berhasil diperbarui.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeHaid(StoreDataHaidRequest $request)
    {
        if (!Auth::user()->canCreate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mencatat siklus haid anggota.');
        }

        try {
            $this->femHealthService->recordHaidManual($request->validated(), Auth::user());
            return back()->with('success', 'Siklus haid siswi berhasil dicatat.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function stopHaid(int|string $id)
    {
        if (!Auth::user()->canUpdate('haid_pita', 'duty')) {
            abort(403, 'Akses ditolak: Anda tidak memiliki wewenang untuk mengakhiri siklus haid anggota.');
        }

        try {
            $this->femHealthService->stopHaid($id, Auth::user()->instansi_id);
            return back()->with('success', 'Status haid berhasil diakhiri.');
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
