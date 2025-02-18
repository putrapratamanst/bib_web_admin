<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCashBankRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'number' => ['required', 'max:100'],
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
}
