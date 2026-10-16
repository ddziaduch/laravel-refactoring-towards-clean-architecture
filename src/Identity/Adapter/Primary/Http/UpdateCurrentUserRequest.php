<?php

namespace Conduit\Identity\Adapter\Primary\Http;

use Conduit\Identity\Application\Port\Primary\UserChanges;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class UpdateCurrentUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->getAuthIdentifier();

        return [
            'user' => 'required|array|min:1',
            'user.username' => ['sometimes', 'string', 'max:50', Rule::unique('users', 'username')->ignore($userId)],
            'user.email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'user.password' => 'sometimes|string|min:8|max:64',
            'user.image' => 'sometimes|nullable|url',
            'user.bio' => 'sometimes|nullable|string|max:2048',
        ];
    }

    public function changes(): UserChanges
    {
        return new UserChanges($this->validated('user'));
    }
}
