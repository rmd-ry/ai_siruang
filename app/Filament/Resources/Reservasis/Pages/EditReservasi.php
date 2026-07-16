<?php

namespace App\Filament\Resources\Reservasis\Pages;

use App\Filament\Resources\Reservasis\ReservasiResource;
use App\Models\Reservasi;
use App\StatusReservasi;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Notifications\Notification;

class EditReservasi extends EditRecord
{
    protected static string $resource = ReservasiResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [

            Actions\Action::make('approve')
                ->label('Terima')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status === StatusReservasi::PENDING)
                ->action(function () {

                    $this->record->update([
                        'status' => StatusReservasi::DITERIMA,
                    ]);

                    Notification::make()
                        ->title('Reservasi Disetujui')
                        ->success()
                        ->send();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            Actions\Action::make('reject')
                ->label('Tolak')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->visible(fn() => $this->record->status === StatusReservasi::PENDING)
                ->action(function () {

                    $this->record->update([
                        'status' => StatusReservasi::DITOLAK,
                    ]);

                    Notification::make()
                        ->title('Reservasi Ditolak')
                        ->danger()
                        ->send();

                    $this->refreshFormData([
                        'status',
                    ]);
                }),

            Actions\DeleteAction::make(),
        ];
    }
}
