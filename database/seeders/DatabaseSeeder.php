<?php

namespace Database\Seeders;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Vehicle;
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
                foreach (range(1, 25) as $i) {
                    $driver  = Driver::inRandomOrder()->where('company_id', $company->id)->first();
                    $vehicle = Vehicle::inRandomOrder()->where('company_id', $company->id)->first();

                    $start = now()->addDays(rand(0, 7))->setTime(rand(6, 20), [0, 30][rand(0,1)]);
                    $durationMinutes = rand(30, 12 * 60);
                    $end   = (clone $start)->addMinutes($durationMinutes);

                    // Ensure no overlap for same driver/vehicle
                    $overlap = Trip::where('company_id', $company->id)
                        ->where(function ($q) use ($driver, $vehicle) {
                            $q->where('driver_id', $driver->id)
                                ->orWhere('vehicle_id', $vehicle->id);
                        })
                        ->where('starts_at', '<', $end)
                        ->where('ends_at', '>', $start)
                        ->exists();

                    if (! $overlap) {
                        Trip::create([
                            'company_id' => $company->id,
                            'driver_id'  => $driver->id,
                            'vehicle_id' => $vehicle->id,
                            'starts_at'  => $start,
                            'ends_at'    => $end,
                        ]);
                    }
                }
            });
    }
}
