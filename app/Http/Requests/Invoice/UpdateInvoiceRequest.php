<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();

        // Super admin can update any invoice
        if ($user->isSuperAdmin()) {
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
            'status' => 'sometimes|in:pending,paid,cancelled',
            'paid_date' => 'nullable|date',
            'payment_method' => 'nullable|string|max:255',
            'transaction_id' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'amount' => 'sometimes|numeric|min:0',
            'due_date' => 'sometimes|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'status.in' => 'Status must be pending, paid, or cancelled.',
            'paid_date.date' => 'Please enter a valid payment date.',
            'amount.min' => 'Amount must be zero or greater.',
        ];
    }
}
