<?php

namespace App\Filament\Widgets;

use App\Models\Trip;
use App\Models\Driver;
use App\Models\Vehicle;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Cache;
class TransportStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            // Active trips right now
            Card::make('Active Trips', Cache::remember('active_trips', 60, fn () =>
            Trip::where('status', 'active')
                ->where('starts_at', '<=', now())
                //->where('ends_at', '>=', now())
                ->count()
            ))
                ->icon('heroicon-o-truck')
                ->color('primary'),

            // Available drivers (drivers not in active trips)
            Card::make('Available Drivers', Cache::remember('available_drivers', 60, fn () =>
            Driver::whereDoesntHave('trips', function ($q) {
                $q->where('status', 'active')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
            })->count()
            ))
                ->icon('heroicon-o-user-group')
                ->color('success'),

            // Available vehicles
            Card::make('Available Vehicles', Cache::remember('available_vehicles', 60, fn () =>
            Vehicle::whereDoesntHave('trips', function ($q) {
                $q->where('status', 'active')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
            })->count()
            ))
                ->icon('heroicon-o-truck')
                ->color('success'),

            // Trips completed this month
            Card::make('Trips Completed (This Month)', Cache::remember('completed_trips', 60, fn () =>
            Trip::where('status', 'completed')
                ->whereMonth('ends_at', now()->month)
                ->whereYear('ends_at', now()->year)
                ->count()
            ))
                ->icon('heroicon-o-check-circle')
                ->color('info'),
        ];
    }
}
