<?php

namespace App\Modules\Kesehatan\Activities\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeaderboardActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'period' => 'nullable|string|in:month,year,last_year,week,all,this_month,this_year',
            'scope' => 'nullable|string|in:local,global',
        ];
    }
}
