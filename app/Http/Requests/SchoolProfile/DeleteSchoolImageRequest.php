<?php

namespace App\Http\Requests\SchoolProfile;

use Illuminate\Foundation\Http\FormRequest;

class DeleteSchoolImageRequest extends FormRequest
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
            'field' => 'required|in:logo,certificate_left_logo,certificate_right_logo,signature_left,signature_middle,signature_right',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'field.required' => 'Please specify which image to delete.',
            'field.in' => 'Invalid image field specified.',
        ];
    }
}
