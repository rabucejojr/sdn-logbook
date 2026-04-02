<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientLogRequest extends FormRequest
{
    /**
     * This form is publicly accessible — no auth required.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Validation rules for the client visit log submission.
     */
    public function rules(): array
    {
        return [
            'firm_name' => ['required', 'string', 'max:255'],

            'client_name' => ['required', 'string', 'max:255'],

            'gender' => ['required', 'in:Male,Female,Prefer not to say'],

            'transaction_type' => [
                'required',
                'in:SETUP,GIA,CEST,Scholarship,S&T Referrals,Others',
            ],

            // Required only when transaction_type is "Others"
            'transaction_other_details' => [
                'nullable',
                'required_if:transaction_type,Others',
                'string',
                'max:500',
            ],

            'address' => ['required', 'string', 'max:255'],

            // Philippine mobile: 09XXXXXXXXX or +639XXXXXXXXX
            'contact_number' => [
                'required',
                'string',
                'regex:/^(\+?63|0)9\d{9}$/',
            ],
        ];
    }

    /**
     * Custom validation error messages.
     */
    public function messages(): array
    {
        return [
            'contact_number.regex' => 'Please enter a valid Philippine mobile number (e.g., 09XXXXXXXXX or +639XXXXXXXXX).',
            'transaction_other_details.required_if' => 'Please specify the transaction details when "Others" is selected.',
        ];
    }

    /**
     * Human-readable attribute names for error messages.
     */
    public function attributes(): array
    {
        return [
            'firm_name'                  => 'Name of Firm',
            'client_name'                => 'Name of Client',
            'gender'                     => 'Gender',
            'transaction_type'           => 'Details of Transaction',
            'transaction_other_details'  => 'Transaction specification',
            'address'                    => 'Address',
            'contact_number'             => 'Contact Number',
        ];
    }
}
