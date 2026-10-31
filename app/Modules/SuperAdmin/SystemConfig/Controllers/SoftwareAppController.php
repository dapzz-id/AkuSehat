<?php

namespace App\Modules\SuperAdmin\SystemConfig\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\SuperAdmin\SystemConfig\Services\SoftwareAppService;
use App\Modules\SuperAdmin\SystemConfig\Requests\StoreSoftwareAppRequest;
use App\Modules\SuperAdmin\SystemConfig\Requests\UpdateSoftwareAppRequest;
use App\Modules\SuperAdmin\SystemConfig\Requests\FilterSoftwareAppRequest;

class SoftwareAppController extends Controller
{
    public function __construct(
        protected SoftwareAppService $softwareAppService
    ) {}

    public function index(FilterSoftwareAppRequest $request)
    {
        $updates = $this->softwareAppService->getAdminList($request->validated());
        return view('superadmin.software.index', compact('updates'));
    }

    public function create()
    {
        return view('superadmin.software.create');
    }

    public function store(StoreSoftwareAppRequest $request)
    {
        $validated = $request->validated();
        $validated['is_mandatory'] = $request->boolean('is_mandatory');

        $app = $this->softwareAppService->storeRelease($validated, $request->file('file'));

        return redirect()->route('superadmin.update.index')
            ->with('success', "Pembaruan {$app->name} (v{$app->version}) berhasil dirilis ke sistem.");
    }

    public function edit(string|int $id)
    {
        $data = $this->softwareAppService->getEditData($id);
        return view('superadmin.software.edit', $data);
    }

    public function update(UpdateSoftwareAppRequest $request, string|int $id)
    {
        $validated = $request->validated();
        $validated['is_mandatory'] = $request->boolean('is_mandatory');

        $app = $this->softwareAppService->updateReleaseById($id, $validated, $request->file('file'));

        return redirect()->route('superadmin.update.index')
            ->with('success', "Pembaruan {$app->name} (v{$app->version}) berhasil diperbarui.");
    }

    public function destroy(string|int $id)
    {
        $result = $this->softwareAppService->deleteReleaseById($id);

        return redirect()->route('superadmin.update.index')
            ->with('success', "Riwayat update {$result['name']} (v{$result['version']}) berhasil dihapus.");
    }

    public function downloadApp(string|int $id)
    {
        $url = $this->softwareAppService->getDownloadUrlById($id);

        if (!$url) {
            return redirect()->route('superadmin.update.index')
                ->with('info', 'Rilis ini tidak memiliki tautan rilis eksternal.');
        }

        return redirect()->away($url);
    }
}
