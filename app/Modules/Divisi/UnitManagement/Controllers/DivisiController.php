<?php

namespace App\Modules\Divisi\UnitManagement\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Divisi;
use App\Modules\Divisi\UnitManagement\Repositories\DivisiRepository;
use App\Modules\Divisi\UnitManagement\Services\DivisiService;
use App\Modules\Divisi\UnitManagement\Requests\FilterDivisiRequest;
use App\Modules\Divisi\UnitManagement\Requests\StoreDivisiRequest;
use App\Modules\Divisi\UnitManagement\Requests\UpdateDivisiRequest;
use App\Modules\Divisi\UnitManagement\Requests\CloneDivisiRequest;
use App\Modules\Divisi\UnitManagement\Requests\UpdateDivisiPermissionsRequest;
use App\Modules\Divisi\UnitManagement\Requests\MassDestroyDivisiRequest;

class DivisiController extends Controller
{
    public function __construct(
        protected DivisiRepository $divisiRepository,
        protected DivisiService $divisiService
    ) {}

    public function index(FilterDivisiRequest $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser->instansi_id) {
            abort(403, 'Anda belum terdaftar dalam instansi.');
        }

        $divisi = $this->divisiRepository->getPaginatedForInstansi(
            $currentUser->instansi_id,
            $request->validated('search'),
            10
        )->withQueryString();

        $kategoriOptions = Divisi::getKategoriOptions($currentUser->instansi?->jenis_instansi);

        return view('admin.divisi.index', compact('divisi', 'kategoriOptions'));
    }

    public function store(StoreDivisiRequest $request)
    {
        try {
            $this->divisiService->createDivisi($request->validated(), Auth::user());
            return redirect()->route('admin.divisi.index')->with('success', 'Divisi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('admin.divisi.index')->with('error', $e->getMessage());
        }
    }

    public function update(UpdateDivisiRequest $request, Divisi $divisi)
    {
        try {
            $this->divisiService->updateDivisi($divisi, $request->validated(), Auth::user());
            return redirect()->route('admin.divisi.index')->with('success', 'Divisi berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('admin.divisi.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function clone(CloneDivisiRequest $request, Divisi $divisi)
    {
        if (!Auth::user()->instansi_id || $divisi->instansi_id !== Auth::user()->instansi_id) {
            return redirect()->route('admin.divisi.index')->with('error', 'Akses ditolak atau divisi tidak ditemukan.');
        }

        $cloned = $this->divisiService->cloneDivisi($divisi, $request->validated(), Auth::user());

        return redirect()->route('admin.divisi.index')
            ->with('success', "Divisi '{$divisi->divisi_name}' berhasil dikloning menjadi '{$cloned->divisi_name}'.");
    }

    public function updatePermissions(UpdateDivisiPermissionsRequest $request, Divisi $divisi)
    {
        if (!Auth::user()->instansi_id || $divisi->instansi_id !== Auth::user()->instansi_id) {
            return redirect()->route('admin.divisi.index')->with('error', 'Akses ditolak.');
        }

        $useCustom = $request->input('use_custom_modules') === '1' || $request->boolean('use_custom_modules');
        $this->divisiService->updateDivisiPermissions($divisi, $request->validated(), $useCustom, Auth::user());

        return redirect()->route('admin.divisi.index')
            ->with('success', "Hak akses modul divisi '{$divisi->divisi_name}' berhasil disimpan.");
    }

    public function destroy(Divisi $divisi)
    {
        if (!Auth::user()->instansi_id || $divisi->instansi_id !== Auth::user()->instansi_id) {
            return redirect()->route('admin.divisi.index')->with('error', 'Akses ditolak.');
        }

        $name = $divisi->divisi_name;
        $affected = $this->divisiRepository->deleteWithUserCleanups($divisi);

        $msg = "Divisi '{$name}' berhasil dihapus.";
        if ($affected > 0) {
            $msg .= " Sebanyak {$affected} pengguna terkait dikeluarkan dari divisi dan di-logout.";
        }

        return redirect()->route('admin.divisi.index')->with('success', $msg);
    }

    public function mass_destroy(MassDestroyDivisiRequest $request)
    {
        $result = $this->divisiRepository->massDeleteWithUserCleanups($request->validated('ids'), Auth::user()->instansi_id);

        return redirect()->route('admin.divisi.index')
            ->with('success', "{$result['deleted']} divisi berhasil dihapus. {$result['affected_users']} pengguna terkait di-logout.");
    }
}
