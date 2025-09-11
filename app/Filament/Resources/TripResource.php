<?php

namespace App\Filament\Resources;

use App\Enums\TripStatus;
use App\Filament\Resources\TripResource\Pages;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Rules\NoOverlapRule;
use App\Rules\TripDurationRule;
use App\Services\StatsService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TripResource extends Resource
{

    protected static ?string $model = Trip::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Operations';

    public static function form(Form $form): Form
    {
        return $form->schema([
        Forms\Components\Select::make('company_id')
            ->relationship('company', 'name')
            ->required()
            ->reactive(),

        Forms\Components\Select::make('driver_id')
            ->label('Driver')
            ->searchable()
            ->options(fn ($get) => $get('company_id')
                ? StatsService::companyDrivers($get('company_id'))
                : []
            )
            ->required()
            ->reactive(),

        Forms\Components\Select::make('vehicle_id')
            ->label('Vehicle')
            ->searchable()
            ->options(fn ($get) => $get('company_id')
                ? StatsService::companyVehicles($get('company_id'))
                : []
            )
            ->required()
            ->reactive(),

        Forms\Components\DateTimePicker::make('starts_at')
            ->required()
            ->reactive(),

        Forms\Components\DateTimePicker::make('ends_at')
            ->required()
            ->after('starts_at')
            ->rules([
                function ($get) {
                    return new TripDurationRule($get('starts_at'));
                }, function ($get,$record) {
                    return new NoOverlapRule(
                        $get('starts_at'),
                        $get('ends_at'),
                        $get('driver_id'),
                        $get('vehicle_id'),
                        $record?->id ); // for edit mode
                }
            ]),
        Forms\Components\Select::make('status')
            ->options(TripStatus::options())
            ->default(TripStatus::Scheduled->value)
            ->required(),
    ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company.name')->badge()->sortable(),
                Tables\Columns\TextColumn::make('driver.name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('vehicle.plate_number')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('starts_at')->dateTime()->sortable(),
                Tables\Columns\TextColumn::make('ends_at')->dateTime()->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'scheduled',
                        'warning' => 'active',
                        'success' => 'completed',
                        'danger'  => 'cancelled',
                    ])->sortable()
            ])
            ->defaultSort('starts_at', 'desc')
            ->filters([
                Tables\Filters\Filter::make('active_now')
                    ->label('Active Now')
                    ->query(fn ($query) => $query
                        ->where('starts_at', '<=', now())
                        ->where('ends_at', '>', now())
                        ->whereNotIn('status', TripStatus::Finished())
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['driver', 'vehicle', 'company']);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
