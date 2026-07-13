<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;


class Bloqueio extends Model
{
    use HasFactory, crudTrait;

    // 🔥 Define o nome correto da tabela
    protected $table = 'bloqueios';

    // 🚀 Libera os campos para gravação no banco via Backpack/Laravel
    protected $fillable = [
        'data',
        'horario_inicio',
        'horario_fim',
        'motivo',
    ];
}