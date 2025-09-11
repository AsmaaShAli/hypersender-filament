<?php

namespace App\Rules;

use App\Models\Trip;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Route;

class NoOverlapRule implements ValidationRule
{
    protected $driverId;
    protected $vehicleId;
    protected $start;
    protected $end;
    protected $tripId;


    public function __construct($start, $end, $driverId, $vehicleId, $tripId = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->driverId = $driverId;
        $this->vehicleId = $vehicleId;
        $this->tripId = $tripId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start     = $this->start;
        $end       = $this->end;
        $driverId  = $this->driverId;
        $vehicleId = $this->vehicleId;
        $tripId    = $this->tripId; // editing case

        if (! $start || ! $end || ! $driverId || ! $vehicleId) {
            return;
        }

        $start = Carbon::parse($start);
        $end   = Carbon::parse($end);

        $conflict = Trip::query()
            ->when($tripId, fn ($q) => $q->where('id', '!=', $tripId))
            ->where(function ($q) use ($driverId, $vehicleId) {
                $q->where('driver_id', $driverId)
                    ->orWhere('vehicle_id', $vehicleId);
            })
            ->where('starts_at', '<', $end) // if the new trip is happening before than the existing one
            ->where('ends_at', '>', $start) // if the new trip is happening after the existing one
            ->whereIn('status',['active','scheduled'])
            ->exists();

        if ($conflict) {
            $fail('This driver or vehicle already has a trip during this time frame.');
        }
    }
}
