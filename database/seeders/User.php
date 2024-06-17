<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User as ModelsUser;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsUser::create([
            'name' => 'Super Administrador',
            'email' => 'super@admin.com',
            'password' => bcrypt('super'),
            'role' => 'super',
            'created_at' => date("Y-m-d H:i:s")
        ]);

        ModelsUser::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'remember_token' => null,
            'role' => 'admin',
            'created_at' => date("Y-m-d H:i:s")
        ]);
    }
}
