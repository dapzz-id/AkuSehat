<?php

namespace App\Modules\Kesehatan\Counseling\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListCounselingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:menunggu,terjadwal,selesai,dibatalkan,all',
            'search' => 'nullable|string|max:100',
        ];
    }
}
