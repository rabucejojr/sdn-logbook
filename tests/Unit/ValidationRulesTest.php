<?php

// ─────────────────────────────────────────────────────────────────────────────
// Helper: default valid payload
// ─────────────────────────────────────────────────────────────────────────────

function validPayload(array $overrides = []): array
{
    return array_merge([
        'firm_name'        => 'Test Firm',
        'client_name'      => 'Juan dela Cruz',
        'gender'           => 'Male',
        'transaction_type' => 'SETUP',
        'address'          => 'Surigao City, Surigao del Norte',
        'contact_number'   => '09123456789',
    ], $overrides);
}

// ─────────────────────────────────────────────────────────────────────────────
// Required fields
// ─────────────────────────────────────────────────────────────────────────────

it('rejects submission when required field is missing', function (string $field) {
    $payload = validPayload();
    unset($payload[$field]);

    $this->post(route('logbook.store'), $payload)
        ->assertSessionHasErrors($field);
})->with([
    'firm_name', 'client_name', 'gender',
    'transaction_type', 'address', 'contact_number',
]);

it('rejects empty strings for text required fields', function (string $field) {
    $this->post(route('logbook.store'), validPayload([$field => '']))
        ->assertSessionHasErrors($field);
})->with(['firm_name', 'client_name', 'address']);

// ─────────────────────────────────────────────────────────────────────────────
// String length limits
// ─────────────────────────────────────────────────────────────────────────────

it('rejects firm_name exceeding 255 characters', function () {
    $this->post(route('logbook.store'), validPayload(['firm_name' => str_repeat('A', 256)]))
        ->assertSessionHasErrors('firm_name');
});

it('accepts client_name at exactly 255 characters', function () {
    $this->post(route('logbook.store'), validPayload(['client_name' => str_repeat('A', 255)]))
        ->assertSessionDoesntHaveErrors('client_name');
});

// ─────────────────────────────────────────────────────────────────────────────
// Gender enum
// ─────────────────────────────────────────────────────────────────────────────

it('accepts valid gender values', function (string $gender) {
    $this->post(route('logbook.store'), validPayload(['gender' => $gender]))
        ->assertSessionDoesntHaveErrors('gender');
})->with(['Male', 'Female', 'Prefer not to say']);

it('rejects an invalid gender value', function () {
    $this->post(route('logbook.store'), validPayload(['gender' => 'Unknown']))
        ->assertSessionHasErrors('gender');
});

// ─────────────────────────────────────────────────────────────────────────────
// Transaction type enum
// ─────────────────────────────────────────────────────────────────────────────

it('accepts all valid transaction types', function (string $type) {
    $payload = validPayload(['transaction_type' => $type]);
    if ($type === 'Others') {
        $payload['transaction_other_details'] = 'Some details';
    }

    $this->post(route('logbook.store'), $payload)
        ->assertSessionDoesntHaveErrors('transaction_type');
})->with(['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals', 'Others']);

it('rejects an invalid transaction type', function () {
    $this->post(route('logbook.store'), validPayload(['transaction_type' => 'INVALID_TYPE']))
        ->assertSessionHasErrors('transaction_type');
});

// ─────────────────────────────────────────────────────────────────────────────
// Others conditional rule
// ─────────────────────────────────────────────────────────────────────────────

it('requires transaction_other_details when Others is selected', function () {
    $this->post(route('logbook.store'), validPayload([
        'transaction_type'          => 'Others',
        'transaction_other_details' => '',
    ]))->assertSessionHasErrors('transaction_other_details');
});

it('rejects Others selection with no details field submitted', function () {
    $payload = validPayload(['transaction_type' => 'Others']);
    unset($payload['transaction_other_details']);

    $this->post(route('logbook.store'), $payload)
        ->assertSessionHasErrors('transaction_other_details');
});

it('does not require transaction_other_details for standard types', function (string $type) {
    $payload = validPayload(['transaction_type' => $type]);
    unset($payload['transaction_other_details']);

    $this->post(route('logbook.store'), $payload)
        ->assertSessionDoesntHaveErrors('transaction_other_details');
})->with(['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals']);

it('rejects transaction_other_details exceeding 500 characters', function () {
    $this->post(route('logbook.store'), validPayload([
        'transaction_type'          => 'Others',
        'transaction_other_details' => str_repeat('X', 501),
    ]))->assertSessionHasErrors('transaction_other_details');
});

// ─────────────────────────────────────────────────────────────────────────────
// Contact number — Philippine mobile format
// ─────────────────────────────────────────────────────────────────────────────

it('accepts valid Philippine mobile number formats', function (string $number) {
    $this->post(route('logbook.store'), validPayload(['contact_number' => $number]))
        ->assertSessionDoesntHaveErrors('contact_number');
})->with([
    'standard 09 format'   => ['09123456789'],
    'alternate 09 format'  => ['09987654321'],
    '+63 format'           => ['+639123456789'],
    '63 format (no plus)'  => ['639123456789'],
]);

it('rejects invalid contact number formats', function (string $number) {
    $this->post(route('logbook.store'), validPayload(['contact_number' => $number]))
        ->assertSessionHasErrors('contact_number');
})->with([
    'letters only'          => ['ABCDEFGHIJK'],
    'too short'             => ['0912345'],
    'too long'              => ['091234567890123'],
    'wrong prefix 08'       => ['08123456789'],
    'wrong prefix +62'      => ['+629123456789'],
    'landline format'       => ['(086) 826-1234'],
    'spaces inside'         => ['0912 345 6789'],
    'dashes inside'         => ['0912-345-6789'],
    'empty string'          => [''],
    'special chars'         => ['09!2345678@'],
]);
