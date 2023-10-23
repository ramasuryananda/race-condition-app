<?php

namespace App\Http\Requests\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "source_account_id" => "required|numeric|exists:accounts,id|different:destination_account_id",
            "destination_account_id" => "required|numeric|exists:accounts,id|different:source_account_id",
            "nominal" => "required|numeric|min:0",
        ];
    }
}
