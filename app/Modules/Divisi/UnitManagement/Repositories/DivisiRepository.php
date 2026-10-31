<?php

namespace App\Modules\Divisi\UnitManagement\Repositories;

use App\Models\Divisi;
use App\Models\User;
use App\Modules\User\Enums\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DivisiRepository
{
    public function getForInstansi(string|int $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)
            ->orderBy('divisi_name', 'asc')
            ->get();
    }

    public function getPaginatedForInstansi(string|int $instansiId, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Divisi::withCount(['users as jumlah_member' => function($q) use ($instansiId) {
            $q->whereNotNull('instansi_id')->where('instansi_id', $instansiId);
        }])->with(['kesehatan'])->where('instansi_id', $instansiId);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('divisi_name', 'like', '%' . $search . '%')
                  ->orWhere('color_cover', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('divisi_name', 'asc')->paginate($perPage);
    }

    public function getAllWithMemberCount(string|int $instansiId): Collection
    {
        return Divisi::where('instansi_id', $instansiId)
            ->withCount(['users as jumlah_member' => function($query) {
                $query->where('level', UserRole::MEMBER->value);
            }])
            ->orderBy('divisi_name', 'asc')
            ->get();
    }

    public function findOrFail(string|int $id): Divisi
    {
        return Divisi::findOrFail($id);
    }

    public function create(array $data): Divisi
    {
        return Divisi::create($data);
    }

    public function update(Divisi $divisi, array $data): bool
    {
        return $divisi->update($data);
    }

    public function deleteWithUserCleanups(Divisi $divisi): int
    {
        return DB::transaction(function () use ($divisi) {
            $userIds = User::where('id_divisi', $divisi->id)->pluck('id')->all();
            $userCount = count($userIds);

            if ($userCount > 0) {
                User::whereIn('id', $userIds)->update(['id_divisi' => null]);
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', User::class)
                    ->whereIn('tokenable_id', $userIds)
                    ->delete();
                try {
                    DB::table('sessions')->whereIn('user_id', $userIds)->delete();
                } catch (\Throwable $e) {}
            }

            $divisi->delete();

            return $userCount;
        });
    }

    public function massDeleteWithUserCleanups(array $ids, string|int $instansiId): array
    {
        return DB::transaction(function () use ($ids, $instansiId) {
            $divisiList = Divisi::whereIn('id', $ids)
                ->where('instansi_id', $instansiId)
                ->get();

            $deletedCount = $divisiList->count();
            if ($deletedCount === 0) {
                return ['deleted' => 0, 'affected_users' => 0];
            }

            $divisiIds = $divisiList->pluck('id')->all();
            $userIds = User::whereIn('id_divisi', $divisiIds)->pluck('id')->all();
            $totalAffectedUsers = count($userIds);

            if ($totalAffectedUsers > 0) {
                User::whereIn('id', $userIds)->update(['id_divisi' => null]);
                DB::table('personal_access_tokens')
                    ->where('tokenable_type', User::class)
                    ->whereIn('tokenable_id', $userIds)
                    ->delete();
                try {
                    DB::table('sessions')->whereIn('user_id', $userIds)->delete();
                } catch (\Throwable $e) {}
            }

            Divisi::whereIn('id', $divisiIds)->delete();

            return ['deleted' => $deletedCount, 'affected_users' => $totalAffectedUsers];
        });
    }
}
