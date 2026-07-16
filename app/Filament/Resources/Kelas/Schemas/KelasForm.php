<?php

namespace App\Filament\Resources\Kelas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class KelasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id_lantai')
                    ->required()
                    ->numeric(),
                TextInput::make('nama_kelas')
                    ->required(),
                TextInput::make('kapasitas')
                    ->required()
                    ->numeric(),
            ]);
    }
}
