<?php

namespace App\Modules\User\ManageUser\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Modules\User\ManageUser\Services\SuperAdminUserService;
use App\Modules\User\ManageUser\Requests\FilterUserRequest;
use App\Modules\User\ManageUser\Requests\StoreSuperAdminUserRequest;
use App\Modules\User\ManageUser\Requests\UpdateSuperAdminUserRequest;
use App\Modules\User\ManageUser\Requests\BanUserRequest;
use App\Modules\User\ManageUser\Requests\SuspendUserRequest;
use Illuminate\Support\Facades\Auth;

class SuperAdminUserController extends Controller
{
    public function __construct(
        protected SuperAdminUserService $superAdminUserService
    ) {}

    public function index(FilterUserRequest $request)
    {
        $data = $this->superAdminUserService->getIndexData($request->validated());
        return view('superadmin.users.index', $data);
    }

    public function create()
    {
        $data = $this->superAdminUserService->getCreateFormData();
        return view('superadmin.users.create', $data);
    }

    public function store(StoreSuperAdminUserRequest $request)
    {
        $this->superAdminUserService->createUser($request->validated());
        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function show(int|string $id)
    {
        return redirect()->route('superadmin.users.edit', $id);
    }

    public function edit(int|string $id)
    {
        $data = $this->superAdminUserService->getEditFormData($id);
        return view('superadmin.users.edit', $data);
    }

    public function update(UpdateSuperAdminUserRequest $request, int|string $id)
    {
        $this->superAdminUserService->updateUserById($id, $request->validated());
        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(int|string $id)
    {
        $this->superAdminUserService->deleteUserById($id);
        return redirect()->route('superadmin.users.index')->with('success', 'User berhasil dihapus.');
    }

    public function ban(BanUserRequest $request, User $user)
    {
        try {
            $this->superAdminUserService->banUser($user, $request->validated('reason'), Auth::user());
            return redirect()->back()->with('success', "Akun '{$user->nama}' (@{$user->username}) berhasil di-banned secara permanen.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function suspend(SuspendUserRequest $request, User $user)
    {
        $validated = $request->validated();

        try {
            $this->superAdminUserService->suspendUser(
                $user, 
                $validated['duration'], 
                $validated['custom_date'] ?? null, 
                $validated['reason'] ?? null, 
                Auth::user()
            );
            return redirect()->back()->with('success', "Akun '{$user->nama}' (@{$user->username}) berhasil disuspend.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function activate(User $user)
    {
        try {
            $this->superAdminUserService->activateUser($user, Auth::user());
            return redirect()->back()->with('success', "Akun '{$user->nama}' (@{$user->username}) berhasil diaktifkan kembali.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
