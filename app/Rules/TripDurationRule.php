<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TripDurationRule implements ValidationRule
{
    private $ends_at;
    private $starts_at;

    public function __construct($starts_at)
    {
        $this->starts_at = $starts_at;
    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // min trip is 30 mins, max trip is 12 hours = 720 mins
        $minMinutes = 30;
        $maxMinutes = 720;

        $this->ends_at = $value;

        if (! $this->starts_at || ! $this->ends_at) {
            return;
        }

        $start = \Carbon\Carbon::parse($this->starts_at);
        $end   = \Carbon\Carbon::parse($this->ends_at);

        $duration = $start->diffInMinutes($end, false);

        if ($duration < $minMinutes) {
            $fail("Trip duration must be at least {$minMinutes} minutes.");
        }

        $maxMinutesInHours = 12;
        if ($duration > $maxMinutes) {
            $fail("Trip duration cannot exceed {$maxMinutesInHours} hours.");
        }

    }
}
