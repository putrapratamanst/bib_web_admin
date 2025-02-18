<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCashBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => ['required', 'max:100', 'unique:cash_banks,number'],
            'type' => ['required', 'in:receive,pay,transfer'],
            'chart_of_account_id' => ['required'],
            'contact_id' => ['required'],
            'date' => ['required'],
            'reference' => ['max:100'],
            'memo' => ['max:160'],
            'currency_id' => ['required'],
            'exchange_rate' => ['required'],
            'amount' => ['required'],
            'details' => ['required', 'array'],
            'details.*.chart_of_account_id' => ['required'],
            'details.*.description' => ['required'],
            'details.*.amount' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'number.required' => 'Number is required!',
            'number.max' => 'Number max 100 characters!',
            'type.required' => 'Type is required!',
            'type.in' => 'Type must be receive, pay, or transfer!',
            'chart_of_account_id.required' => 'Chart of account is required!',
            'contact_id.required' => 'Contact is required!',
            'date.required' => 'Date is required!',
            'reference.max' => 'Reference max 100 characters!',
            'memo.max' => 'Memo max 160 characters!',
            'currency_id.required' => 'Currency is required!',
            'exchange_rate.required' => 'Exchange rate is required!',
            'amount.required' => 'Amount is required!',
            'details.*.chart_of_account_id.required' => 'Chart of account is required!',
            'details.*.description.required' => 'Description is required!',
            'details.*.amount.required' => 'Amount is required!',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->errors()
        ], 400));
    }
}
