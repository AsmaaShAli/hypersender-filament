<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VehicleResource\Pages;
use App\Filament\Resources\VehicleResource\RelationManagers;
use App\Models\Vehicle;
use App\Services\StatsService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')
                ->relationship('company', 'name')
                ->required()
                ->label('Company'),

            Forms\Components\TextInput::make('plate_number')
                ->label('Plate Number')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(50),

/*            Forms\Components\Select::make('drivers')
                ->multiple()
                ->relationship('drivers', 'name')
                ->preload()
                ->label('Assigned Drivers'),*/
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('company.name')
                ->sortable()
                ->searchable()
                ->label('Company'),

            Tables\Columns\TextColumn::make('plate_number')
                ->label('Plate Number')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('drivers_count')
                ->label('Drivers')
                ->getStateUsing(fn ($record) =>
                    StatsService::vehicleDriversCount($record->id)
                ),

            Tables\Columns\TextColumn::make('trips_count')
                ->counts('trips')
                ->label('Trips'),
        ])
            ->defaultSort('plate_number')
            ->filters([])
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
            ->with(['trips','drivers']); // eager load drivers
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVehicles::route('/'),
            'create' => Pages\CreateVehicle::route('/create'),
            'edit' => Pages\EditVehicle::route('/{record}/edit'),
        ];
    }
}
