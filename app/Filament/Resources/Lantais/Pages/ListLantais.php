<?php

namespace App\Filament\Resources\Lantais\Pages;

use App\Filament\Resources\Lantais\LantaiResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLantais extends ListRecords
{
    protected static string $resource = LantaiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
