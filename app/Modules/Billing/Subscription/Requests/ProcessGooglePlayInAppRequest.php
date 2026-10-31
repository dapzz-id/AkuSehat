<?php

namespace App\Modules\Billing\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessGooglePlayInAppRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|string',
            'purchase_token' => 'required|string',
            'order_id' => 'required|string',
        ];
    }
}
