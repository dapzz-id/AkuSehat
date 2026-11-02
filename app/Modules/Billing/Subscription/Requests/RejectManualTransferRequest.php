<?php

namespace App\Modules\Billing\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectManualTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'reason' => 'nullable|string|max:500',
        ];
    }
}
