<?php declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Bogdan',
            'email' => 'bogdygewald@yahoo.de',
            'password' => bcrypt('password'),
        ]);
    }
}
