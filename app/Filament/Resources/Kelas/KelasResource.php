<?php

namespace App\Filament\Resources\Kelas;

use App\Filament\Resources\Kelas\Pages;
use App\Filament\Resources\Kelas\Pages\CreateKelas;
use App\Filament\Resources\Kelas\Pages\EditKelas;
use App\Filament\Resources\Kelas\Pages\ListKelas;
use App\Filament\Resources\Kelas\Schemas\KelasForm;
use App\Filament\Resources\Kelas\Tables\KelasTable;
use App\Models\Kelas;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class KelasResource extends Resource
{
    protected static ?string $model = Kelas::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    protected static ?string $recordTitleAttribute = 'nama_kelas';
    protected static ?string $navigationLabel = 'Manajemen Kelas';


    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Input Nama Kelas
                Forms\Components\TextInput::make('nama_kelas')
                    ->required()
                    ->maxLength(255),

                // Dropdown Lantai (Relasi)
                Forms\Components\Select::make('id_lantai')
                    ->label('Lantai')
                    ->relationship('lantai', 'nama_lantai')
                    ->required()
                    ->preload()
                    ->searchable(),

                // Kapasitas (Opsional)
                Forms\Components\TextInput::make('kapasitas')
                    ->numeric()
                    ->default(30),
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_kelas')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('lantai.nama_lantai')
                    ->label('Lantai')
                    ->sortable(),

                TextColumn::make('kapasitas')
                    ->numeric(),
            ])
            ->recordUrl(fn($record) => static::getUrl('edit', ['record' => $record]));
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
            'index' => Pages\ListKelas::route('/'),
            'create' => Pages\CreateKelas::route('/create'),
            'edit' => Pages\EditKelas::route('/{record}/edit'),
        ];
    }
}
