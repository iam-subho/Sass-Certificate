<?php

namespace App\Http\Requests\Issuer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIssuerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $issuer = $this->route('issuer');

        // School admin can only edit issuers from their school
        if ($user->isSchoolAdmin() &&
            $issuer->school_id == $user->school_id &&
            $issuer->role == 'issuer') {
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
        $issuerId = $this->route('issuer')->id;

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $issuerId,
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Issuer name is required.',
            'email.unique' => 'This email is already registered.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
        ];
    }
}
