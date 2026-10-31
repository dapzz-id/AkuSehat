<?php

namespace App\Modules\SuperAdmin\Instansi\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Instansi;
use App\Modules\SuperAdmin\Instansi\Repositories\InstansiRepository;
use App\Modules\SuperAdmin\Instansi\Services\InstansiService;
use App\Modules\SuperAdmin\Instansi\Requests\StoreInstansiRequest;
use App\Modules\SuperAdmin\Instansi\Requests\UpdateInstansiRequest;
use App\Modules\SuperAdmin\Instansi\Requests\GetKabupatenRequest;

class InstansiController extends Controller
{
    public function __construct(
        protected InstansiRepository $instansiRepository,
        protected InstansiService $instansiService
    ) {}

    public function index()
    {
        $instansi = $this->instansiRepository->getPaginatedWithLicense(10);
        return view('superadmin.instansi.index', compact('instansi'));
    }

    public function create()
    {
        $province = $this->instansiService->fetchProvinces();
        return view('superadmin.instansi.create', compact('province'));
    }

    public function store(StoreInstansiRequest $request)
    {
        $data = $request->validated();

        try {
            $instansi = $this->instansiService->createWithStarterLicenseAndAdmin($data);

            return redirect()->route('superadmin.instansi.index')
                ->with('success', "Data {$instansi->jenis_instansi} '{$instansi->nama_instansi}' berhasil ditambahkan dengan lisensi default 500 user. Akun Admin: Username [{$instansi->npsn}], Password [password123]");
        } catch (\Throwable $e) {
            return back()->withInput()->with('error', 'Gagal menambahkan instansi: ' . $e->getMessage() . '. Seluruh data berhasil dibatalkan (Rollback).');
        }
    }

    public function edit(Instansi $instansi)
    {
        $province = $this->instansiService->fetchProvinces();
        $sekolah = $instansi;
        return view('superadmin.instansi.edit', compact('instansi', 'sekolah', 'province'));
    }

    public function update(UpdateInstansiRequest $request, Instansi $instansi)
    {
        $data = $request->validated();

        if (empty($data['jenjang'])) {
            $data['jenjang'] = $data['jenis_instansi'] === 'Sekolah' ? 'Umum' : ($data['jenis_instansi'] === 'Universitas' ? 'Perguruan Tinggi' : 'Non-Pendidikan');
        }

        $this->instansiRepository->update($instansi, $data);

        return redirect()->route('superadmin.instansi.index')
            ->with('success', "Data {$instansi->jenis_instansi} '{$instansi->nama_instansi}' berhasil diupdate.");
    }

    public function destroy(Instansi $instansi)
    {
        $nama = $instansi->nama_instansi;
        $this->instansiRepository->deleteWithUsers($instansi);

        return redirect()->route('superadmin.instansi.index')
            ->with('success', "Data instansi '{$nama}' berhasil dihapus.");
    }

    public function getProvinsi()
    {
        return $this->instansiService->fetchProvinces();
    }

    public function getKabupaten(GetKabupatenRequest $request)
    {
        $provinceId = $request->validated('province_id');
        $data = $this->instansiService->fetchRegencies($provinceId);
        return response()->json($data);
    }
}
