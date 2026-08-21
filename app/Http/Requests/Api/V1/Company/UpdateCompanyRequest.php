<?php

namespace App\Http\Requests\Api\V1\Company;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:companies,email,' . $this->company,
            'subdomain' => 'sometimes|string|max:100|unique:companies,subdomain,' . $this->company,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:50',
            'plan' => 'nullable|in:free,pro,enterprise',
            'status' => 'nullable|in:active,suspended,trial',
        ];
    }
}
