<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // already guarded by auth middleware on admin routes
    }

    public function rules(): array
    {
        return [
            'date_visited' => ['required', 'date'],

            'firm_name'    => ['required', 'string', 'max:255'],

            'client_name'   => ['required', 'array', 'min:1'],
            'client_name.*' => ['required', 'string', 'max:255'],

            'gender' => ['required', 'in:Male,Female,Prefer not to say'],

            'transaction_type'   => ['required', 'array', 'min:1'],
            'transaction_type.*' => ['required', 'in:SETUP,GIA,CEST,Scholarship,S&T Referrals,Others'],

            'transaction_other_details' => [
                'nullable',
                Rule::requiredIf(fn () => in_array('Others', (array) $this->input('transaction_type', []))),
                'string',
                'max:500',
            ],

            'address'        => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^(\+?63|0)9\d{9}$/'],
            'email'          => ['nullable', 'email', 'max:255'],

            'attended_by' => ['required', 'string', 'max:255'],
            'remarks'     => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact_number.regex'               => 'Please enter a valid Philippine mobile number (e.g., 09XXXXXXXXX or +639XXXXXXXXX).',
            'transaction_other_details.required' => 'Please specify the transaction details when "Others" is selected.',
            'transaction_type.required'          => 'Please select at least one transaction type.',
            'client_name.required'               => 'Please enter at least one client name.',
            'client_name.*.required'             => 'Each client name field must not be empty.',
            'attended_by.required'               => 'Please enter the name of the staff member who attended to this client.',
        ];
    }

    public function attributes(): array
    {
        return [
            'date_visited'               => 'Date Visited',
            'firm_name'                  => 'Name of Firm',
            'client_name'                => 'Name of Client',
            'client_name.*'              => 'Client name',
            'gender'                     => 'Gender',
            'transaction_type'           => 'Details of Transaction',
            'transaction_type.*'         => 'Transaction type',
            'transaction_other_details'  => 'Transaction specification',
            'address'                    => 'Address',
            'contact_number'             => 'Contact Number',
            'email'                      => 'Email Address',
            'attended_by'                => 'Attended by',
            'remarks'                    => 'Remarks',
        ];
    }
}
