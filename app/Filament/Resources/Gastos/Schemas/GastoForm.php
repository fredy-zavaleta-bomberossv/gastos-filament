<?php

namespace App\Filament\Resources\Gastos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GastoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('descripcion')
                ->label('Descripción')
                ->required(),

            TextInput::make('monto')
                ->label('Monto')
                ->numeric()
                ->required(),

            DatePicker::make('fecha')
                ->label('Fecha')
                ->default(now())
                ->required(),

            Select::make('categoria_id')
                ->label('Categoría')
                ->relationship('categoria', 'nombre')
                ->required(), // 🔴 OBLIGATORIO
        ]);
    }
}
