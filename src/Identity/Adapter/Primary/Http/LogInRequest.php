<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Illuminate\Foundation\Http\FormRequest;

final class LogInRequest extends FormRequest
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
