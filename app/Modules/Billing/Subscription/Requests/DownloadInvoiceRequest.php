<?php

namespace App\Modules\Billing\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DownloadInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'preview' => 'nullable|boolean',
            'stream' => 'nullable|boolean',
        ];
    }
}
