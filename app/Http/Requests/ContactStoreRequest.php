<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContactStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_group_id' => 'required|exists:contact_groups,id',
            'display_name' => 'required|max:100',
            'name' => 'nullable|min:5|max:100',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:20',
            'type' => 'required|in:client,agent,insurance',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_group_id.required' => 'Contact group is required',
            'contact_group_id.exists' => 'Contact group is invalid',
            'display_name.required' => 'Display name is required',
            'display_name.max' => 'Display name may not be greater than 100 characters',
            'name.min' => 'Name may not be less than 5 characters',
            'name.max' => 'Name may not be greater than 100 characters',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email may not be greater than 100 characters',
            'phone.max' => 'Phone may not be greater than 20 characters',
            'type.required' => 'Type is required',
            'type.in' => 'Type must be client, agent, or insurance',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->errors()
        ], 400));
    }
}
