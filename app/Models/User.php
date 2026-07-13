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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed', // 🔥 Isto basta! Criptografa strings limpas automaticamente.
        ];
    }

    /**
     * Permissão de acesso exigida pelo Backpack
     */
    public function canAccessBackpack(): bool
    {
        return $this->hasRole('admin');
    }
}