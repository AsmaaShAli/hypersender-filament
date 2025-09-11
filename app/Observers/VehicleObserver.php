<?php

namespace App\Observers;


use App\Models\Driver;
use App\Models\Vehicle;
use App\Services\StatsService;

class VehicleObserver
{
    public function created(Vehicle $vehicle): void
    {
        StatsService::flushCompanyLists($vehicle->company_id);
    }
    public function saved(Vehicle $vehicle): void
    {
        StatsService::flushCompanyLists($vehicle->company_id);
    }
    public function deleted(Vehicle $vehicle): void
    {
        StatsService::flushCompanyLists($vehicle->company_id);
    }
}
