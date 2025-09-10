<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Management';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('company_id')
                ->relationship('company', 'name')
                ->required()
                ->label('Company'),

            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255)
                ->label('Driver Name'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('company.name')
                ->sortable()
                ->searchable()
                ->label('Company'),

            Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->label('Driver Name'),

            Tables\Columns\TextColumn::make('vehicles_count')
                ->label('Vehicles')
                ->getStateUsing(fn ($record) =>
                    $record->vehicles->count()
                ),

          /*  Tables\Columns\TextColumn::make('vehicles_count')
                ->label('Vehicles')
                ->getStateUsing(fn ($record) =>
                $record->vehicles ? $record->vehicles->pluck('id')->unique()->count() : 0
                ),
          */

            Tables\Columns\TextColumn::make('trips_count')
                ->counts('trips')
                ->label('Trips'),
        ])
            ->defaultSort('name')
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
            ->with(['trips','vehicles']); // eager load trips and vehicles
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
        ];
    }
}
