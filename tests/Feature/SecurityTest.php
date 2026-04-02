<?php

use App\Models\ClientLog;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
});

// ─── helper ──────────────────────────────────────────────────────────────────

function secPayload(array $overrides = []): array
{
    return array_merge([
        'firm_name'        => 'Test Firm',
        'client_name'      => 'Test Client',
        'gender'           => 'Male',
        'transaction_type' => 'SETUP',
        'address'          => 'Surigao City',
        'contact_number'   => '09171234567',
    ], $overrides);
}

// ─────────────────────────────────────────────────────────────────────────────
// CSRF
// ─────────────────────────────────────────────────────────────────────────────

it('logbook form exposes a CSRF token', function () {
    $this->get(route('logbook.index'))
        ->assertSee('name="_token"', false);
});

it('login form exposes a CSRF token', function () {
    $this->get(route('login'))
        ->assertSee('name="_token"', false);
});

// ─────────────────────────────────────────────────────────────────────────────
// SQL injection prevention
// ─────────────────────────────────────────────────────────────────────────────

it('survives SQL injection in the search parameter without crashing', function (string $injection) {
    ClientLog::factory()->create(['client_name' => 'Safe Record']);

    $this->actingAs($this->admin)
        ->get(route('admin.dashboard', ['search' => $injection]))
        ->assertOk();

    // Table must still exist with its data intact
    $this->assertDatabaseHas('client_logs', ['client_name' => 'Safe Record']);
})->with([
    "'; DROP TABLE client_logs; --",
    "' OR '1'='1",
    "1; SELECT * FROM users; --",
    "' UNION SELECT null,null,null,null,null,null,null --",
]);

it('neutralises SQL injection in the sort_by parameter', function (string $injection) {
    ClientLog::factory()->count(3)->create();

    $this->actingAs($this->admin)
        ->get(route('admin.dashboard', ['sort_by' => $injection]))
        ->assertOk()
        ->assertViewHas('sortBy', 'date_visited'); // safely defaulted

    $this->assertDatabaseCount('client_logs', 3);
})->with([
    'id; DROP TABLE client_logs; --',
    'client_name UNION SELECT password FROM users--',
    '(SELECT * FROM users)',
]);

it('neutralises invalid sort_dir values', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.dashboard', ['sort_dir' => 'ASC; DROP TABLE client_logs;']))
        ->assertOk()
        ->assertViewHas('sortDir', 'desc');
});

it('stores an SQL injection attempt as literal text (Eloquent parameterisation)', function () {
    $injection = "Robert'); DROP TABLE client_logs; --";

    $this->post(route('logbook.store'), secPayload(['client_name' => $injection]));

    $this->assertDatabaseHas('client_logs', ['client_name' => $injection]);
    $this->assertDatabaseCount('client_logs', 1); // table still alive
});

// ─────────────────────────────────────────────────────────────────────────────
// XSS prevention
// ─────────────────────────────────────────────────────────────────────────────

it('stores XSS payload as raw text in the database', function () {
    $xss = '<script>alert("xss")</script>';

    $this->post(route('logbook.store'), secPayload(['client_name' => $xss]));

    $this->assertDatabaseHas('client_logs', ['client_name' => $xss]);
});

it('HTML-escapes XSS payload in the admin dashboard output', function () {
    $xss = '<script>alert("xss")</script>';
    ClientLog::factory()->create(['client_name' => $xss]);

    $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

    // Raw script tag must NOT appear (would execute in browser)
    $response->assertDontSee($xss, false);
    // Blade-escaped version must appear instead
    $response->assertSee('&lt;script&gt;', false);
});

it('HTML-escapes XSS payload in firm name', function () {
    $xss = '<img src=x onerror=alert(1)>';
    ClientLog::factory()->create(['firm_name' => $xss]);

    $this->actingAs($this->admin)
        ->get(route('admin.dashboard'))
        ->assertDontSee($xss, false)
        ->assertSee('&lt;img', false);
});

it('HTML-escapes XSS payload in the print view', function () {
    $xss = '<script>document.cookie</script>';
    ClientLog::factory()->create(['client_name' => $xss]);

    $this->actingAs($this->admin)
        ->get(route('admin.logs.print'))
        ->assertDontSee($xss, false)
        ->assertSee('&lt;script&gt;', false);
});

// ─────────────────────────────────────────────────────────────────────────────
// Auth middleware — every admin route must require authentication
// ─────────────────────────────────────────────────────────────────────────────

it('redirects unauthenticated requests on all admin routes', function (string $method, string $route, array $params) {
    $this->{$method}(route($route, $params))
        ->assertRedirect(route('login'));
})->with([
    'GET dashboard'  => ['get',    'admin.dashboard', []],
    'GET print'      => ['get',    'admin.logs.print', []],
    'GET export csv' => ['get',    'admin.export.csv', []],
    'DELETE log'     => ['delete', 'admin.logs.destroy', [1]],
]);

// ─────────────────────────────────────────────────────────────────────────────
// Input edge cases
// ─────────────────────────────────────────────────────────────────────────────

it('accepts inputs right at the 255-character limit', function () {
    $this->post(route('logbook.store'), secPayload([
        'firm_name'   => str_repeat('A', 255),
        'client_name' => str_repeat('B', 255),
        'address'     => str_repeat('C', 255),
    ]))->assertRedirect(route('logbook.success'));

    $this->assertDatabaseCount('client_logs', 1);
});
