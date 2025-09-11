<?php

namespace App\Observers;

use App\Models\Trip;
use App\Services\StatsService;
use Illuminate\Support\Facades\Cache;
class TripObserver
{
    public function created(Trip $trip): void
    {
        $this->flush($trip);
    }
    public function saved(Trip $trip): void
    {
        $this->flush($trip);
    }

    public function deleted(Trip $trip): void
    {
        $this->flush($trip);
    }

    private function flush(Trip $trip)
    {
        StatsService::flushDriver($trip->driver_id);
        StatsService::flushVehicle($trip->vehicle_id);
        StatsService::flushCompany($trip->company_id);
        StatsService::flushDashboard();
    }

}
