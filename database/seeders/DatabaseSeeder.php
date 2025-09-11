<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        \App\Models\Company::factory(1)
            ->has(\App\Models\Driver::factory(10))
            ->has(\App\Models\Vehicle::factory(5))
            ->create()
            ->each(function ($company) {
                \App\Models\Trip::factory(20)->create([
                    'company_id' => $company->id,
                    'driver_id' => $company->drivers->random()->id,
                    'vehicle_id' => $company->vehicles->random()->id,
                ]);
            });
    }
}
