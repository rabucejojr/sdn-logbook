<?php

namespace Database\Seeders;

use App\Models\ClientLog;
use Illuminate\Database\Seeder;

/**
 * Generates a large dataset for performance and load testing.
 *
 * Usage:
 *   php artisan db:seed --class=ClientLogSeeder          # 1 000 records (default)
 *   php artisan db:seed --class=ClientLogSeeder --count=5000
 *
 * Note: Does NOT use WithoutModelEvents so that Eloquent's timestamps
 * (created_at / updated_at) are set — matching real production data.
 */
class ClientLogSeeder extends Seeder
{
    public function run(): void
    {
        $count = (int) ($this->command->option('count') ?? 1000);

        $this->command->info("Seeding {$count} client log records…");

        $bar = $this->command->getOutput()->createProgressBar($count);
        $bar->start();

        // Seed in chunks of 200 to avoid memory pressure
        $chunks = (int) ceil($count / 200);

        for ($i = 0; $i < $chunks; $i++) {
            $batchSize = min(200, $count - $i * 200);

            ClientLog::factory()->count($batchSize)->create();

            $bar->advance($batchSize);
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info("Done. Total records: " . ClientLog::count());
    }
}
