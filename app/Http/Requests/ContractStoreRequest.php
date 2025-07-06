<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ContractStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contract_status' => 'required|in:renewal,new',
            'contract_type_id' => 'required|exists:contract_types,id',
            'number' => 'nullable|max:100|unique:contracts,number',
            'policy_number' => 'required|max:150',
            'contact_id' => 'required|exists:contacts,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date',
            'currency_code' => 'required|exists:currencies,code',
            'exchange_rate' => 'required|numeric',
            'coverage_amount' => 'required|numeric',
            'gross_premium' => 'required|numeric',
            'discount' => 'required|numeric',
            'stamp_fee' => 'required|numeric',
            'amount' => 'required|numeric',
            'memo' => 'nullable',
            'installment_count' => 'nullable|numeric',
            'details' => 'required|array',
            'details.*.insurance_id' => 'required|exists:contacts,id',
            'details.*.description' => 'nullable',
            'details.*.percentage' => 'required|numeric',
            'details.*.brokerage_fee' => 'required|numeric',
            'details.*.eng_fee' => 'required|numeric',
            'covered_item' => 'nullable|numeric',
        ];
    }

    // messages
    public function messages(): array
    {
        return [
            'contract_status.required' => 'Contract status is required',
            'contract_status.in' => 'Contract status is invalid',
            'contract_type_id.required' => 'Contract type is required',
            'contract_type_id.exists' => 'Contract type is invalid',
            'number.max' => 'Number must not be greater than 100 characters',
            'number.unique' => 'Number already exists',
            'policy_number.required' => 'Policy number is required',
            'policy_number.max' => 'Policy number must not be greater than 150 characters',
            'contact_id.required' => 'Contact is required',
            'contact_id.exists' => 'Contact is invalid',
            'period_start.required' => 'Period start is required',
            'period_start.date' => 'Period start must be a date',
            'period_end.required' => 'Period end is required',
            'period_end.date' => 'Period end must be a date',
            'currency_code.required' => 'Currency code is required',
            'currency_code.exists' => 'Currency code is invalid',
            'exchange_rate.required' => 'Exchange rate is required',
            'exchange_rate.numeric' => 'Exchange rate must be a number',
            'coverage_amount.required' => 'Coverage amount is required',
            'coverage_amount.numeric' => 'Coverage amount must be a number',
            'gross_premium.required' => 'Gross premium is required',
            'gross_premium.numeric' => 'Gross premium must be a number',
            'discount.required' => 'Discount is required',
            'discount.numeric' => 'Discount must be a number',
            'stamp_fee.required' => 'Stamp fee is required',
            'stamp_fee.numeric' => 'Stamp fee must be a number',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'memo.max' => 'Memo must not be greater than 255 characters',
            'installment_count.numeric' => 'Installment count must be a number',
            'details.required' => 'Details is required',
            'details.array' => 'Details must be an array',
            'details.*.insurance_id.required' => 'Insurance is required',
            'details.*.insurance_id.exists' => 'Insurance is invalid',
            'details.*.description.required' => 'Description is required',
            'details.*.percentage.required' => 'Percentage is required',
            'details.*.percentage.numeric' => 'Percentage must be a number',
            'details.*.brokerage_fee.required' => 'Brokerage fee is required',
            'details.*.brokerage_fee.numeric' => 'Brokerage fee must be a number',
            'details.*.eng_fee.required' => 'Eng fee is required',
            'details.*.eng_fee.numeric' => 'Eng fee must be a number',
        ];
    }

    // withValidator details percentage must be 100
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $totalPercentage = collect($this->input('details'))->sum('percentage');

            if ($totalPercentage != 100) {
                $validator->errors()->add('details', 'Total percentage must be 100');
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
