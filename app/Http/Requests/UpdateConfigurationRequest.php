<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConfigurationRequest extends FormRequest
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
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'wheel_hub_id' => 'sometimes|exists:wheel_hubs,id',
            // Accept both 'components' and 'component_ids' to match API docs and client
            'components' => 'nullable|array',
            'components.*' => 'exists:wheel_components,id',
            'component_ids' => 'nullable|array',
            'component_ids.*' => 'exists:wheel_components,id',
        ];
    }
}
