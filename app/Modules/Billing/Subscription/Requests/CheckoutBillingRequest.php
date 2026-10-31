<?php

namespace App\Modules\Billing\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutBillingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:extension,quota_topup',
            'kuota_tambahan' => 'nullable|integer|min:1|max:10000',
            'durasi_bulan' => 'nullable|integer|min:1|max:36',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Jenis transaksi billing wajib dipilih.',
            'type.in' => 'Jenis transaksi billing tidak valid.',
            'kuota_tambahan.min' => 'Kuota tambahan minimal 1 pengguna.',
            'durasi_bulan.min' => 'Durasi perpanjangan minimal 1 bulan.',
        ];
    }
}
