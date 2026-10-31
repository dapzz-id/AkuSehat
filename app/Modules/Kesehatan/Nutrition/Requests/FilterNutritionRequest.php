<?php

namespace App\Modules\Kesehatan\Nutrition\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Modules\Kesehatan\Nutrition\Enums\MealType;

class FilterNutritionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'divisi_id' => 'nullable|string',
            'meal_type' => 'nullable|in:' . implode(',', MealType::values()),
            'search' => 'nullable|string|max:100',
        ];
    }
}
