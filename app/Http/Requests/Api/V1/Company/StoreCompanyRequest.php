<?php

namespace App\Http\Requests\Api\V1\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:companies,email',
            'subdomain' => 'nullable|string|max:100|unique:companies,subdomain',
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'website' => 'nullable|string|max:255',
            'industry' => 'nullable|string|max:50',
            'plan' => 'nullable|in:free,pro,enterprise',
            'status' => 'nullable|in:active,suspended,trial',
        ];
    }
}
