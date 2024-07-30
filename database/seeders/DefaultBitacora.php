<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Bitacora as ModelsBitacora;

class DefaultBitacora extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsBitacora::create([
            'total_completados' => 0,
            'total_fallidos' => 0,
            'descripcion' => 'Carga de clientes completa. 0 clientes tuvieron efecto.',
            'tipo' => 'Cliente',
            'estado' => true,
            'codes' => null,
            'created_at' => date("Y-m-d")." 00:00:00",
            'updated_at' => date("Y-m-d")." 00:00:00"
        ]);

        ModelsBitacora::create([
            'total_completados' => 0,
            'total_fallidos' => 0,
            'descripcion' => 'Carga de creditos completa. 0 creditos tuvieron efecto.',
            'tipo' => 'Credito',
            'estado' => true,
            'codes' => null,
            'created_at' => date("Y-m-d")." 00:00:00",
            'updated_at' => date("Y-m-d")." 00:00:00"
        ]);

        ModelsBitacora::create([
            'total_completados' => 0,
            'total_fallidos' => 0,
            'descripcion' => 'Carga de movimientos completa. 0 movimientos tuvieron efecto.',
            'tipo' => 'Movimiento',
            'estado' => true,
            'codes' => null,
            'created_at' => date("Y-m-d")." 00:00:00",
            'updated_at' => date("Y-m-d")." 00:00:00"
        ]);

    }
}
