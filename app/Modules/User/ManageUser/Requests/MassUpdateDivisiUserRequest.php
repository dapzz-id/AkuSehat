<?php

namespace App\Modules\User\ManageUser\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class MassUpdateDivisiUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $instansiId = auth()->user()?->instansi_id;

        return [
            'selected_users' => 'required|array|min:1',
            'selected_users.*' => [
                'required',
                $instansiId
                    ? Rule::exists('users', 'id')->where('instansi_id', $instansiId)
                    : 'exists:users,id',
            ],
            'target_divisi_id' => [
                'required',
                $instansiId
                    ? Rule::exists('divisi', 'id')->where('instansi_id', $instansiId)
                    : 'exists:divisi,id',
            ],
        ];
    }
}
