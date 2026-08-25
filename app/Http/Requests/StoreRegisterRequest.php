<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'last_name' => ['required', 'string', 'min:2', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Il nome è obbligatorio',
            'name.min' => 'Il nome deve contenere almeno 2 caratteri',
            'last_name.required' => 'Il cognome è obbligatorio',
            'last_name.min' => 'Il cognome deve contenere almeno 2 caratteri',
            'email.required' => 'L\'email è obbligatoria',
            'email.unique' => 'L\'email è già registrata',
            'password.required' => 'La password è obbligatoria',
            'password.min' => 'La password deve essere almeno 8 caratteri',
            'password.confirmed' => 'Le password non corrispondono',
        ];
    }
}
