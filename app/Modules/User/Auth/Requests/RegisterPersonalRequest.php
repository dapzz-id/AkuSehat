<?php

namespace App\Modules\User\Auth\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPersonalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}
