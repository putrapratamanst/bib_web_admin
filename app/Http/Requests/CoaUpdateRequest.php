<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CoaUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $chartOfAccountId = $this->route('id');
        
        return [
            'account_category_id' => 'required|exists:account_categories,id',
            'code' => [
                'required',
                'max:10',
                Rule::unique('chart_of_accounts')->where(function ($query) {
                    return $query->where('prefix', request('prefix'));
                })->ignore($chartOfAccountId),
            ],
            'prefix' => 'required|max:10',
            'name' => [
                'required',
                'max:50',
                Rule::unique('chart_of_accounts')->ignore($chartOfAccountId),
            ],
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
            'prefix.required' => 'Prefix is required',
            'prefix.max' => 'Prefix may not be greater than 10 characters',
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