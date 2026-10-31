<?php

namespace App\Modules\User\Member\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Member\Services\MemberService;
use App\Modules\User\Member\Requests\UpdateProfileRequest;
use App\Modules\User\Member\Requests\UpdatePasswordRequest;

class ProfileController extends Controller
{
    public function __construct(
        protected MemberService $memberService
    ) {}

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();
        $avatarFile = $request->hasFile('avatar') ? $request->file('avatar') : null;

        $result = $this->memberService->updateProfileInfo($user, $validated, $avatarFile);

        if (!$result['status']) {
            return back()->withErrors(['avatar' => $result['message']])->withInput();
        }

        return redirect()->route('profile.edit')->with('success', 'Informasi profil Anda berhasil diperbarui.');
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = auth()->user();
        $validated = $request->validated();

        $result = $this->memberService->updatePassword(
            $user,
            $validated['current_password'],
            $validated['password']
        );

        if (!$result['status']) {
            return back()->withErrors(['current_password' => $result['message']]);
        }

        return redirect()->route('profile.edit')->with('success', 'Password Anda berhasil diperbarui.');
    }

    public function deleteAvatar()
    {
        $user = auth()->user();
        if ($user->avatar) {
            $this->memberService->deleteStoredAvatar($user->avatar);
            $user->avatar = null;
            $user->save();
        }

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil dihapus.');
    }
}
