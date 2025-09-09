<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TripResource\Pages;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Rules\NoOverlap;
use App\TripStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TripResource extends Resource
{
    protected static ?string $model = Trip::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
            ->options(fn ($get) =>
            $get('company_id')
                ? Driver::where('company_id', $get('company_id'))->pluck('name', 'id')
                : []
            )
            ->required()
            ->reactive(),

        Forms\Components\Select::make('vehicle_id')
            ->label('Vehicle')
            ->searchable()
            ->options(fn ($get) =>
            $get('company_id')
                ? Vehicle::where('company_id', $get('company_id'))->pluck('plate_number', 'id')
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
            ->rules([new NoOverlap()]),

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
                    ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrips::route('/'),
            'create' => Pages\CreateTrip::route('/create'),
            'edit' => Pages\EditTrip::route('/{record}/edit'),
        ];
    }
}
