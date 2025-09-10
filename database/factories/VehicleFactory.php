<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'company_id'   => Company::factory(),
            'plate_number' => strtoupper($this->faker->unique()->bothify('???-####')),             // 3 letters - 4 digits (uppercase) to mimic a plate number
            'model'        => $this->faker->company . ' ' . $this->faker->randomElement(['XL','S','2020','Pro','SE']),
            'created_at'   => now(),
            'updated_at'   => now(),
        ];
    }
}
