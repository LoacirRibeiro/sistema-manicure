<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    use HasFactory;

    // Define explicitamente o nome da tabela caso o plural automático mude
    protected $table = 'configuracaos';

    // Campos permitidos para escrita/atualização
    protected $fillable = [
        'foto_hero',
        'foto_espaco',
    ];
}