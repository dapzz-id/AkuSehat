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
            'search' => 'nullable|string|max:100',
            'q' => 'nullable|string|max:100',
            'status' => 'nullable|string|in:all,pending,success,paid,failed,expired',
            'payment_method' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];
    }
}

