<?php

namespace App\Http\Requests\Package;

use Illuminate\Foundation\Http\FormRequest;

class StorePackageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isSuperAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'monthly_certificate_limit' => 'required|integer|min:1|max:1000000',
            'duration_months' => 'required|integer|min:1|max:120',
            'price' => 'required|numeric|min:0|max:999999.99',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Package name is required.',
            'monthly_certificate_limit.required' => 'Please specify the certificate limit.',
            'monthly_certificate_limit.min' => 'Certificate limit must be at least 1.',
            'duration_months.required' => 'Please specify the duration in months.',
            'duration_months.min' => 'Duration must be at least 1 month.',
            'price.required' => 'Please specify the package price.',
        ];
    }
}
