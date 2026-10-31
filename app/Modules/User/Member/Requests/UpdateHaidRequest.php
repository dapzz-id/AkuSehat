<?php

namespace App\Modules\User\Member\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHaidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'tanggal_selesai' => ['nullable', 'date'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ];
    }
}
