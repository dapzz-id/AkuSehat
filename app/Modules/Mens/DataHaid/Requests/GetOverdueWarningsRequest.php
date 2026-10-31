<?php

namespace App\Modules\Mens\DataHaid\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetOverdueWarningsRequest extends FormRequest
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
