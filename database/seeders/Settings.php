<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Settings as ModelsSettings;

class Settings extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsSettings::create([
            'key' => 'hora_inicio',
            'value' => '"06:00:00"',
            'created_at' => date("Y-m-d")." 00:00:00",
            'updated_at' => date("Y-m-d")." 00:00:00"
        ]);

        ModelsSettings::create([
            'key' => 'hora_fin',
            'value' => '"21:00:00"',
            'created_at' => date("Y-m-d")." 00:00:00",
            'updated_at' => date("Y-m-d")." 00:00:00"
        ]);
    }
}
