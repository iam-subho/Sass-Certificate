<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
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
            'email' => 'required|email|unique:schools,email',
            'phone' => 'required|string|max:20',
            'template_ids' => 'required|array|min:1',
            'template_ids.*' => 'exists:certificate_templates,id',
            'package_id' => 'required|exists:packages,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'certificate_left_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'certificate_right_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'signature_left' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signature_middle' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signature_right' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signature_left_title' => 'nullable|string|max:255',
            'signature_middle_title' => 'nullable|string|max:255',
            'signature_right_title' => 'nullable|string|max:255',
            // School Admin Details
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users,email',
            'admin_password' => 'required|string|min:8',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'template_ids.required' => 'Please select at least one certificate template.',
            'template_ids.min' => 'Please select at least one certificate template.',
            'admin_email.unique' => 'This email is already registered.',
            'admin_password.min' => 'Admin password must be at least 8 characters.',
            'logo.dimensions' => 'Logo must be at least 100x100 pixels.',
            'package_id.required' => 'Please select at least one package.',
            'package_id.exists' => 'Selected package does not exist.',
        ];
    }
}
