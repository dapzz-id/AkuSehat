<?php

namespace App\Modules\Billing\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentMethodSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'is_active' => 'nullable',
            'environment' => 'nullable|string',
            'server_key' => 'nullable|string',
            'client_key' => 'nullable|string',
            'merchant_id' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'whatsapp_number' => 'nullable|string',
            'merchant_name' => 'nullable|string',
        ];
    }
}

