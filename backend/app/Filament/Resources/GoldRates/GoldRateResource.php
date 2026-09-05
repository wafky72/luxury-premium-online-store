<?php

namespace App\Filament\Resources\GoldRates;

use App\Filament\Resources\GoldRates\Pages\CreateGoldRate;
use App\Filament\Resources\GoldRates\Pages\EditGoldRate;
use App\Filament\Resources\GoldRates\Pages\ListGoldRates;
use App\Filament\Resources\GoldRates\Schemas\GoldRateForm;
use App\Filament\Resources\GoldRates\Tables\GoldRatesTable;
use App\Models\GoldRate;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GoldRateResource extends Resource
{
    protected static ?string $model = GoldRate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';
    protected static string|UnitEnum|null $navigationGroup = 'Business Logic';

    public static function form(Schema $schema): Schema
    {
        return GoldRateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GoldRatesTable::configure($table);
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
            'index' => ListGoldRates::route('/'),
            'create' => CreateGoldRate::route('/create'),
            'edit' => EditGoldRate::route('/{record}/edit'),
        ];
    }
}
