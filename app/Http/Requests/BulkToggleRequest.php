<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkToggleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'post_ids'   => 'required|array|min:1',
            'post_ids.*' => 'required|string',
            'enable'     => 'required|boolean',
        ];
    }
}
