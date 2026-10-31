<?php

namespace App\Modules\SuperAdmin\SystemConfig\Services;

use App\Models\SoftwareApp;
use App\Models\MaintenanceMode;
use App\Modules\SuperAdmin\SystemConfig\Repositories\SoftwareAppRepository;
use App\Services\AuditLogger;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SoftwareAppService
{
    public function __construct(
        protected SoftwareAppRepository $repository
    ) {}

    public function getAdminList(array $filters = [])
    {
        return $this->repository->getPaginated($filters, 10);
    }

    public function getPatchNotesData(?string $platform = null): array
    {
        $updates = $this->repository->getActivePatchNotes($platform, 12);
        $latestWeb = $this->repository->getLatestActive('web');
        $latestAndroid = $this->repository->getLatestActive('android');

        return compact('updates', 'latestWeb', 'latestAndroid');
    }

    public function checkUpdate(string $platform, string $currentVersion): array
    {
        $latest = $this->repository->getLatestActive($platform);

        $isMaintenance = in_array($platform, ['android', 'ios']) 
            ? MaintenanceMode::isMobileInMaintenance() 
            : MaintenanceMode::isWebInMaintenance();

        if (!$latest) {
            return [
                'success' => true,
                'has_update' => false,
                'is_maintenance' => $isMaintenance,
                'message' => 'Aplikasi sudah menggunakan versi terbaru.',
                'data' => null,
            ];
        }

        $hasUpdate = version_compare($latest->version, $currentVersion, '>');
        $isMandatory = false;

        if ($hasUpdate && $latest->is_mandatory) {
            $isMandatory = true;
        } elseif ($hasUpdate && $latest->min_version) {
            $isMandatory = version_compare($currentVersion, $latest->min_version, '<');
        }

        return [
            'success' => true,
            'has_update' => $hasUpdate,
            'is_mandatory' => $isMandatory,
            'is_maintenance' => $isMaintenance,
            'data' => [
                'id' => $latest->id,
                'name' => $latest->name,
                'version' => $latest->version,
                'min_version' => $latest->min_version,
                'platform' => $latest->platform,
                'download_url' => $latest->link,
                'developer' => $latest->developer,
                'description' => $latest->description,
                'changelog' => $latest->changelog,
                'release_date' => $latest->release_date ? $latest->release_date->format('Y-m-d') : null,
            ],
        ];
    }

    public function getLatestWebVersion(): array
    {
        $latest = $this->repository->getLatestActive('web');

        if (!$latest) {
            return [
                'has_version' => false,
                'version' => '1.0.0',
                'timestamp' => 0,
            ];
        }

        return [
            'has_version' => true,
            'version' => $latest->version,
            'name' => $latest->name,
            'timestamp' => $latest->updated_at ? $latest->updated_at->timestamp : $latest->created_at->timestamp,
            'release_date' => $latest->release_date ? $latest->release_date->format('Y-m-d') : null,
        ];
    }

    public function getReleaseHistory(?string $platform): array
    {
        $updates = $this->repository->getActivePatchNotes($platform, 30);

        return $updates->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'version' => $item->version,
                'platform' => $item->platform,
                'is_mandatory' => (bool) $item->is_mandatory,
                'description' => $item->description,
                'changelog' => $item->changelog,
                'download_url' => $item->link,
                'release_date' => $item->release_date ? $item->release_date->format('Y-m-d') : null,
                'release_date_formatted' => $item->release_date ? $item->release_date->locale('id')->isoFormat('D MMMM Y') : null,
            ];
        })->toArray();
    }

    public function storeRelease(array $data, ?UploadedFile $file = null): SoftwareApp
    {
        if ($file && $file->isValid()) {
            $data['file_path'] = $file->store('software_releases', 'public');
            $data['file_size'] = $file->getSize();
        }

        $app = $this->repository->create($data);

        AuditLogger::system(
            'software_release_created',
            "SuperAdmin merilis update software: {$app->name} v{$app->version} ({$app->platform})",
            ['software_id' => $app->id, 'version' => $app->version]
        );

        return $app;
    }

    public function updateRelease(SoftwareApp $app, array $data, ?UploadedFile $file = null): bool
    {
        if ($file && $file->isValid()) {
            if ($app->file_path && Storage::disk('public')->exists($app->file_path)) {
                Storage::disk('public')->delete($app->file_path);
            }
            $data['file_path'] = $file->store('software_releases', 'public');
            $data['file_size'] = $file->getSize();
        }

        $success = $this->repository->update($app, $data);

        AuditLogger::system(
            'software_release_updated',
            "SuperAdmin memperbarui software: {$app->name} v{$app->version}",
            ['software_id' => $app->id]
        );

        return $success;
    }

    public function deleteRelease(SoftwareApp $app): bool
    {
        if ($app->file_path && Storage::disk('public')->exists($app->file_path)) {
            Storage::disk('public')->delete($app->file_path);
        }

        return $this->repository->delete($app);
    }

    public function getEditData(string|int $id): array
    {
        $update = $this->repository->findOrFail($id);
        return compact('update');
    }

    public function updateReleaseById(string|int $id, array $data, ?UploadedFile $file = null): SoftwareApp
    {
        $app = $this->repository->findOrFail($id);
        $this->updateRelease($app, $data, $file);
        return $app;
    }

    public function deleteReleaseById(string|int $id): array
    {
        $app = $this->repository->findOrFail($id);
        $name = $app->name;
        $version = $app->version;
        $this->deleteRelease($app);

        return [
            'name' => $name,
            'version' => $version,
        ];
    }

    public function getDownloadUrlById(string|int $id): ?string
    {
        $app = $this->repository->findOrFail($id);
        return !empty($app->link) ? $app->link : null;
    }
}
