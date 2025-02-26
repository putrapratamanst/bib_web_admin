<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class JournalEntryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => 'required|max:100|unique:journal_entries,number',
            'entry_date' => 'required|date',
            'reference' => 'nullable|max:100',
            'description' => 'nullable',
            'status' => 'required|in:draft,posted',
            'details' => 'required|array',
            'details.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'details.*.debit' => 'required|numeric',
            'details.*.credit' => 'required|numeric',
            'details.*.description' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'number.required' => 'Number is required',
            'number.max' => 'Number must not be greater than 100 characters',
            'number.unique' => 'Number has already been taken',
            'entry_date.required' => 'Entry date is required',
            'entry_date.date' => 'Entry date must be a date',
            'reference.max' => 'Reference must not be greater than 100 characters',
            'status.required' => 'Status is required',
            'status.in' => 'Status must be draft or posted',
            'details.required' => 'Details is required',
            'details.array' => 'Details must be an array',
            'details.*.chart_of_account_id.required' => 'Chart of account is required',
            'details.*.chart_of_account_id.exists' => 'Chart of account is invalid',
            'details.*.debit.required' => 'Debit is required',
            'details.*.debit.numeric' => 'Debit must be a number',
            'details.*.credit.required' => 'Credit is required',
            'details.*.credit.numeric' => 'Credit must be a number',
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $totalDebit = collect($this->input('details'))->sum('debit');
            $totalCredit = collect($this->input('details'))->sum('credit');

            if ($totalDebit != $totalCredit) {
                $validator->errors()->add('details', 'Total debit and credit must be equal');
            }
        });
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'errors' => $validator->errors()
        ], 400));
    }
}
