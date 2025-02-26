<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class BillingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:AR,AP',
            'number' => 'required|unique:billings,number',
            'contact_id' => 'required|exists:contacts,id',
            'contract_id' => 'nullable|exists:contracts,id',
            'reference' => 'nullable|max:100',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'description' => 'nullable',
            'currency_code' => 'required|exists:currencies,code',
            'exchange_rate' => 'required|numeric',
            'amount' => 'required|numeric',
            'status' => 'required|in:unpaid,paid,cancelled',
        ];
    }

    public function messages()
    {
        return [
            'type.required' => 'Type is required',
            'type.in' => 'Type must be AR or AP',
            'number.required' => 'Number is required',
            'number.unique' => 'Number must be unique',
            'contact_id.required' => 'Contact is required',
            'contact_id.exists' => 'Contact is not exists',
            'contract_id.exists' => 'Contract is not exists',
            'reference.max' => 'Reference must be less than 100 characters',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a date',
            'due_date.required' => 'Due Date is required',
            'due_date.date' => 'Due Date must be a date',
            'currency_code.required' => 'Currency is required',
            'currency_code.exists' => 'Currency is not exists',
            'exchange_rate.required' => 'Exchange Rate is required',
            'exchange_rate.numeric' => 'Exchange Rate must be a number',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be unpaid, paid, or cancelled',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->errors()
        ], 400));
    }
}
