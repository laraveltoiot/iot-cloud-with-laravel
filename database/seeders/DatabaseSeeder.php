<?php declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SketchSeeder::class,
            ThingSeeder::class,
            VariableSeeder::class,
            TriggerSeeder::class,
            DashboardSeeder::class,
        ]);
    }
}
