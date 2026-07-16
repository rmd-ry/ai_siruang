<?php

namespace App\Filament\Resources\Reservasis;

use Filament\Forms\Components\Select;
use Filament\Notifications\Notification; // Buat notifikasi pojok kanan
use App\Filament\Resources\Reservasis\Pages\CreateReservasi;
use App\Filament\Resources\Reservasis\Pages\EditReservasi;
use App\Filament\Resources\Reservasis\Pages\ListReservasis;
use App\Filament\Resources\Reservasis\Schemas\ReservasiForm;
use App\Filament\Resources\Reservasis\Tables\ReservasisTable;
use App\Models\Reservasi;
use App\StatusReservasi;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ReservasiResource extends Resource
{
    protected static ?string $model = Reservasi::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'alasan';
    protected static ?string $navigationLabel = 'Manajemen Reservasi';

    public static function form(Schema $schema): Schema
    {
        return ReservasiForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.nama')
                    ->label('Peminjam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('kelas.nama_kelas')
                    ->label('Ruangan')
                    ->searchable(),

                TextColumn::make('tanggal')
                    ->date('d M Y')
                    ->description(
                        fn(Reservasi $record) =>
                        substr($record->jam_mulai, 0, 5)
                            . ' - ' .
                            substr($record->jam_selesai, 0, 5)
                    )
                    ->sortable(),

                TextColumn::make('alasan')
                    ->limit(30)
                    ->tooltip(fn(Reservasi $record) => $record->alasan),



                TextColumn::make('status')
                    ->badge()
                    ->color(fn(StatusReservasi $state) => match ($state) {
                        StatusReservasi::DITERIMA => 'success',
                        StatusReservasi::DITOLAK  => 'danger',
                        StatusReservasi::PENDING  => 'warning',
                    }),



            ])
            ->defaultSort('created_at', 'desc')
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
            'index' => ListReservasis::route('/'),
            'create' => CreateReservasi::route('/create'),
            'edit' => EditReservasi::route('/{record}/edit'),
        ];
    }
}
