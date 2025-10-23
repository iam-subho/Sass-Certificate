<?php

namespace App\Http\Requests\Certificate;

use Illuminate\Foundation\Http\FormRequest;

class BulkPrintCertificatesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'certificate_ids' => 'required|array|min:1',
            'certificate_ids.*' => 'exists:certificates,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'certificate_ids.required' => 'Please select at least one certificate to print.',
            'certificate_ids.min' => 'Please select at least one certificate.',
            'certificate_ids.*.exists' => 'One or more selected certificates do not exist.',
        ];
    }
}
