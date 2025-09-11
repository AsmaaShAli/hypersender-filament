<?php

namespace App\Services;


use App\Enums\TripStatus;
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
                ->get()->count()
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
                ->get()->count()
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
                    ->orWhere('ends_at', '>=', now())
                    ->active()
                    ->count(),

                'available_drivers' => Driver::whereDoesntHave('trips', function ($q) {
                    $q->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now())
                        ->whereIn('status',TripStatus::Finished());
                })->count(),

                'available_vehicles' => Vehicle::whereDoesntHave('trips', function ($q) {
                    $q->where('starts_at', '<=', now())
                        ->where('ends_at', '>=', now())
                        ->whereIn('status',TripStatus::Finished());
                })->count(),

                'completed_trips_month' => Trip::whereBetween('ends_at', [
                    now()->startOfMonth(),
                    now()->endOfMonth()
                ])
                    ->where('status','completed')
                    ->count(),
            ]
        );
    }

    public static function companyDrivers(int $companyId): array
    {
        return Cache::remember(
            "company:{$companyId}:drivers_list",
            now()->addMinutes(30),
            fn () => Driver::where('company_id', $companyId)
                ->pluck('name', 'id')
                ->toArray()
        );
    }

    public static function companyVehicles(int $companyId): array
    {
        return Cache::remember(
            "company:{$companyId}:vehicles_list",
            now()->addMinutes(30),
            fn () => Vehicle::where('company_id', $companyId)
                ->pluck('plate_number', 'id')
                ->toArray()
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

    public static function flushCompanyLists(int $companyId): void
    {
        Cache::forget("company:{$companyId}:drivers_list");
        Cache::forget("company:{$companyId}:vehicles_list");
    }

}
