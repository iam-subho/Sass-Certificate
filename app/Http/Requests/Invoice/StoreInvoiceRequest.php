<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
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
            'school_id' => 'required|exists:schools,id',
            'month' => 'required|date_format:Y-m',
            'certificates_count' => 'required|integer|min:0',
            'amount' => 'required|numeric|min:0',
            'plan_type' => 'required|string|max:255',
            'status' => 'required|in:pending,paid,cancelled',
            'due_date' => 'required|date|after_or_equal:today',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'school_id.required' => 'Please select a school.',
            'month.required' => 'Invoice month is required.',
            'month.date_format' => 'Month must be in YYYY-MM format.',
            'certificates_count.required' => 'Certificate count is required.',
            'amount.required' => 'Invoice amount is required.',
            'due_date.after_or_equal' => 'Due date must be today or a future date.',
        ];
    }
}
