<?php

namespace App\Modules\Hb\PemeriksaanHb\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Hb\PemeriksaanHb\Services\HbService;
use App\Modules\Hb\PemeriksaanHb\Requests\FilterHbRequest;
use App\Modules\Hb\PemeriksaanHb\Requests\StoreHbRequest;
use App\Modules\Hb\PemeriksaanHb\Requests\UpdateHbRequest;
use App\Modules\Hb\PemeriksaanHb\Requests\MassDestroyHbRequest;
use App\Modules\Hb\PemeriksaanHb\Requests\ExportHbRequest;

class HbController extends Controller
{
    public function __construct(
        protected HbService $hbService
    ) {}

    public function index(FilterHbRequest $request)
    {
        $data = $this->hbService->getDashboardData(Auth::user(), $request->validated());
        return view('admin.hb.index', $data);
    }

    public function create()
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Pemeriksaan Hemoglobin (Hb) dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canCreate()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menambah data pemeriksaan hemoglobin.');
        }

        $data = $this->hbService->getFormData(Auth::user());
        return view('admin.hb.create', $data);
    }

    public function store(StoreHbRequest $request)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Pemeriksaan Hemoglobin (Hb) dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canCreate()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menambah data pemeriksaan hemoglobin.');
        }

        $this->hbService->recordExamination($request->validated(), Auth::user());

        return redirect()->route('admin.hb.index')
            ->with('success', 'Data HB berhasil ditambahkan.');
    }

    public function edit(int|string $id)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Perubahan data pemeriksaan Hb dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canUpdate()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengubah data pemeriksaan hemoglobin.');
        }

        $data = $this->hbService->getEditFormData($id, Auth::user());
        return view('admin.hb.edit', $data);
    }

    public function update(UpdateHbRequest $request, int|string $id)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Perubahan data pemeriksaan Hb dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canUpdate()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengubah data pemeriksaan hemoglobin.');
        }

        $this->hbService->updateExaminationById($id, $request->validated(), Auth::user());

        return redirect()->route('admin.hb.index')
            ->with('success', 'Data HB berhasil diupdate.');
    }

    public function destroy(int|string $id)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Penghapusan data pemeriksaan Hb dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canDelete()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus data pemeriksaan hemoglobin.');
        }

        $this->hbService->deleteExamination($id, Auth::user());

        return redirect()->route('admin.hb.index')
            ->with('success', 'Data HB berhasil dihapus.');
    }

    public function mass_destroy(MassDestroyHbRequest $request)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Penghapusan data pemeriksaan Hb dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canDelete()) {
            return redirect()->route('admin.hb.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus data pemeriksaan hemoglobin.');
        }

        $deletedCount = $this->hbService->massDeleteExaminations($request->validated('ids'), Auth::user());

        return redirect()->route('admin.hb.index')
            ->with('success', "{$deletedCount} data HB berhasil dihapus.");
    }

    public function exportExcel(ExportHbRequest $request)
    {
        try {
            return $this->hbService->exportExcel($request->validated(), Auth::user());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(ExportHbRequest $request)
    {
        try {
            return $this->hbService->exportPdf($request->validated(), Auth::user());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
        }
    }
}
