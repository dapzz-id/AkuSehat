<?php

namespace App\Modules\User\Member\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'tanggal_mulai' => ['required', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
