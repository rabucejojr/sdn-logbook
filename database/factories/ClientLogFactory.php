<?php

namespace Database\Factories;

use App\Models\ClientLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory for generating realistic ClientLog test records.
 */
class ClientLogFactory extends Factory
{
    protected $model = ClientLog::class;

    /** Municipality/city pool matching Surigao del Norte geography */
    private array $surigaoAddresses = [
        'Surigao City, Surigao del Norte',
        'Dapa, Surigao del Norte',
        'General Luna, Surigao del Norte',
        'Del Carmen, Surigao del Norte',
        'San Isidro, Surigao del Norte',
        'Burgos, Surigao del Norte',
        'Tubod, Surigao del Norte',
        'Malimono, Surigao del Norte',
        'Gigaquit, Surigao del Norte',
        'Mainit, Surigao del Norte',
    ];

    public function definition(): array
    {
        $transactionType = $this->faker->randomElement([
            'SETUP', 'GIA', 'CEST', 'Scholarship', 'S&T Referrals', 'Others',
        ]);

        return [
            'date_visited'              => $this->faker->dateTimeBetween('-1 year', 'now'),
            'firm_name'                 => $this->faker->company(),
            'client_name'               => $this->faker->name(),
            'gender'                    => $this->faker->randomElement(['Male', 'Female', 'Prefer not to say']),
            'transaction_type'          => $transactionType,
            'transaction_other_details' => $transactionType === 'Others'
                ? $this->faker->sentence(6)
                : null,
            'address'                   => $this->faker->randomElement($this->surigaoAddresses),
            'contact_number'            => '09' . $this->faker->numerify('#########'),
        ];
    }

    // ─── Named states ────────────────────────────────────────────────────────

    /** Force transaction_type = 'Others' with a non-empty specification. */
    public function others(string $details = null): static
    {
        return $this->state(fn () => [
            'transaction_type'          => 'Others',
            'transaction_other_details' => $details ?? $this->faker->sentence(5),
        ]);
    }

    /** Force a standard (non-Others) transaction type with null details. */
    public function standardTransaction(string $type = 'SETUP'): static
    {
        return $this->state(fn () => [
            'transaction_type'          => $type,
            'transaction_other_details' => null,
        ]);
    }

    /** Place the date_visited within today. */
    public function visitedToday(): static
    {
        return $this->state(fn () => [
            'date_visited' => now(),
        ]);
    }

    /** Place the date_visited in the current calendar month. */
    public function visitedThisMonth(): static
    {
        return $this->state(fn () => [
            'date_visited' => $this->faker->dateTimeBetween(
                now()->startOfMonth(),
                now()->endOfMonth()
            ),
        ]);
    }

    /** Place the date_visited in the previous month. */
    public function visitedLastMonth(): static
    {
        return $this->state(fn () => [
            'date_visited' => $this->faker->dateTimeBetween(
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ),
        ]);
    }

    /** Force a specific gender. */
    public function male(): static
    {
        return $this->state(fn () => ['gender' => 'Male']);
    }

    public function female(): static
    {
        return $this->state(fn () => ['gender' => 'Female']);
    }
}
