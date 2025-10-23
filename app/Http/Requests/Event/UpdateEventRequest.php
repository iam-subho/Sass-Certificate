<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $event = $this->route('event');

        if ($user->isSuperAdmin()) {
            return true;
        }

        // School admin can only edit their own school's events
        if ($user->isSchoolAdmin() && $event->school_id == $user->school_id) {
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'event_date' => 'nullable|date',
            'event_type' => 'required|string|max:255',
            'certificate_template_id' => 'nullable|exists:certificate_templates,id',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Event name is required.',
            'event_type.required' => 'Please specify the event type.',
        ];
    }
}
