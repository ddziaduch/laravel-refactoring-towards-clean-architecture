<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user' => 'required|array',
            'user.email' => 'required|email',
            'user.password' => 'required|string',
        ];
    }
}
