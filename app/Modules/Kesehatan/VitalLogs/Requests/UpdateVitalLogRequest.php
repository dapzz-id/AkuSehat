<?php

namespace App\Modules\Kesehatan\VitalLogs\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\Kesehatan\VitalLogs\Enums\MoodScore;

class UpdateVitalLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'water_intake_ml' => 'nullable|integer|min:0|max:10000',
            'sleep_hours' => 'nullable|numeric|min:0|max:24',
            'mood_score' => ['nullable', 'integer', Rule::in(MoodScore::values())],
            'systolic' => 'nullable|integer|min:50|max:300',
            'diastolic' => 'nullable|integer|min:30|max:200',
            'blood_sugar' => 'nullable|numeric|min:20|max:1000',
            'weight_kg' => 'nullable|numeric|min:10|max:500',
            'notes' => 'nullable|string|max:255',
        ];
    }
}
