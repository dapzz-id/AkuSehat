<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectPitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'alasan' => 'nullable|string|max:500',
        ];
    }
}
