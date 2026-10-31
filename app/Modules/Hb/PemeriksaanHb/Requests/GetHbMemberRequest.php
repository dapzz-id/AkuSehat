<?php

namespace App\Modules\Hb\PemeriksaanHb\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetHbMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }
}
