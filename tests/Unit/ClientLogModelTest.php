<?php

use App\Models\ClientLog;

// ─────────────────────────────────────────────────────────────────────────────
// transaction_display accessor
// ─────────────────────────────────────────────────────────────────────────────

describe('transaction_display accessor', function () {

    it('returns the type name for all standard transaction types', function (string $type) {
        $log = ClientLog::factory()->standardTransaction($type)->make();
        expect($log->transaction_display)->toBe($type);
    })->with(['SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals']);

    it('prefixes Others with the specification text', function () {
        $log = ClientLog::factory()->others('Needs assistance with packaging')->make();
        expect($log->transaction_display)->toBe('Others: Needs assistance with packaging');
    });

    it('returns plain Others when details are null', function () {
        $log = ClientLog::factory()->make([
            'transaction_type'          => 'Others',
            'transaction_other_details' => null,
        ]);
        expect($log->transaction_display)->toBe('Others');
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// scopeSearch
// ─────────────────────────────────────────────────────────────────────────────

describe('scopeSearch', function () {

    it('finds records by client name', function () {
        ClientLog::factory()->create(['client_name' => 'Maria Santos']);
        ClientLog::factory()->create(['client_name' => 'Jose Rizal']);

        expect(ClientLog::search('Maria')->count())->toBe(1)
            ->and(ClientLog::search('Maria')->first()->client_name)->toBe('Maria Santos');
    });

    it('finds records by firm name', function () {
        ClientLog::factory()->create(['firm_name' => 'Surigao Tech Hub']);
        ClientLog::factory()->create(['firm_name' => 'Pacific Trading Co.']);

        expect(ClientLog::search('Tech Hub')->count())->toBe(1);
    });

    it('finds records by address', function () {
        ClientLog::factory()->create(['address' => 'Surigao City, Surigao del Norte']);
        ClientLog::factory()->create(['address' => 'Dapa, Surigao del Norte']);

        expect(ClientLog::search('Surigao City')->count())->toBe(1);
    });

    it('finds records by transaction type', function () {
        ClientLog::factory()->standardTransaction('SETUP')->create();
        ClientLog::factory()->standardTransaction('GIA')->create();

        expect(ClientLog::search('SETUP')->count())->toBe(1);
    });

    it('is case-insensitive', function () {
        ClientLog::factory()->create(['client_name' => 'Juan dela Cruz']);

        expect(ClientLog::search('juan')->count())->toBe(1)
            ->and(ClientLog::search('JUAN')->count())->toBe(1)
            ->and(ClientLog::search('Juan')->count())->toBe(1);
    });

    it('returns all records when search term is null', function () {
        ClientLog::factory()->count(5)->create();
        expect(ClientLog::search(null)->count())->toBe(5);
    });

    it('returns all records when search term is an empty string', function () {
        ClientLog::factory()->count(3)->create();
        expect(ClientLog::search('')->count())->toBe(3);
    });

    it('returns zero records for a non-matching term', function () {
        ClientLog::factory()->create(['client_name' => 'Juan dela Cruz']);
        expect(ClientLog::search('zzznomatch')->count())->toBe(0);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// scopeDateRange
// ─────────────────────────────────────────────────────────────────────────────

describe('scopeDateRange', function () {

    it('filters by from date, excluding records before it', function () {
        ClientLog::factory()->create(['date_visited' => now()->subDays(10)]);
        ClientLog::factory()->create(['date_visited' => now()->subDays(2)]);
        ClientLog::factory()->create(['date_visited' => now()]);

        $from = now()->subDays(3)->format('Y-m-d');
        expect(ClientLog::dateRange($from, null)->count())->toBe(2);
    });

    it('filters by to date, excluding records after it', function () {
        ClientLog::factory()->create(['date_visited' => now()->subDays(10)]);
        ClientLog::factory()->create(['date_visited' => now()->subDays(2)]);
        ClientLog::factory()->create(['date_visited' => now()]);

        $to = now()->subDays(3)->format('Y-m-d');
        expect(ClientLog::dateRange(null, $to)->count())->toBe(1);
    });

    it('filters by a both-sided date range', function () {
        ClientLog::factory()->create(['date_visited' => now()->subDays(10)]);
        ClientLog::factory()->create(['date_visited' => now()->subDays(5)]);
        ClientLog::factory()->create(['date_visited' => now()]);

        $from = now()->subDays(7)->format('Y-m-d');
        $to   = now()->subDays(2)->format('Y-m-d');
        expect(ClientLog::dateRange($from, $to)->count())->toBe(1);
    });

    it('returns all records when both dates are null', function () {
        ClientLog::factory()->count(4)->create();
        expect(ClientLog::dateRange(null, null)->count())->toBe(4);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// scopeFilterGender
// ─────────────────────────────────────────────────────────────────────────────

describe('scopeFilterGender', function () {

    it('returns only records matching the given gender', function () {
        ClientLog::factory()->male()->count(3)->create();
        ClientLog::factory()->female()->count(2)->create();

        $results = ClientLog::filterGender('Male')->get();
        expect($results)->toHaveCount(3);
        $results->each(fn ($r) => expect($r->gender)->toBe('Male'));
    });

    it('returns all records when gender is null', function () {
        ClientLog::factory()->male()->count(2)->create();
        ClientLog::factory()->female()->count(2)->create();
        expect(ClientLog::filterGender(null)->count())->toBe(4);
    });

    it('returns all records when gender is an empty string', function () {
        ClientLog::factory()->count(3)->create();
        expect(ClientLog::filterGender('')->count())->toBe(3);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// scopeFilterTransaction
// ─────────────────────────────────────────────────────────────────────────────

describe('scopeFilterTransaction', function () {

    it('returns only records matching the given transaction type', function () {
        ClientLog::factory()->standardTransaction('SETUP')->count(2)->create();
        ClientLog::factory()->standardTransaction('GIA')->count(3)->create();

        $results = ClientLog::filterTransaction('GIA')->get();
        expect($results)->toHaveCount(3);
        $results->each(fn ($r) => expect($r->transaction_type)->toBe('GIA'));
    });

    it('returns all records when type is null', function () {
        ClientLog::factory()->count(5)->create();
        expect(ClientLog::filterTransaction(null)->count())->toBe(5);
    });
});

// ─────────────────────────────────────────────────────────────────────────────
// Chained scopes
// ─────────────────────────────────────────────────────────────────────────────

it('can chain multiple scopes together accurately', function () {
    ClientLog::factory()->male()->standardTransaction('SETUP')->visitedToday()->count(3)->create();
    ClientLog::factory()->female()->standardTransaction('SETUP')->visitedToday()->count(2)->create();
    ClientLog::factory()->male()->standardTransaction('GIA')->visitedToday()->create();

    expect(
        ClientLog::filterGender('Male')->filterTransaction('SETUP')->count()
    )->toBe(3);
});
