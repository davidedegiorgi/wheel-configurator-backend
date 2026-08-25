<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'Token non valido',
            'email.required' => 'Inserisci la tua email',
            'email.email' => 'Email non valida',
            'password.required' => 'Inserisci la nuova password',
            'password.min' => 'La password deve essere almeno 8 caratteri',
            'password.confirmed' => 'Le password non corrispondono',
        ];
    }
}
