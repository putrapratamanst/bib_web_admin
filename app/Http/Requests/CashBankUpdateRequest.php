<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CashBankUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $cashBankId = $this->route('id');
        
        return [
            'type' => 'required|in:receive,pay',
            'transaction_type' => 'required|in:bank_transaction,bank_to_account',
            'number' => [
                'required',
                'max:50',
                Rule::unique('cash_banks')->ignore($cashBankId),
            ],
            'contact_id' => 'required|exists:contacts,id',
            'date' => 'required|date',
            'chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'contra_account_id' => 'nullable|required_if:transaction_type,bank_to_account|exists:chart_of_accounts,id',
            'amount' => 'required|numeric',
            'description' => 'nullable|max:255',
            'reference' => 'nullable|max:50',
            'status' => 'required|in:draft,approved,rejected',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Type is required',
            'type.in' => 'Type must be receive or pay',
            'transaction_type.required' => 'Transaction Type is required',
            'transaction_type.in' => 'Transaction Type must be bank transaction or bank to account',
            'number.required' => 'Number is required',
            'number.max' => 'Number may not be greater than 50 characters',
            'number.unique' => 'Number has already been taken',
            'contact_id.required' => 'Contact is required',
            'contact_id.exists' => 'Contact is not exists',
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a valid date',
            'chart_of_account_id.required' => 'Chart of account is required',
            'chart_of_account_id.exists' => 'Chart of account is not exists',
            'contra_account_id.required_if' => 'Contra account is required when transaction type is bank to account',
            'contra_account_id.exists' => 'Contra account is not exists',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'description.max' => 'Description may not be greater than 255 characters',
            'reference.max' => 'Reference may not be greater than 50 characters',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be draft, approved or rejected',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Validation errors',
            'errors'    => $validator->errors()
        ], 422));
    }
}