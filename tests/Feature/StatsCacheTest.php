<?php

use App\Models\Company;
use App\Models\Trip;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('computes KPIs correctly', function () {
    $company = Company::factory()->create();

    // Active trip
    Trip::factory()->create([
        'company_id' => $company->id,
        'starts_at' => now()->subHour(),
        'ends_at' => now()->addHour(),
        'status'    => 'active',
    ]);

    // Completed this month
    Trip::factory()->create([
        'company_id' => $company->id,
        'starts_at' => now()->subDays(5),
        'ends_at' => now()->subDays(4),
        'status'    => 'completed',
    ]);

    $stats = \App\Services\StatsService::dashboard();

    expect($stats['active_trips'])->toBe(1)
        ->and($stats['completed_trips_month'])->toBeGreaterThanOrEqual(1);
});
