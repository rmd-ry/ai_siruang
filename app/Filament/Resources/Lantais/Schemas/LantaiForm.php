<?php

namespace App\Filament\Resources\Lantais\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LantaiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama_lantai')
                    ->required(),
            ]);
    }
}
