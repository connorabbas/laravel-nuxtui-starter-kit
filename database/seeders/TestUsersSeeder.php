<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()
            ->count(100)
            ->has(
                Account::factory()->count(fake()->numberBetween(1, 3)),
                'accounts'
            )
            ->create();
    }
}
