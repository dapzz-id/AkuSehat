<?php

namespace App\Modules\Billing\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessGooglePayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'mode_transaksi' => 'nullable|in:perpanjang,tambah_kuota',
            'license_id' => 'nullable|string',
            'tambah_kuota' => 'nullable|integer|min:0|max:10000',
            'order_id' => 'nullable|string',
            'payment_token' => 'nullable|string',
        ];
    }
}
