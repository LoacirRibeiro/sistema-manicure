<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Executa os seeders base primeiro
        $this->call([
            ServicoSeeder::class,
            RoleSeeder::class,     // Cria as roles: admin, manicure, etc.
            HorarioSeeder::class,  // Cria a grade de horários
            ServicoSeeder::class, 
        ]);

        // 2. Cria o usuário administrador se ele não existir
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'], // Coluna única de verificação
            [
                'name' => 'Admin NailsStudio',
                'telefone' => '(00) 99999-0000',
                'password' => Hash::make('senha123'),
            ]
        );

        // Atribui a role de admin (apenas se ele não a tiver ainda)
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // 3. Cria a manicure se ela não existir
        $manicure = User::firstOrCreate(
            ['email' => 'marcirlle@salao.com'], // Coluna única de verificação
            [
                'name' => 'Marcielle Paiva',
                'telefone' => '(63) 99218-5324',
                'password' => Hash::make('senha123'),
            ]
        );

        // Atribui a role de manicure (apenas se ela não a tiver ainda)
        if (!$manicure->hasRole('manicure')) {
            $manicure->assignRole('manicure');
        }
    }
}