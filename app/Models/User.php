<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Backpack\CRUD\app\Models\Traits\CrudTrait;

class User extends Authenticatable
{
    use HasFactory, Notifiable, CrudTrait, HasRoles; 

    protected $fillable = [
        'name',
        'email',
        'telefone',
        'password',
        'role', // <-- Adicionado para permitir salvar a role direta no banco
        'data_nascimento',
        'bloqueado_ate',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'bloqueado_ate' => 'datetime',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Sincroniza a coluna 'role' com as Roles do Spatie/Backpack automaticamente
     */
    protected static function booted()
    {
        static::saved(function ($user) {
            // Evita loop infinito ao atualizar o próprio model
            static::withoutEvents(function () use ($user) {
                // Pega a primeira role atribuída via Spatie/Backpack (ex: 'manicure', 'admin')
                $roleName = $user->roles()->first()?->name;

                if ($roleName) {
                    $user->update([
                        'role' => strtolower($roleName)
                    ]);
                }
            });
        });
    }

    /**
     * Permissão de acesso exigida pelo Backpack
     */
    public function canAccessBackpack(): bool
    {
        return $this->hasRole('admin');
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}