<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'max_active_posts' => 'required|integer|min:1',
            'daily_private_replies' => 'required|integer|min:1',
            'monthly_private_replies' => 'required|integer|min:1',
        ];
    }
}

