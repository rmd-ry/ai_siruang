<?php

namespace App\Filament\Resources\Lantais;

use App\Filament\Resources\Lantais\Pages\CreateLantai;
use App\Filament\Resources\Lantais\Pages\EditLantai;
use App\Filament\Resources\Lantais\Pages\ListLantais;
use App\Filament\Resources\Lantais\Schemas\LantaiForm;
use App\Filament\Resources\Lantais\Tables\LantaisTable;
use App\Models\Lantai;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class LantaiResource extends Resource
{
    protected static ?string $model = Lantai::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama_lantai';
    protected static ?string $navigationLabel = 'Manajemen Lantai';

    public static function form(Schema $schema): Schema
    {
        return LantaiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LantaisTable::configure($table);
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
            'index' => ListLantais::route('/'),
            'create' => CreateLantai::route('/create'),
            'edit' => EditLantai::route('/{record}/edit'),
        ];
    }
}
