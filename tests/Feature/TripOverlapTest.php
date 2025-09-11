<?php

use App\Models\Trip;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Company;
use App\Rules\NoOverlapRule;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('prevents overlapping trips for the same driver', function () {
    $company = Company::factory()->create();
    $driver = Driver::factory()->create(['company_id' => $company->id]);
    $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

    // Existing trip
    Trip::factory()->create([
        'company_id' => $company->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'starts_at' => Carbon::parse('2025-01-01 10:00'),
        'ends_at'   => Carbon::parse('2025-01-01 12:00'),
    ]);

    // Overlapping trip Data
    $data = [
        'company_id' => $company->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'starts_at' => Carbon::parse('2025-01-01 11:00'),
        'ends_at'   => Carbon::parse('2025-01-01 13:00'),
    ];

    $this->expectException(ValidationException::class);

    $rules = [ 'ends_at' =>
        [
            new NoOverlapRule(
            $data['starts_at'],
            $data['ends_at'],
            $data['driver_id'],
            $data['vehicle_id'],
        )]
    ];

    Validator::make($data, $rules)->validate(); // should fail validation
});
