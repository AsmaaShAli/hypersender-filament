@php
    $stats = \App\Services\StatsService::dashboard();
@endphp

<div class="flex items-center gap-4">
    <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">
        🚚 Active Trips: {{ $stats['active_trips'] }}
    </span>

    <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">
        👨‍ Drivers Available: {{ $stats['available_drivers'] }}
    </span>

    <span class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-sm">
        🚗 Vehicles Available: {{ $stats['available_vehicles'] }}
    </span>

    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm">
        📊 Completed Trips (This Month): {{ $stats['completed_trips_month'] }}
    </span>


    {{-- Actions --}}
    <a href="{{ route('filament.admin.pages.availability-page') }}"
       class="px-3 py-1 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700 transition">
        🔍 Availability Checker
    </a>

    <a href="{{ route('filament.admin.resources.trips.create') }}"
       class="px-3 py-1 bg-emerald-600 text-white rounded-md text-sm hover:bg-emerald-700 transition">
        ➕ New Trip
    </a>
</div>
