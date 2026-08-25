<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreConfigurationRequest extends FormRequest
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
            'wheel_hub_id' => 'required|exists:wheel_hubs,id',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            // Accept both 'components' and 'component_ids' to match API docs and client
            'components' => 'nullable|array',
            'components.*' => 'exists:wheel_components,id',
            'component_ids' => 'nullable|array',
            'component_ids.*' => 'exists:wheel_components,id',
        ];
    }
}
