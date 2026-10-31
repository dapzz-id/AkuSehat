<?php

namespace App\Modules\SuperAdmin\LicenseKey\Controllers;

use App\Http\Controllers\Controller;
use App\Models\LicenseKey;
use App\Modules\SuperAdmin\LicenseKey\Repositories\LicenseKeyRepository;
use App\Modules\SuperAdmin\LicenseKey\Services\LicenseKeyService;
use App\Modules\SuperAdmin\LicenseKey\Requests\StoreLicenseKeyRequest;
use App\Modules\SuperAdmin\LicenseKey\Requests\UpdateLicenseKeyRequest;
use App\Modules\SuperAdmin\LicenseKey\Requests\RenewLicenseKeyRequest;

class LicenseKeyController extends Controller
{
    public function __construct(
        protected LicenseKeyRepository $licenseKeyRepository,
        protected LicenseKeyService $licenseKeyService
    ) {}

    public function index()
    {
        $licenses = $this->licenseKeyRepository->getPaginatedWithSekolah(10);
        return view('superadmin.license.index', compact('licenses'));
    }

    public function create()
    {
        $data = $this->licenseKeyService->getCreateFormData();
        return view('superadmin.license.create', $data);
    }

    public function store(StoreLicenseKeyRequest $request)
    {
        $license = $this->licenseKeyService->generateLicense($request->validated());

        return redirect()->route('superadmin.license.index')
            ->with('success', "License Key [{$license->key}] berhasil digenerate.");
    }

    public function show(LicenseKey $license)
    {
        return redirect()->route('superadmin.license.edit', $license->id);
    }

    public function edit(int|string $id)
    {
        $data = $this->licenseKeyService->getEditFormData($id);
        return view('superadmin.license.edit', $data);
    }

    public function update(UpdateLicenseKeyRequest $request, int|string $id)
    {
        $license = $this->licenseKeyService->updateLicense($id, $request->validated());

        return redirect()->route('superadmin.license.index')
            ->with('success', "License Key [{$license->key}] berhasil diperbarui.");
    }

    public function renew(RenewLicenseKeyRequest $request, int|string $id)
    {
        $result = $this->licenseKeyService->renewLicense($id, $request->validated());
        $license = $result['license'];
        $newEndDate = $result['new_end_date'];

        return redirect()->route('superadmin.license.index')
            ->with('success', "License Key [{$license->key}] berhasil diperpanjang hingga {$newEndDate->format('d/m/Y')}.");
    }

    public function destroy(int|string $id)
    {
        $this->licenseKeyService->deleteLicense($id);

        return redirect()->route('superadmin.license.index')
            ->with('success', 'License Key berhasil dihapus.');
    }
}
