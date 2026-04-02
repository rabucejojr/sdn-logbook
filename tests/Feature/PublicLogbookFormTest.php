<?php

use App\Models\ClientLog;

// ─────────────────────────────────────────────────────────────────────────────
// Helper
// ─────────────────────────────────────────────────────────────────────────────

function formPayload(array $overrides = []): array
{
    return array_merge([
        'firm_name'        => 'Surigao Tech Firm',
        'client_name'      => 'Maria Santos',
        'gender'           => 'Female',
        'transaction_type' => 'SETUP',
        'address'          => 'Surigao City, Surigao del Norte',
        'contact_number'   => '09171234567',
    ], $overrides);
}

// ─────────────────────────────────────────────────────────────────────────────
// Form display
// ─────────────────────────────────────────────────────────────────────────────

describe('logbook form display', function () {

    it('loads on the public route', function () {
        $this->get(route('logbook.index'))
            ->assertOk()
            ->assertViewIs('logbook.index')
            ->assertSee('Client Visit Logbook');
    });

    it('contains all required input fields', function () {
        $response = $this->get(route('logbook.index'));
        foreach (['firm_name', 'client_name', 'gender', 'transaction_type', 'address', 'contact_number'] as $field) {
            $response->assertSee($field, false);
        }
    });

    it('does not expose a date_visited input field', function () {
        // date_visited is set server-side; the user must never control it
        $this->get(route('logbook.index'))
            ->assertDontSee('name="date_visited"', false);
    });

    it('contains a CSRF token in the form', function () {
        $this->get(route('logbook.index'))
            ->assertSee('name="_token"', false);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Successful submission
// ─────────────────────────────────────────────────────────────────────────────

describe('successful form submission', function () {

    it('creates a database record and redirects to success page', function () {
        $this->post(route('logbook.store'), formPayload())
            ->assertRedirect(route('logbook.success'));

        expect(ClientLog::count())->toBe(1);
        $this->assertDatabaseHas('client_logs', [
            'firm_name'                 => 'Surigao Tech Firm',
            'client_name'               => 'Maria Santos',
            'gender'                    => 'Female',
            'transaction_type'          => 'SETUP',
            'transaction_other_details' => null,
        ]);
    });

    it('sets date_visited server-side, not from user input', function () {
        $this->post(route('logbook.store'), formPayload([
            'date_visited' => '2000-01-01 00:00:00', // attempt to inject
        ]));

        $log = ClientLog::first();
        expect($log->date_visited)->not->toBeNull();
        expect($log->date_visited->format('Y-m-d'))->not->toBe('2000-01-01');
        expect(abs(now()->timestamp - $log->date_visited->timestamp))->toBeLessThan(10);
    });

    it('stores details for Others transaction type', function () {
        $this->post(route('logbook.store'), formPayload([
            'transaction_type'          => 'Others',
            'transaction_other_details' => 'Requesting S&T consultation for barangay project',
        ]));

        $this->assertDatabaseHas('client_logs', [
            'transaction_type'          => 'Others',
            'transaction_other_details' => 'Requesting S&T consultation for barangay project',
        ]);
    });

    it('stores each standard transaction type correctly', function (string $type) {
        $this->post(route('logbook.store'), formPayload(['transaction_type' => $type]));
        $this->assertDatabaseHas('client_logs', ['transaction_type' => $type]);
    })->with(['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals']);

    it('accepts 09XXXXXXXXX format contact numbers', function () {
        $this->post(route('logbook.store'), formPayload(['contact_number' => '09171234567']))
            ->assertRedirect(route('logbook.success'));
    });

    it('accepts +639XXXXXXXXX format contact numbers', function () {
        $this->post(route('logbook.store'), formPayload(['contact_number' => '+639171234567']))
            ->assertRedirect(route('logbook.success'));
    });

    it('accepts 639XXXXXXXXX format contact numbers', function () {
        $this->post(route('logbook.store'), formPayload(['contact_number' => '639171234567']))
            ->assertRedirect(route('logbook.success'));
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Data integrity: transaction_other_details
// ─────────────────────────────────────────────────────────────────────────────

it('nullifies transaction_other_details for non-Others types even if POSTed', function () {
    // An attacker could POST this hidden field manually
    $this->post(route('logbook.store'), formPayload([
        'transaction_type'          => 'SETUP',
        'transaction_other_details' => 'Injected data that should be discarded',
    ]));

    $this->assertDatabaseHas('client_logs', [
        'transaction_type'          => 'SETUP',
        'transaction_other_details' => null,
    ]);
});

// ─────────────────────────────────────────────────────────────────────────────
// Success page guard
// ─────────────────────────────────────────────────────────────────────────────

describe('success page guard', function () {

    it('redirects to form when success page accessed directly', function () {
        $this->get(route('logbook.success'))
            ->assertRedirect(route('logbook.index'));
    });

    it('shows success page after a valid form submission', function () {
        $this->post(route('logbook.store'), formPayload());

        $this->get(route('logbook.success'))
            ->assertOk()
            ->assertViewIs('logbook.success')
            ->assertSee('Visit Logged Successfully');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Old input retained on validation failure
// ─────────────────────────────────────────────────────────────────────────────

it('retains old input values when validation fails', function () {
    $this->post(route('logbook.store'), formPayload([
        'client_name'    => 'Retained Name',
        'contact_number' => 'bad-number',
    ]))->assertSessionHasErrors('contact_number');

    $this->get(route('logbook.index'))
        ->assertSee('Retained Name');
});

// ─────────────────────────────────────────────────────────────────────────────
// Validation failures
// ─────────────────────────────────────────────────────────────────────────────

it('returns errors for all required fields when form is empty', function () {
    $this->post(route('logbook.store'), [])
        ->assertSessionHasErrors([
            'firm_name', 'client_name', 'gender',
            'transaction_type', 'address', 'contact_number',
        ]);
});

it('fails when Others is selected without a specification', function () {
    $this->post(route('logbook.store'), formPayload([
        'transaction_type'          => 'Others',
        'transaction_other_details' => '',
    ]))->assertSessionHasErrors('transaction_other_details');

    expect(ClientLog::count())->toBe(0);
});

it('rejects invalid contact number formats', function (string $number) {
    $this->post(route('logbook.store'), formPayload(['contact_number' => $number]))
        ->assertSessionHasErrors('contact_number');

    expect(ClientLog::count())->toBe(0);
})->with([
    'letters'          => ['ABCDEFGHIJK'],
    'too short'        => ['091234'],
    'wrong prefix'     => ['08123456789'],
    'spaces'           => ['0917 123 4567'],
    'dashes'           => ['0917-123-4567'],
    'empty'            => [''],
]);

// ─────────────────────────────────────────────────────────────────────────────
// Duplicate entries
// ─────────────────────────────────────────────────────────────────────────────

it('allows duplicate submissions as each visit is a separate event', function () {
    $this->post(route('logbook.store'), formPayload());
    $this->post(route('logbook.store'), formPayload());
    expect(ClientLog::count())->toBe(2);
});

// ─────────────────────────────────────────────────────────────────────────────
// Special characters
// ─────────────────────────────────────────────────────────────────────────────

it('stores special characters in firm name correctly', function () {
    $name = "O'Brien & Associates — Surigao";
    $this->post(route('logbook.store'), formPayload(['firm_name' => $name]));
    $this->assertDatabaseHas('client_logs', ['firm_name' => $name]);
});

it('stores unicode characters in client name correctly', function () {
    $name = 'Ñoño García López';
    $this->post(route('logbook.store'), formPayload(['client_name' => $name]));
    $this->assertDatabaseHas('client_logs', ['client_name' => $name]);
});
