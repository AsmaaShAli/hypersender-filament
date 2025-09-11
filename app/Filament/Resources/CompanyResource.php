<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Services\StatsService;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;


class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Management';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name')->sortable()->searchable(),

            Tables\Columns\TextColumn::make('drivers_count')
                ->label('Drivers')
                ->getStateUsing(fn ($record) =>
                    StatsService::companyStats($record->id)['drivers_count']
                ),

            Tables\Columns\TextColumn::make('vehicles_count')
                ->label('Vehicles')
                ->getStateUsing(fn ($record) =>
                    StatsService::companyStats($record->id)['vehicles_count']
                ),

            Tables\Columns\TextColumn::make('trips_count')
                ->label('Trips')
                ->getStateUsing(fn ($record) =>
                    StatsService::companyStats($record->id)['trips_count']
                ),
        ])
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}
