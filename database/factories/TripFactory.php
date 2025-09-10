<?php

namespace Database\Factories;

use App\Models\Trip;
use App\TripStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 day', '+1 day');
        $end = (clone $start)->modify('+'.rand(1, 5).' hours');

        return [
            'company_id' => \App\Models\Company::factory(),
            'driver_id' => \App\Models\Driver::factory(),
            'vehicle_id' => \App\Models\Vehicle::factory(),
            'starts_at' => $start,
            'ends_at' => $end,
            'status' => $this->faker->randomElement([
                TripStatus::Scheduled->value,
                TripStatus::Active->value,
                TripStatus::Completed->value,
            ]),
        ];
    }
}
