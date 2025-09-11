<?php

namespace App\Services;


use App\Models\Company;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Cache;

class StatsService
{
    /**
     * Cache driver-vehicle relationship count
     */
    public static function driverVehiclesCount(int $driverId): int
    {
        return Cache::remember(
            "driver:{$driverId}:vehicles_count",
            now()->addMinutes(10),
            fn () => Driver::findOrFail($driverId)
                ->vehicles()
                ->distinct('vehicles.id')
                ->count()
        );
    }

    /**
     * Cache vehicle-driver relationship count
     */
    public static function vehicleDriversCount(int $vehicleId): int
    {
        return Cache::remember(
            "vehicle:{$vehicleId}:drivers_count",
            now()->addMinutes(10),
            fn () => Vehicle::findOrFail($vehicleId)
                ->drivers()
                ->distinct('drivers.id')
                ->count()
        );
    }

    /**
     * Cache company stats (drivers, vehicles, trips)
     */
    public static function companyStats(int $companyId): array
    {
        return Cache::remember(
            "company:{$companyId}:stats",
            now()->addMinutes(30),
            fn () => [
                'drivers_count'  => Company::findOrFail($companyId)->drivers()->count(),
                'vehicles_count' => Company::findOrFail($companyId)->vehicles()->count(),
                'trips_count'    => Company::findOrFail($companyId)->trips()->count(),
            ]
        );
    }

    /**
     * Cache dashboard KPIs
     */
    public static function dashboard(): array
    {
        return Cache::remember(
            "dashboard:kpis",
            now()->addMinutes(15),
            fn () => [
                'active_trips' => Trip::where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now())
                    ->count(),

                'available_drivers' => Driver::whereDoesntHave('trips', function ($q) {
                    $q->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now());
                })->count(),

                'available_vehicles' => Vehicle::whereDoesntHave('trips', function ($q) {
                    $q->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now());
                })->count(),

                'completed_trips_month' => Trip::whereBetween('ends_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth(),
                ])->count(),
            ]
        );
    }

    /**
     * Flush cache for a driver, vehicle, company, or dashboard
     */
    public static function flushDriver(int $driverId): void
    {
        Cache::forget("driver:{$driverId}:vehicles_count");
    }

    public static function flushVehicle(int $vehicleId): void
    {
        Cache::forget("vehicle:{$vehicleId}:drivers_count");
    }

    public static function flushCompany(int $companyId): void
    {
        Cache::forget("company:{$companyId}:stats");
    }

    public static function flushDashboard(): void
    {
        Cache::forget("dashboard:kpis");
    }

}
