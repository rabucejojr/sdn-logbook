<?php

use App\Models\ClientLog;
use App\Models\User;

// ─────────────────────────────────────────────────────────────────────────────
// Setup
// ─────────────────────────────────────────────────────────────────────────────

beforeEach(function () {
    $this->admin = User::factory()->create();
});

// ─────────────────────────────────────────────────────────────────────────────
// Page load
// ─────────────────────────────────────────────────────────────────────────────

describe('dashboard page load', function () {

    it('loads for authenticated admin', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewIs('admin.dashboard');
    });

    it('passes all required view variables', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHasAll([
                'logs', 'totalVisitors', 'todayVisitors', 'weekVisitors', 'monthVisitors',
                'transactionDistribution', 'genderDistribution',
                'visitorsOverTime', 'topMunicipalities',
                'transactionTypes', 'genders',
            ]);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Data table rendering
// ─────────────────────────────────────────────────────────────────────────────

describe('data table', function () {

    it('displays submitted records', function () {
        ClientLog::factory()->create(['client_name' => 'Pedro Reyes', 'firm_name' => 'Caraga Ventures']);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSee('Pedro Reyes')
            ->assertSee('Caraga Ventures');
    });

    it('displays Others transaction with its detail text', function () {
        ClientLog::factory()->others('Custom barangay project consultation')->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSee('Others')
            ->assertSee('Custom barangay project consultation');
    });

    it('shows a no-records message when the table is empty', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertSee('No records found');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Analytics counts
// ─────────────────────────────────────────────────────────────────────────────

describe('analytics counts', function () {

    it('reports accurate total visitor count', function () {
        ClientLog::factory()->count(7)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('totalVisitors', 7);
    });

    it('counts only today\'s visitors in todayVisitors', function () {
        ClientLog::factory()->visitedToday()->count(3)->create();
        ClientLog::factory()->visitedLastMonth()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('totalVisitors', 8)
            ->assertViewHas('todayVisitors', 3);
    });

    it('counts only this month\'s visitors in monthVisitors', function () {
        ClientLog::factory()->visitedThisMonth()->count(4)->create();
        ClientLog::factory()->visitedLastMonth()->count(6)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('monthVisitors', 4);
    });

    it('returns zeros when no records exist', function () {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertViewHas('totalVisitors', 0)
            ->assertViewHas('todayVisitors', 0)
            ->assertViewHas('weekVisitors', 0)
            ->assertViewHas('monthVisitors', 0);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Search
// ─────────────────────────────────────────────────────────────────────────────

describe('search', function () {

    it('finds records by client name', function () {
        ClientLog::factory()->create(['client_name' => 'Luz Tanaka']);
        ClientLog::factory()->create(['client_name' => 'Roberto Cruz']);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['search' => 'Luz Tanaka']))
            ->assertSee('Luz Tanaka')
            ->assertDontSee('Roberto Cruz');
    });

    it('finds records by firm name', function () {
        ClientLog::factory()->create(['firm_name' => 'Pacific Tech Corp']);
        ClientLog::factory()->create(['firm_name' => 'Mindanao Traders']);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['search' => 'Pacific']))
            ->assertSee('Pacific Tech Corp')
            ->assertDontSee('Mindanao Traders');
    });

    it('finds records by address', function () {
        // Use unique client/firm names that cannot accidentally match the
        // search term, making the count assertion fully deterministic.
        ClientLog::factory()->create([
            'address'     => 'Surigao City, Surigao del Norte',
            'client_name' => 'SearchByAddressClientA',
            'firm_name'   => 'SearchByAddressFirmA',
        ]);
        ClientLog::factory()->create([
            'address'     => 'Dapa, Surigao del Norte',
            'client_name' => 'SearchByAddressClientB',
            'firm_name'   => 'SearchByAddressFirmB',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['search' => 'Surigao City']));

        // Exactly 1 record must match; assert on the paginator count
        $response->assertViewHas('logs', fn ($logs) => $logs->total() === 1);
        $response->assertSee('Surigao City, Surigao del Norte');
    });

    it('shows no records message when search has no match', function () {
        ClientLog::factory()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['search' => 'zzznomatch']))
            ->assertSee('No records found');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Filters
// ─────────────────────────────────────────────────────────────────────────────

describe('filters', function () {

    it('filters by gender', function () {
        ClientLog::factory()->male()->count(2)->create();
        ClientLog::factory()->female()->count(3)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['gender' => 'Male']))
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 2);
    });

    it('filters by transaction type', function () {
        ClientLog::factory()->standardTransaction('SETUP')->count(4)->create();
        ClientLog::factory()->standardTransaction('GIA')->count(2)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['transaction_type' => 'GIA']))
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 2);
    });

    it('filters by date range from-date', function () {
        ClientLog::factory()->create(['date_visited' => now()->subDays(30)]);
        ClientLog::factory()->create(['date_visited' => now()->subDays(5)]);
        ClientLog::factory()->create(['date_visited' => now()]);

        $from = now()->subDays(10)->format('Y-m-d');

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['date_from' => $from]))
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 2);
    });

    it('filters by date range to-date', function () {
        ClientLog::factory()->create(['date_visited' => now()->subDays(30)]);
        ClientLog::factory()->create(['date_visited' => now()->subDays(5)]);
        ClientLog::factory()->create(['date_visited' => now()]);

        $to = now()->subDays(10)->format('Y-m-d');

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['date_to' => $to]))
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 1);
    });

    it('applies combined gender + transaction + date filters', function () {
        ClientLog::factory()->male()->standardTransaction('SETUP')->visitedToday()->count(2)->create();
        ClientLog::factory()->female()->standardTransaction('SETUP')->visitedToday()->create();
        ClientLog::factory()->male()->standardTransaction('GIA')->visitedToday()->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', [
                'gender'           => 'Male',
                'transaction_type' => 'SETUP',
                'date_from'        => today()->format('Y-m-d'),
                'date_to'          => today()->format('Y-m-d'),
            ]))
            ->assertViewHas('logs', fn ($logs) => $logs->total() === 2);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Sorting
// ─────────────────────────────────────────────────────────────────────────────

describe('sorting', function () {

    it('defaults to newest records first', function () {
        ClientLog::factory()->create(['date_visited' => now()->subDays(10), 'client_name' => 'Older']);
        ClientLog::factory()->create(['date_visited' => now(),              'client_name' => 'Newer']);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('logs', fn ($logs) => $logs->first()->client_name === 'Newer');
    });

    it('sorts by date ascending when requested', function () {
        ClientLog::factory()->create(['date_visited' => now()->subDays(5), 'client_name' => 'Older']);
        ClientLog::factory()->create(['date_visited' => now(),              'client_name' => 'Newer']);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['sort_by' => 'date_visited', 'sort_dir' => 'asc']))
            ->assertViewHas('logs', fn ($logs) => $logs->first()->client_name === 'Older');
    });

    it('sorts by client name alphabetically', function () {
        ClientLog::factory()->create(['client_name' => 'Zorro Valdez']);
        ClientLog::factory()->create(['client_name' => 'Ana Reyes']);

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['sort_by' => 'client_name', 'sort_dir' => 'asc']))
            ->assertViewHas('logs', fn ($logs) => $logs->first()->client_name === 'Ana Reyes');
    });

    it('defaults to date_visited when sort column is invalid', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['sort_by' => 'id; DROP TABLE client_logs; --']))
            ->assertOk()
            ->assertViewHas('sortBy', 'date_visited');
    });

    it('defaults to desc when sort direction is invalid', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['sort_dir' => 'INVALID']))
            ->assertOk()
            ->assertViewHas('sortDir', 'desc');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Pagination
// ─────────────────────────────────────────────────────────────────────────────

describe('pagination', function () {

    it('shows 20 records per page', function () {
        ClientLog::factory()->count(25)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard'))
            ->assertViewHas('logs', fn ($logs) => $logs->count() === 20 && $logs->total() === 25);
    });

    it('shows remaining records on page 2', function () {
        ClientLog::factory()->count(25)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.dashboard', ['page' => 2]))
            ->assertViewHas('logs', fn ($logs) => $logs->count() === 5 && $logs->currentPage() === 2);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Delete record
// ─────────────────────────────────────────────────────────────────────────────

describe('delete record', function () {

    it('deletes the record and redirects to dashboard', function () {
        $log = ClientLog::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.logs.destroy', $log))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseMissing('client_logs', ['id' => $log->id]);
    });

    it('flashes a success message after deletion', function () {
        $log = ClientLog::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('admin.logs.destroy', $log))
            ->assertSessionHas('success');
    });

    it('returns 404 when trying to delete a non-existent record', function () {
        $this->actingAs($this->admin)
            ->delete(route('admin.logs.destroy', 9999))
            ->assertNotFound();
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Print view
// ─────────────────────────────────────────────────────────────────────────────

describe('print view', function () {

    it('is accessible to authenticated admin', function () {
        $this->actingAs($this->admin)
            ->get(route('admin.logs.print'))
            ->assertOk()
            ->assertViewIs('admin.logs.print');
    });

    it('shows all records', function () {
        ClientLog::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.logs.print'))
            ->assertViewHas('logs', fn ($logs) => $logs->count() === 5);
    });

    it('respects filters', function () {
        ClientLog::factory()->male()->count(3)->create();
        ClientLog::factory()->female()->count(4)->create();

        $this->actingAs($this->admin)
            ->get(route('admin.logs.print', ['gender' => 'Male']))
            ->assertViewHas('logs', fn ($logs) => $logs->count() === 3);
    });
});
