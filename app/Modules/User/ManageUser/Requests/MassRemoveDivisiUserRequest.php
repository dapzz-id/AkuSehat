<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassRemoveDivisiUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected_users' => 'required|array|min:1',
            'selected_users.*' => 'exists:users,id',
        ];
    }
}
