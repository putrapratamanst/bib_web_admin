<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreditNoteApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'notes.string' => 'Notes must be a string',
            'notes.max' => 'Notes may not be greater than 1000 characters',
        ];
    }
}