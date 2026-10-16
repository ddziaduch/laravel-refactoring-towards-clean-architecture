<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Illuminate\Foundation\Http\FormRequest;

final class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user' => 'required|array',
            'user.username' => 'required|string|max:50|unique:users,username',
            'user.email' => 'required|email|max:255|unique:users,email',
            'user.password' => 'required|string|min:8|max:64',
        ];
    }
}
