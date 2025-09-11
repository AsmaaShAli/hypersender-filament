<?php

use App\Models\Company;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Rules\TripDurationRule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('rejects trips shorter than minimum duration', function () {
    $company = Company::factory()->create();
    $driver = Driver::factory()->create(['company_id' => $company->id]);
    $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

    $this->expectException(ValidationException::class);

    $data = [
        'company_id' => $company->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'starts_at' => Carbon::parse('2025-01-01 10:00'),
        'ends_at'   => Carbon::parse('2025-01-01 10:10'), // too short
    ];

    $rules = [
        'starts_at' => ['required','date'],
        'ends_at'   => ['required','date', new TripDurationRule($data['starts_at'], $data['ends_at']
        )],
    ];

    Validator::make($data, $rules)->validate();

});

it('rejects trips longer than maximum duration', function () {
    $company = Company::factory()->create();
    $driver = Driver::factory()->create(['company_id' => $company->id]);
    $vehicle = Vehicle::factory()->create(['company_id' => $company->id]);

    $this->expectException(ValidationException::class);

    $data = [
        'company_id' => $company->id,
        'driver_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'starts_at' => Carbon::parse('2025-01-01 10:00'),
        'ends_at'   => Carbon::parse('2025-01-01 23:10'), // too long
    ];

    $rules = [
        'starts_at' => ['required','date'],
        'ends_at'   => ['required','date', new TripDurationRule($data['starts_at'], $data['ends_at']
        )],
    ];

    Validator::make($data, $rules)->validate();
});
