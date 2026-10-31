<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MassDestroyUserRequest extends FormRequest
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

    public function messages(): array
    {
        return [
            'selected_users.required' => 'Pilih minimal satu pengguna untuk dihapus.',
            'selected_users.min' => 'Pilih minimal satu pengguna untuk dihapus.',
        ];
    }
}
