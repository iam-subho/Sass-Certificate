<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class ImportStudentsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        return $user->isSuperAdmin() || $user->isSchoolAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'csv_file.required' => 'Please select a CSV file to upload.',
            'csv_file.mimes' => 'The file must be a CSV file.',
            'csv_file.max' => 'The file size must not exceed 5MB.',
        ];
    }
}
