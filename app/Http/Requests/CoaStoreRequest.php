<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CoaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'account_category_id' => 'required|exists:account_categories,id',
            'code' => 'required|unique:chart_of_accounts,code|max:10',
            'name' => 'required|unique:chart_of_accounts,name|max:50',
            'balance_type' => 'required|in:DEBIT,CREDIT',
        ];
    }

    public function messages(): array
    {
        return [
            'account_category_id.required' => 'Account category is required',
            'account_category_id.exists' => 'Account category is invalid',
            'code.required' => 'Code is required',
            'code.unique' => 'Code has already been taken',
            'code.max' => 'Code may not be greater than 10 characters',
            'name.required' => 'Name is required',
            'name.unique' => 'Name has already been taken',
            'name.max' => 'Name may not be greater than 50 characters',
            'balance_type.required' => 'Balance type is required',
            'balance_type.in' => 'Balance type must be DEBIT or CREDIT',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->errors()
        ], 400));
    }
}
