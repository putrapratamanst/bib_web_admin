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
            'display_name' => 'required|max:300',
            'name' => 'nullable|min:1|max:300',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:20',
            'billing_name' => 'nullable|max:100',
            'billing_address' => 'nullable',
            'billing_email' => 'nullable|email|max:100',
            'billing_phone' => 'nullable|max:20',
            'type' => 'required|in:client,agent,insurance',
        ];
    }

    public function messages(): array
    {
        return [
            'contact_group_id.required' => 'Contact group is required',
            'contact_group_id.exists' => 'Contact group is invalid',
            'display_name.required' => 'Display name is required',
            'display_name.max' => 'Display name may not be greater than 300 characters',
            'name.min' => 'Name may not be less than 1 character',
            'name.max' => 'Name may not be greater than 300 characters',
            'email.email' => 'Email must be a valid email address',
            'email.max' => 'Email may not be greater than 100 characters',
            'phone.max' => 'Phone may not be greater than 20 characters',
            'billing_name.max' => 'Billing name may not be greater than 100 characters',
            'billing_email.email' => 'Billing email must be a valid email address',
            'billing_email.max' => 'Billing email may not be greater than 100 characters',
            'billing_phone.max' => 'Billing phone may not be greater than 20 characters',
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
