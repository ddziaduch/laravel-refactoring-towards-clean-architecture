<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user' => 'required|array|min:1',
            'user.username' => ['sometimes', 'string', 'max:50', Rule::unique('users', 'username')->ignore($this->user())],
            'user.email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->user())],
            'user.password' => 'sometimes|string|min:8|max:64',
            'user.image' => 'sometimes|nullable|url',
            'user.bio' => 'sometimes|nullable|string|max:2048',
        ];
    }
}
