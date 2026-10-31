<?php

namespace App\Modules\Kesehatan\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Kesehatan\Activities\Enums\ActivityType;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('finished_at') && !$this->has('ended_at')) {
            $this->merge(['ended_at' => $this->input('finished_at')]);
        }
    }

    public function rules(): array
    {
        return [
            'activity_type' => ['required', Rule::in(ActivityType::values())],
            'started_at' => 'required|date',
            'ended_at' => 'required|date|after_or_equal:started_at',
            'duration_seconds' => 'required|integer|min:1',
            'distance_meters' => 'nullable|numeric|min:0',
            'calories' => 'nullable|numeric|min:0',
            'steps' => 'nullable|integer|min:0',
            'route_coordinates' => 'nullable|array',
            'notes' => 'nullable|string|max:255',
        ];
    }
}
