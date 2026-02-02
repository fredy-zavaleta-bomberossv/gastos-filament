<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'Transporte',
            'Comida',
            'Servicios',
            'Salud',
            'Educación',
            'Otros',
        ];

        foreach ($categorias as $nombre) {
            Categoria::firstOrCreate([
                'nombre' => $nombre,
            ]);
        }
    }
}
