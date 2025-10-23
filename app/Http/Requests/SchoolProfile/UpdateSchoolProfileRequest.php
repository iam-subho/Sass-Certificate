<?php

namespace App\Http\Requests\SchoolProfile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isSchoolAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'certificate_left_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'certificate_right_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048|dimensions:min_width=100,min_height=100',
            'signature_left' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'signature_middle' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'signature_right' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'signature_left_title' => 'nullable|string|max:255',
            'signature_middle_title' => 'nullable|string|max:255',
            'signature_right_title' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'logo.image' => 'Logo must be an image file.',
            'logo.max' => 'Logo size must not exceed 2MB.',
            'logo.dimensions' => 'Logo must be at least 100x100 pixels.',
            'signature_left.image' => 'Signature must be an image file.',
            'signature_middle.image' => 'Signature must be an image file.',
            'signature_right.image' => 'Signature must be an image file.',
        ];
    }
}
