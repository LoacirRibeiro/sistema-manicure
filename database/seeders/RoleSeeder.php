<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Cria as funções no sistema se elas não existirem
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'manicure']);
        Role::firstOrCreate(['name' => 'caixa']);
        Role::firstOrCreate(['name' => 'cliente']);
    }
}