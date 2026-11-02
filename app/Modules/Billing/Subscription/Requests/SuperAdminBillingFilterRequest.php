<?php

namespace App\Modules\Billing\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuperAdminBillingFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:all,pending,success,paid,failed,expired',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}

