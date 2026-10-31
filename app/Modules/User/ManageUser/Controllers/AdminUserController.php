<?php

namespace App\Modules\User\ManageUser\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Modules\User\ManageUser\Services\UserService;
use App\Modules\User\ManageUser\Services\UserImportExportService;
use App\Modules\User\ManageUser\Requests\FilterAdminUserRequest;
use App\Modules\User\ManageUser\Requests\StoreUserRequest;
use App\Modules\User\ManageUser\Requests\UpdateUserRequest;
use App\Modules\User\ManageUser\Requests\MassDestroyUserRequest;
use App\Modules\User\ManageUser\Requests\MassUpdateDivisiUserRequest;
use App\Modules\User\ManageUser\Requests\MassRemoveDivisiUserRequest;
use App\Modules\User\ManageUser\Requests\UpdateUserPermissionsRequest;
use App\Modules\User\ManageUser\Requests\ImportUserExcelRequest;
use App\Modules\User\Enums\UserRole;
use App\Modules\User\Enums\UserGender;

class AdminUserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected UserImportExportService $userImportExportService
    ) {}

    public function index(FilterAdminUserRequest $request)
    {
        $data = $this->userService->getIndexData(Auth::user(), $request->validated());
        return view('admin.users.index', $data);
    }

    public function create()
    {
        if (!Auth::user()->canAddUser()) {
            return redirect()->route('admin.users.index')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menambah pengguna.');
        }

        $data = $this->userService->getCreateFormData(Auth::user());
        return view('admin.users.create', $data);
    }

    public function store(StoreUserRequest $request)
    {
        if (!Auth::user()->canAddUser()) {
            return redirect()->route('admin.users.index')->with('error', 'Akses ditolak: Anda tidak memiliki izin untuk menambah pengguna.');
        }

        try {
            $this->userService->createUser($request->validated(), Auth::user());
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(int|string $id)
    {
        $data = $this->userService->getEditFormData($id, Auth::user());
        return view('admin.users.edit', $data);
    }

    public function update(UpdateUserRequest $request, int|string $id)
    {
        try {
            $this->userService->updateUserById($id, $request->validated(), Auth::user());
            return redirect()->route('admin.users.index')->with('success', 'Data pengguna berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function destroy(int|string $id)
    {
        try {
            $this->userService->deleteUserById($id, Auth::user());
            return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }
    }

    public function mass_destroy(MassDestroyUserRequest $request)
    {
        $count = $this->userService->massDeleteUsers($request->validated('selected_users'), Auth::user());
        return redirect()->route('admin.users.index')->with('success', "{$count} pengguna berhasil dihapus.");
    }

    public function mass_update_divisi(MassUpdateDivisiUserRequest $request)
    {
        $validated = $request->validated();
        $this->userService->massUpdateDivisi(
            $validated['selected_users'],
            $validated['target_divisi_id'],
            Auth::user()
        );

        return redirect()->route('admin.users.index')->with('success', 'Divisi pengguna berhasil diperbarui secara massal.');
    }

    public function mass_remove_divisi(MassRemoveDivisiUserRequest $request)
    {
        $this->userService->massRemoveDivisi($request->validated('selected_users'), Auth::user());
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dikeluarkan dari divisi.');
    }

    public function updatePermissions(UpdateUserPermissionsRequest $request, User $user)
    {
        $this->userService->updateUserPermissions($user, $request->boolean('can_add_user'), Auth::user());
        return back()->with('success', 'Hak akses berhasil diperbarui.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_user_import.csv"',
        ];

        $columns = ['nomor_induk', 'nama', 'username', 'password', 'nama_divisi', 'jk', 'level'];
        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['1001', 'Budi Santoso', 'budisantoso', '12345678', 'Unit A', UserGender::LAKI_LAKI->value, UserRole::MEMBER->value]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importUsers(ImportUserExcelRequest $request)
    {
        try {
            $result = $this->userImportExportService->importFromExcel($request->file('file'), Auth::user());
            return redirect()->route('admin.users.index')->with('success', "{$result['imported']} data pengguna berhasil diimpor.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    public function exportUsers()
    {
        return $this->userImportExportService->exportToExcel(Auth::user());
    }

    public function exportExcel()
    {
        return $this->exportUsers();
    }
}
