<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CashBankStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:receive,pay',
            'number' => 'required|max:50|unique:cash_banks,number',
            'contact_id' => 'required|exists:contacts,id',
            'date' => 'required|date',
            //required|exists:chart_of_accounts,id add must string
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'contra_account_id' => 'required|exists:chart_of_accounts,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|max:255',
            'reference' => 'nullable|max:50',
            'status' => 'required|in:draft,approved,rejected',
        ];
    }

    // message
    public function messages(): array
    {
        return [
            'type.required' => 'Type is required',
            'type.in' => 'Type must be receive or pay',
            'number.required' => 'Number is required',
            'number.max' => 'Number may not be greater than 50 characters',
            'number.unique' => 'Number has already been taken',
            'contact_id.required' => 'Contact is required',
            'contact_id.exists' => 'Contact is not exists',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a valid date',
            'chart_of_account_id.required' => 'Chart of account is required',
            'chart_of_account_id.exists' => 'Chart of account is not exists',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'description.max' => 'Description may not be greater than 255 characters',
            'reference.max' => 'Reference may not be greater than 50 characters',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be draft, approved, or rejected',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->errors()
        ], 400));
    }
}
