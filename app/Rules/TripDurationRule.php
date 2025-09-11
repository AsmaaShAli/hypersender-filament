<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class TripDurationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // min trip is 30 mins, max trip is 12 hours = 720 mins
        $minMinutes = 30;
        $maxMinutes = 720;

        if (! request()->has('starts_at') || ! request()->has('ends_at')) {
            return;
        }

        $start = \Carbon\Carbon::parse(request()->input('starts_at'));
        $end   = \Carbon\Carbon::parse(request()->input('ends_at'));

        $duration = $start->diffInMinutes($end, false);

        if ($duration < $minMinutes) {
            $fail("Trip duration must be at least {$this->minMinutes} minutes.");
        }

        $maxMinutesInHours = 12;
        if ($duration > $maxMinutes) {
            $fail("Trip duration cannot exceed {$maxMinutesInHours} hours.");
        }

    }
}
