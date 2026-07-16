<?php

namespace App\Filament\Resources\Lantais\Pages;

use App\Filament\Resources\Lantais\LantaiResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLantai extends EditRecord
{
    protected static string $resource = LantaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
