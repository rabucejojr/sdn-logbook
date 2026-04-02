<?php

use App\Models\ClientLog;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create();
});

// ─────────────────────────────────────────────────────────────────────────────
// Access control
// ─────────────────────────────────────────────────────────────────────────────

it('denies CSV export to unauthenticated users', function () {
    $this->get(route('admin.export.csv'))
        ->assertRedirect(route('login'));
});

it('allows CSV export for authenticated admin', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->assertOk();
});

// ─────────────────────────────────────────────────────────────────────────────
// Response headers
// ─────────────────────────────────────────────────────────────────────────────

it('returns text/csv content-type header', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
});

it('returns an attachment content-disposition with the correct filename pattern', function () {
    $disposition = $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->headers->get('Content-Disposition');

    expect($disposition)
        ->toContain('attachment')
        ->toContain('client_logs_')
        ->toContain('.csv');
});

// ─────────────────────────────────────────────────────────────────────────────
// Content correctness
// ─────────────────────────────────────────────────────────────────────────────

it('contains all 7 required column headers', function () {
    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->streamedContent();

    foreach (['Date Visited', 'Name of Firm', 'Name of Client', 'Gender', 'Details of Transaction', 'Address', 'Contact Number'] as $header) {
        expect($content)->toContain($header);
    }
});

it('contains correct data values for each record', function () {
    ClientLog::factory()->create([
        'firm_name'      => 'Surigao Exports Inc.',
        'client_name'    => 'Carlo Mendez',
        'gender'         => 'Male',
        'transaction_type' => 'GIA',
        'address'        => 'Surigao City',
        'contact_number' => '09171234567',
    ]);

    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->streamedContent();

    expect($content)
        ->toContain('Surigao Exports Inc.')
        ->toContain('Carlo Mendez')
        ->toContain('Male')
        ->toContain('GIA')
        ->toContain('09171234567');
});

it('formats Others transaction as "Others: {details}"', function () {
    ClientLog::factory()->others('Barangay livelihood program inquiry')->create();

    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->streamedContent();

    expect($content)->toContain('Others: Barangay livelihood program inquiry');
});

it('contains only a header row when there are no records', function () {
    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->streamedContent();

    $cleaned = ltrim($content, "\xEF\xBB\xBF");
    $lines   = array_values(array_filter(explode("\n", trim($cleaned))));

    expect($lines)->toHaveCount(1); // Header row only
});

it('has the correct row count matching total records', function () {
    ClientLog::factory()->count(5)->create();

    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->streamedContent();

    $cleaned = ltrim($content, "\xEF\xBB\xBF");
    $lines   = array_values(array_filter(explode("\n", trim($cleaned))));

    expect($lines)->toHaveCount(6); // 1 header + 5 records
});

it('starts with a UTF-8 BOM for Excel compatibility', function () {
    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv'))
        ->streamedContent();

    expect(substr($content, 0, 3))->toBe("\xEF\xBB\xBF");
});

// ─────────────────────────────────────────────────────────────────────────────
// Filter passthrough
// ─────────────────────────────────────────────────────────────────────────────

it('respects gender filter in the export', function () {
    ClientLog::factory()->male()->count(3)->create(['client_name' => 'Male Client']);
    ClientLog::factory()->female()->count(2)->create(['client_name' => 'Female Client']);

    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv', ['gender' => 'Female']))
        ->streamedContent();

    expect($content)
        ->toContain('Female Client')
        ->not->toContain('Male Client');
});

it('respects transaction type filter in the export', function () {
    ClientLog::factory()->standardTransaction('SETUP')->create(['firm_name' => 'Setup Firm']);
    ClientLog::factory()->standardTransaction('GIA')->create(['firm_name' => 'GIA Firm']);

    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv', ['transaction_type' => 'SETUP']))
        ->streamedContent();

    expect($content)
        ->toContain('Setup Firm')
        ->not->toContain('GIA Firm');
});

it('respects search filter in the export', function () {
    ClientLog::factory()->create(['client_name' => 'Visible Client']);
    ClientLog::factory()->create(['client_name' => 'Hidden Client']);

    $content = $this->actingAs($this->admin)
        ->get(route('admin.export.csv', ['search' => 'Visible']))
        ->streamedContent();

    expect($content)
        ->toContain('Visible Client')
        ->not->toContain('Hidden Client');
});
