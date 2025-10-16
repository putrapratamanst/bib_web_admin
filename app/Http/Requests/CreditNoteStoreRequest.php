<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreditNoteStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_id' => 'exists:contracts,id',
            'debit_note_id' => 'nullable|exists:debit_notes,id',
            'number' => 'required|unique:credit_notes,number',
            'date' => 'required|date',
            'description' => 'nullable',
            'currency_code' => 'required|exists:currencies,code',
            'exchange_rate' => 'required|numeric',
            'amount' => 'required|numeric',
            'status' => 'required|in:active,cancel',
        ];
    }
    
    public function messages()
    {
        return [
            'contract_id.required' => 'Contract is required',
            'contract_id.exists' => 'Contract is not exists',
            'debit_note_id.exists' => 'Debit Note is not exists',
            'number.required' => 'Number is required',
            'number.unique' => 'Number must be unique',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a date',
            'currency_code.required' => 'Currency is required',
            'currency_code.exists' => 'Currency is not exists',
            'exchange_rate.required' => 'Exchange Rate is required',
            'exchange_rate.numeric' => 'Exchange Rate must be a number',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be active or cancel',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->errors()
        ], 400));
    }
}
