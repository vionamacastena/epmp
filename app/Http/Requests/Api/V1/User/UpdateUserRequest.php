<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $this->user,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'nullable|string|in:user,admin,team_lead,developer,qa',
            'company_id' => 'nullable|exists:companies,id',
            'is_active' => 'nullable|boolean',
        ];
    }
}
