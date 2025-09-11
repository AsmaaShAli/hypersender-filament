<x-filament::page>
    <div class="mb-6">
        {{ $this->form }}
    </div>

    @if($availableDrivers || $availableVehicles)
        <div class="grid grid-cols-2 gap-6">
            <div>
                <h3 class="text-lg font-bold mb-2">Available Drivers</h3>
                <ul class="list-disc list-inside">
                    @forelse($availableDrivers as $driver)
                        <tr>
                            <td>{{ $driver->company->name ?? '—' }}</td> ==>
                            <td>{{ $driver->name }}</td>
                            <br>
                        </tr>
                        @empty
                        <li class="text-gray-500">No drivers available</li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-bold mb-2">Available Vehicles</h3>
                <ul class="list-disc list-inside">
                    @forelse($availableVehicles as $vehicle)
                        <tr>
                            <td>{{ $vehicle->company->name ?? '—'  }}</td> ==>
                            <td>{{ $vehicle->plate_number }}</td>
                            <br>
                        </tr>
                    @empty
                        <li class="text-gray-500">No vehicles available</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endif
</x-filament::page>
