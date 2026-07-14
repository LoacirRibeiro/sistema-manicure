<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
        ]);

        // 2. Cria o usuário administrador do salão
        $admin = User::factory()->create([
            'name' => 'Admin NailsStudio',
            'email' => 'admin@admin.com',
            'telefone' => '(00) 99999-0000',
            'password' => 'senha123', // Define uma senha padrão para o primeiro acesso
        ]);

        // 3. Atribui a role de admin do Spatie/Backpack para este usuário
        $admin->assignRole('admin');

        // 4. Cria a manicure profissional (Marcielle)
        $manicure = User::factory()->create([
            'name' => 'Marcielle Paiva',
            'email' => 'marcielle@salao.com',
            'telefone' => '(63) 99218-5324',
            'password' => 'senha123', // Altere para a senha que preferir acessar o painel
        ]);

        // 5. Atribui a role de 'manicure' para ela (criada previamente no seu RoleSeeder)
        $manicure->assignRole('manicure');
    
    }
}