<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        // Super admin and school admin can create students
        if ($user->isSuperAdmin() || $user->isSchoolAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date|before:today',
            'father_name' => 'required|string|max:255',
            'mother_name' => 'required|string|max:255',
            'mobile' => 'required|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'email' => 'nullable|email|max:255',
            'school_id' => 'required|exists:schools,id',
            'class_id' => 'nullable|exists:classes,id',
            'section' => 'nullable|string|max:50',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = auth()->user();

            // School admin can only create students for their school
            if ($user->isSchoolAdmin() && $this->input('school_id') != $user->school_id) {
                $validator->errors()->add('school_id', 'You can only add students to your own school.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'dob.before' => 'Date of birth must be in the past.',
            'mobile.regex' => 'Please enter a valid phone number.',
            'school_id.required' => 'Please select a school.',
        ];
    }
}
