<?php

namespace App\Http\Requests\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends FormRequest
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
        $schoolId = $this->route('school')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:schools,email,' . $schoolId,
            'phone' => 'required|string|max:20',
            'template_ids' => 'required|array|min:1',
            'template_ids.*' => 'exists:certificate_templates,id',
            'package_id' => 'nullable|exists:packages,id',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'certificate_left_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'certificate_right_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'signature_left' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signature_middle' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signature_right' => 'nullable|image|mimes:png,jpg,jpeg|max:1024',
            'signature_left_title' => 'nullable|string|max:255',
            'signature_middle_title' => 'nullable|string|max:255',
            'signature_right_title' => 'nullable|string|max:255',
            'is_active' => 'boolean',
            'status' => 'nullable|in:pending,approved,rejected,suspended',
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
            'logo.dimensions' => 'Logo must be at least 100x100 pixels.',
        ];
    }
}
