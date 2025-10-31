<?php

namespace App\Http\Requests\LiderSemillero;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class, 'email')->ignore($this->user()->id),
            ],
            'telefono' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-()\s]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'El correo personal ya está en uso.',
            'telefono.regex' => 'El formato del teléfono no es válido.',
        ];
    }
}
