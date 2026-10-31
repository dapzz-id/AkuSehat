<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSanksiDendaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'status_denda' => 'nullable|string|in:lunas,belum_lunas,dimaafkan',
            'catatan_sanksi' => 'nullable|string|max:500',
        ];
    }
}
