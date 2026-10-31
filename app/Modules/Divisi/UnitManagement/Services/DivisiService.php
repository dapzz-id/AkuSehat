<?php

namespace App\Modules\Divisi\UnitManagement\Services;

use App\Models\Divisi;
use App\Models\User;
use App\Modules\Divisi\UnitManagement\Repositories\DivisiRepository;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\DB;
use Exception;

class DivisiService
{
    public function __construct(
        protected DivisiRepository $divisiRepository
    ) {}

    public function createDivisi(array $data, User $actor): Divisi
    {
        if (!$actor->instansi_id) {
            throw new Exception('Anda tidak memiliki instansi terkait.');
        }

        $kategori = in_array($data['kategori'] ?? '', ['member', 'staf', 'pimpinan']) 
            ? $data['kategori'] 
            : 'member';

        $divisi = $this->divisiRepository->create([
            'instansi_id' => $actor->instansi_id,
            'tgl' => now()->format('Y-m-d'),
            'divisi_name' => $data['divisi'],
            'kategori' => $kategori,
            'color_cover' => $data['cover_color'],
        ]);

        AuditLogger::log(
            'create_divisi',
            'instansi',
            "Admin membuat divisi baru: {$divisi->divisi_name} ({$divisi->kategori})",
            ['divisi_id' => $divisi->id]
        );

        return $divisi;
    }

    public function updateDivisi(Divisi $divisi, array $data, User $actor): bool
    {
        $kategori = in_array($data['kategori'] ?? '', ['member', 'staf', 'pimpinan']) 
            ? $data['kategori'] 
            : $divisi->kategori;

        $updated = $this->divisiRepository->update($divisi, [
            'divisi_name' => $data['divisi'],
            'kategori' => $kategori,
            'color_cover' => $data['cover_color'],
        ]);

        AuditLogger::log(
            'update_divisi',
            'instansi',
            "Admin memperbarui divisi: {$divisi->divisi_name}",
            ['divisi_id' => $divisi->id]
        );

        return $updated;
    }

    public function deleteDivisiWithMembers(Divisi $divisi, ?int $moveToDivisiId, User $actor): void
    {
        DB::transaction(function () use ($divisi, $moveToDivisiId) {
            if ($moveToDivisiId) {
                User::where('id_divisi', $divisi->id)->update(['id_divisi' => $moveToDivisiId]);
            } else {
                User::where('id_divisi', $divisi->id)->update(['id_divisi' => null]);
            }
            $this->divisiRepository->delete($divisi);
        });

        AuditLogger::log(
            'delete_divisi',
            'instansi',
            "Admin menghapus divisi: {$divisi->divisi_name}",
            ['divisi_id' => $divisi->id, 'migrated_to' => $moveToDivisiId]
        );
    }

    public function cloneDivisi(Divisi $sourceDivisi, array $validated, User $actor): Divisi
    {
        $cloned = $this->divisiRepository->create([
            'instansi_id' => $actor->instansi_id,
            'tgl' => now()->format('Y-m-d'),
            'divisi_name' => $validated['new_divisi_name'],
            'kategori' => $sourceDivisi->kategori ?? 'member',
            'color_cover' => $validated['new_cover_color'] ?: $sourceDivisi->color_cover,
            'active_modules' => $sourceDivisi->active_modules,
            'module_permissions' => $sourceDivisi->module_permissions,
            'description' => $sourceDivisi->description ? ($sourceDivisi->description . ' (Kloning)') : ('Kloning dari ' . $sourceDivisi->divisi_name),
        ]);

        AuditLogger::log(
            'clone_divisi',
            'instansi',
            "Admin mengkloning divisi: {$sourceDivisi->divisi_name} menjadi {$cloned->divisi_name}",
            ['source_divisi_id' => $sourceDivisi->id, 'new_divisi_id' => $cloned->id]
        );

        return $cloned;
    }

    public function updateDivisiPermissions(Divisi $divisi, array $validated, bool $useCustom, User $actor): bool
    {
        $payload = [
            'description' => $validated['description'] ?? null,
            'active_modules' => $useCustom ? ($validated['active_modules'] ?? []) : null,
            'module_permissions' => $validated['permissions'] ?? [],
        ];

        $updated = $this->divisiRepository->update($divisi, $payload);

        AuditLogger::log(
            'update_divisi_permissions',
            'instansi',
            "Admin memperbarui izin modul divisi: {$divisi->divisi_name}",
            ['divisi_id' => $divisi->id]
        );

        return $updated;
    }
}
