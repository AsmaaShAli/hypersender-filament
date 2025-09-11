<?php

namespace App\Observers;

use App\Models\Driver;
use App\Services\StatsService;

class DriverObserver
{
    public function created(Driver $driver): void
    {
        StatsService::flushCompanyLists($driver->company_id);
    }
    public function saved(Driver $driver): void
    {
        StatsService::flushCompanyLists($driver->company_id);
    }
    public function deleted(Driver $driver): void
    {
        StatsService::flushCompanyLists($driver->company_id);
    }
}
