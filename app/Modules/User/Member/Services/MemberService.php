<?php

namespace App\Modules\User\Member\Services;

use App\Models\User;
use App\Modules\User\Member\Repositories\MemberRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MemberService
{
    public function __construct(
        protected MemberRepository $memberRepository
    ) {}

    public function getHealthConsultantDashboard(): array
    {
        return $this->memberRepository->getHealthConsultantDashboardStats();
    }

    public function getMemberDetailDashboard(int|string $memberId): ?array
    {
        return $this->memberRepository->getMemberDetailDashboard($memberId);
    }

    public function updateProfileInfo(User $user, array $validatedData, $avatarFile = null, ?string $avatarInput = null): array
    {
        $user->nama = $validatedData['nama'];
        if (isset($validatedData['email'])) {
            $user->email = $validatedData['email'];
        }

        if ($avatarFile) {
            $this->deleteStoredAvatar($user->avatar);
            $user->avatar = $avatarFile->store('avatars', 'public');
        } elseif ($avatarInput) {
            if (str_starts_with($avatarInput, 'data:image/')) {
                $imageParts = explode(';base64,', $avatarInput);
                if (count($imageParts) === 2) {
                    $headerPart = $imageParts[0];
                    $rawType = strtolower(str_replace('data:image/', '', $headerPart));

                    $allowedMimes = [
                        'jpeg' => 'jpg',
                        'jpg'  => 'jpg',
                        'png'  => 'png',
                        'webp' => 'webp',
                        'gif'  => 'gif',
                    ];

                    if (!array_key_exists($rawType, $allowedMimes)) {
                        return [
                            'status' => false,
                            'message' => 'Format file avatar ditolak. Hanya format JPG, PNG, WEBP, dan GIF yang diizinkan.'
                        ];
                    }

                    $imageData = base64_decode($imageParts[1], true);
                    if ($imageData === false || strlen($imageData) < 10) {
                        return ['status' => false, 'message' => 'Data gambar base64 tidak valid atau rusak.'];
                    }

                    if (strlen($imageData) > 3 * 1024 * 1024) {
                        return ['status' => false, 'message' => 'Ukuran file avatar maksimal adalah 3MB.'];
                    }

                    $ext = $allowedMimes[$rawType];
                    $safeFileName = 'avatars/' . Str::random(40) . '.' . $ext;

                    $this->deleteStoredAvatar($user->avatar);
                    Storage::disk('public')->put($safeFileName, $imageData);
                    $user->avatar = $safeFileName;
                }
            } elseif (filter_var($avatarInput, FILTER_VALIDATE_URL)) {
                $user->avatar = $avatarInput;
            }
        } elseif (array_key_exists('avatar', $validatedData) && is_null($validatedData['avatar'])) {
            $this->deleteStoredAvatar($user->avatar);
            $user->avatar = null;
        }

        $user->save();

        return [
            'status' => true,
            'message' => 'Profil berhasil diperbarui.',
            'user' => $user
        ];
    }

    public function deleteStoredAvatar(?string $avatarPath): void
    {
        if (!empty($avatarPath) && !str_starts_with($avatarPath, 'http://') && !str_starts_with($avatarPath, 'https://')) {
            if (Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
        }
    }

    public function updatePassword(User $user, string $currentPassword, string $newPassword): array
    {
        if (!Hash::check($currentPassword, $user->password)) {
            return ['status' => false, 'message' => 'Password saat ini yang Anda masukkan salah.'];
        }

        if ($currentPassword === $newPassword) {
            return ['status' => false, 'message' => 'Password baru tidak boleh sama dengan password lama.'];
        }

        $this->memberRepository->updatePassword($user, Hash::make($newPassword));

        return ['status' => true, 'message' => 'Kata sandi berhasil diperbarui dengan aman.'];
    }

    public function getSummary(User $user): array
    {
        $last = $this->memberRepository->getLatestKesehatan($user->id);
        $bmi = null;
        if ($last) {
            $bb = floatval($last->bb ?? 0);
            $tb = floatval($last->tb ?? 0) / 100.0;
            $bmi = $tb > 0 ? $bb / ($tb * $tb) : null;
        }

        return [
            'bmi' => $bmi,
            'sistol' => $last->sistol ?? null,
            'diastol' => $last->diastol ?? null,
            'last_check' => $last->tgl ?? null,
            'tinggi' => $last->tb ?? null,
            'berat' => $last->bb ?? null,
            'hb' => $this->memberRepository->getLatestHb($user->id),
        ];
    }

    public function getSummaryGrafik(User $user): array
    {
        $records = $this->memberRepository->getHealthRecordsFor5Years($user->id);

        $months = collect();
        $start = now()->subYears(5)->startOfYear();
        $end = now()->endOfYear();

        while ($start <= $end) {
            $months->push([
                'year' => (int) $start->year,
                'month' => (int) $start->month,
                'label' => $start->format('M Y'),
                'score' => 0,
            ]);
            $start->addMonth();
        }

        $filled = $months->map(function ($m) use ($records) {
            $found = $records->first(fn($r) => $r->year == $m['year'] && $r->month == $m['month']);
            if ($found) $m['score'] = round(min($found->score, 100), 2);
            return $m;
        });

        return $filled->groupBy('year')->map(function ($months) {
            $validMonths = $months->filter(fn($m) => $m['score'] > 0);
            $avg = $validMonths->avg('score') ?? 0;
            return [
                'year' => $months->first()['year'],
                'avg_score' => round($avg, 2),
                'data' => $months->values(),
            ];
        })->values()->all();
    }
}
