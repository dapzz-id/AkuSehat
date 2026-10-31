<?php

namespace App\Modules\SuperAdmin\SystemConfig\Repositories;

use App\Models\SoftwareApp;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwarePlatform;
use App\Modules\SuperAdmin\SystemConfig\Enums\SoftwareStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class SoftwareAppRepository
{
    public function getQuery(array $filters = []): Builder
    {
        $query = SoftwareApp::query()->orderBy('release_date', 'desc')->orderBy('created_at', 'desc');

        if (!empty($filters['platform'])) {
            $query->where('platform', $filters['platform']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('version', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    public function getPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->getQuery($filters)->paginate($perPage)->withQueryString();
    }

    public function getActivePatchNotes(?string $platform = null, int $perPage = 12): LengthAwarePaginator
    {
        $query = SoftwareApp::where('status', SoftwareStatus::ACTIVE->value)
            ->orderBy('release_date', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($platform) && in_array($platform, SoftwarePlatform::values())) {
            $query->where('platform', $platform);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getLatestActive(string $platform): ?SoftwareApp
    {
        return \Illuminate\Support\Facades\Cache::remember("latest_software_app_{$platform}", 3600, function () use ($platform) {
            return SoftwareApp::where('platform', $platform)
                ->where('status', SoftwareStatus::ACTIVE->value)
                ->orderBy('release_date', 'desc')
                ->first();
        });
    }

    public function findOrFail(string|int $id): SoftwareApp
    {
        return SoftwareApp::findOrFail($id);
    }

    public function create(array $data): SoftwareApp
    {
        return SoftwareApp::create($data);
    }

    public function update(SoftwareApp $app, array $data): bool
    {
        return $app->update($data);
    }

    public function delete(SoftwareApp $app): bool
    {
        return $app->delete();
    }
}
