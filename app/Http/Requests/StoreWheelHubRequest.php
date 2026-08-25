<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreWheelHubRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'wheel_category_id' => 'required|exists:wheel_categories,id',
            'name' => 'required|string',
            'engine_type' => 'required|string',
            'horsepower' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ];
    }
}
