<?php

namespace App\Filament\Pages;

use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;

class AvailabilityPage extends Page implements HasForms
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static string $view = 'filament.pages.availability-page';

    protected static ?string $title = 'Availability Checker';
    protected static ?string $navigationGroup = 'Operations'; // groups under "Operations"

    public ?string $starts_at = null;
    public ?string $ends_at = null;

    public $availableDrivers;
    public $availableVehicles;

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DateTimePicker::make('starts_at')
                ->label('Start Time')
                ->required(),

            Forms\Components\DateTimePicker::make('ends_at')
                ->label('End Time')
                ->required(),

            Forms\Components\Actions::make([
                Forms\Components\Actions\Action::make('check')
                    ->label('Check Availability')
                    ->action('checkAvailability'),
            ]),
        ]);
    }

    public function checkAvailability(): void
    {
        $start = Carbon::parse($this->starts_at);
        $end   = Carbon::parse($this->ends_at);

        $this->availableDrivers = Driver::with('company')->whereDoesntHave('trips', function ($q) use ($start, $end) {
            $q->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start);
            })->get();

        $this->availableVehicles = Vehicle::with('company')->whereDoesntHave('trips', function ($q) use ($start, $end) {
            $q->where('starts_at', '<', $end)
                ->where('ends_at', '>', $start);
        })->get();
    }
}
