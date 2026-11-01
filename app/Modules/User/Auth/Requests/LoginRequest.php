<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Http;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'username' => 'required|string',
            'password' => 'required|string',
            'kebijakan_privasi' => 'required|accepted',
        ];

        if ($this->isTurnstileEnabled()) {
            $rules['cf-turnstile-response'] = 'required|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'kebijakan_privasi.required' => 'Anda wajib membaca dan menyetujui Kebijakan Privasi terlebih dahulu.',
            'kebijakan_privasi.accepted' => 'Anda wajib menyetujui Kebijakan Privasi untuk melanjutkan.',
            'cf-turnstile-response.required' => 'Harap selesaikan verifikasi keamanan Cloudflare Turnstile terlebih dahulu.',
        ];
    }

    public function withValidator($validator): void
    {
        if (!$this->isTurnstileEnabled()) {
            return;
        }

        $validator->after(function ($validator) {
            if ($validator->errors()->has('cf-turnstile-response')) {
                return;
            }

            $token = (string) $this->input('cf-turnstile-response', '');
            $secretKey = $this->getTurnstileSecretKey();

            try {
                $response = Http::asForm()->timeout(10)->post(
                    'https://challenges.cloudflare.com/turnstile/v0/siteverify',
                    [
                        'secret' => $secretKey,
                        'response' => $token,
                        'remoteip' => $this->ip(),
                    ]
                );

                if (!$response->successful() || !$response->json('success')) {
                    $validator->errors()->add(
                        'cf-turnstile-response',
                        'Verifikasi keamanan Cloudflare Turnstile tidak valid atau telah kedaluwarsa. Silakan ulangi kembali.'
                    );
                }
            } catch (\Throwable $e) {
                $validator->errors()->add(
                    'cf-turnstile-response',
                    'Gagal memverifikasi keamanan Cloudflare Turnstile. Silakan periksa koneksi dan coba lagi.'
                );
            }
        });
    }

    protected function isTurnstileEnabled(): bool
    {
        return !app()->environment('testing')
            && (bool) config('services.turnstile.enabled', true)
            && !empty($this->getTurnstileSiteKey())
            && !empty($this->getTurnstileSecretKey());
    }

    protected function getTurnstileSiteKey(): string
    {
        return (string) (
            config('services.turnstile.site_key')
            ?: env('TURNSTILE_SITE_KEY')
            ?: env('CLOUDFLARE_TURNSTILE_SITE_KEY')
            ?: env('CF_TURNSTILE_SITE_KEY', '')
        );
    }

    protected function getTurnstileSecretKey(): string
    {
        return (string) (
            config('services.turnstile.secret_key')
            ?: env('TURNSTILE_SECRET_KEY')
            ?: env('CLOUDFLARE_TURNSTILE_SECRET_KEY')
            ?: env('CF_TURNSTILE_SECRET_KEY', '')
        );
    }
}
