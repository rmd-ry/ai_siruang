<?php

namespace App\Filament\Resources\Reservasis\Schemas;

use App\Models\Reservasi;
use App\StatusReservasi;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservasiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi Reservasi')
                    ->columns(2)
                    ->schema([

                        Placeholder::make('peminjam')
                            ->label('Peminjam')
                            ->content(fn($record) => $record?->user?->nama ?? '-'),

                        Placeholder::make('ruangan')
                            ->label('Ruangan')
                            ->content(fn($record) => $record?->kelas?->nama_kelas ?? '-'),

                        Placeholder::make('tanggal_info')
                            ->label('Tanggal')
                            ->content(
                                fn($record) =>
                                \Carbon\Carbon::parse($record?->tanggal)->format('d M Y')
                            ),

                        Placeholder::make('jam_info')
                            ->label('Jam')
                            ->content(
                                fn($record) =>
                                substr($record?->jam_mulai, 0, 5) . ' - ' . substr($record?->jam_selesai, 0, 5)
                            ),
                        DatePicker::make('tanggal')
                            ->disabled(
                                fn($record) =>
                                $record && $record->status !== StatusReservasi::PENDING
                            ),

                        TimePicker::make('jam_mulai')
                            ->disabled(
                                fn($record) =>
                                $record && $record->status !== StatusReservasi::PENDING
                            ),

                        TimePicker::make('jam_selesai')
                            ->disabled(
                                fn($record) =>
                                $record && $record->status !== StatusReservasi::PENDING
                            ),

                    ]),



                Section::make('Detail Permintaan')
                    ->schema([
                        Textarea::make('alasan')
                            ->required()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
