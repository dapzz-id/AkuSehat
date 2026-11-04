<?php

namespace App\Modules\Kesehatan\Pemeriksaan\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Kesehatan\Pemeriksaan\Services\KesehatanService;
use App\Modules\Kesehatan\Pemeriksaan\Requests\FilterKesehatanRequest;
use App\Modules\Kesehatan\Pemeriksaan\Requests\StoreKesehatanRequest;
use App\Modules\Kesehatan\Pemeriksaan\Requests\UpdateKesehatanRequest;
use App\Modules\Kesehatan\Pemeriksaan\Requests\MassDestroyKesehatanRequest;
use App\Modules\Kesehatan\Pemeriksaan\Requests\ExportKesehatanRequest;

class KesehatanController extends Controller
{
    public function __construct(
        protected KesehatanService $kesehatanService
    ) {}

    public function index(FilterKesehatanRequest $request)
    {
        $data = $this->kesehatanService->getDashboardData(Auth::user(), $request->validated());
        return view('admin.kesehatan.index', $data);
    }

    public function create()
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Input dan pemeriksaan fisik dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canCreate('kesehatan')) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menambah data pemeriksaan fisik.');
        }

        $data = $this->kesehatanService->getFormData(Auth::user());
        return view('admin.kesehatan.create', $data);
    }

    public function store(StoreKesehatanRequest $request)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Input dan pemeriksaan fisik dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canCreate('kesehatan')) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menambah data pemeriksaan fisik.');
        }

        $this->kesehatanService->recordExamination($request->validated(), Auth::user());

        return redirect()->route('admin.kesehatan.index')
            ->with('success', 'Data kesehatan berhasil ditambahkan.');
    }

    public function edit(int|string $id)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Perubahan data pemeriksaan fisik dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canUpdate('kesehatan')) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengubah data pemeriksaan fisik.');
        }

        $data = $this->kesehatanService->getEditFormData($id, Auth::user());
        return view('admin.kesehatan.edit', $data);
    }

    public function update(UpdateKesehatanRequest $request, int|string $id)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Perubahan data pemeriksaan fisik dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canUpdate('kesehatan')) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk mengubah data pemeriksaan fisik.');
        }

        $this->kesehatanService->updateExaminationById($id, $request->validated(), Auth::user());

        return redirect()->route('admin.kesehatan.index')
            ->with('success', 'Data kesehatan berhasil diupdate.');
    }

    public function destroy(int|string $id)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Penghapusan data pemeriksaan fisik dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canDelete('kesehatan')) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus data pemeriksaan fisik.');
        }

        $this->kesehatanService->deleteExamination($id, Auth::user());

        return redirect()->route('admin.kesehatan.index')
            ->with('success', 'Data kesehatan berhasil dihapus.');
    }

    public function mass_destroy(MassDestroyKesehatanRequest $request)
    {
        if (Auth::user()->isAdminInstansi()) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Admin Instansi hanya memiliki hak akses pelaporan dan monitoring. Penghapusan data pemeriksaan fisik dilakukan oleh Petugas Kesehatan.');
        }

        if (!Auth::user()->canDelete('kesehatan')) {
            return redirect()->route('admin.kesehatan.index')
                ->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menghapus data pemeriksaan fisik.');
        }

        $deletedCount = $this->kesehatanService->massDeleteExaminations($request->validated('ids'), Auth::user());

        return redirect()->route('admin.kesehatan.index')
            ->with('success', "{$deletedCount} data kesehatan berhasil dihapus.");
    }

    public function exportExcel(ExportKesehatanRequest $request)
    {
        try {
            return $this->kesehatanService->exportExcel($request->validated(), Auth::user());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export Excel: ' . $e->getMessage());
        }
    }

    public function exportPdf(ExportKesehatanRequest $request)
    {
        try {
            return $this->kesehatanService->exportPdf($request->validated(), Auth::user());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat export PDF: ' . $e->getMessage());
        }
    }
}
