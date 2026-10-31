<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPermissionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'can_add_user' => 'nullable|boolean',
        ];
    }
}
