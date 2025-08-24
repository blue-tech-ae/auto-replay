<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ];
    }
}

