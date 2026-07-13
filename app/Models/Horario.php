<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    // Define explicitamente o nome da tabela no banco de dados
    protected $table = 'horarios';

    // Campos que podem ser preenchidos em massa (ex: no seeder ou formulários)
    protected $fillable = [
        'hora',
        'dia_tipo', // Ex: 'semana', 'sabado'
    ];

    // Garante que o Laravel entenda o campo 'hora' como um objeto de tempo manipulável
    protected $casts = [
        'hora' => 'datetime:H:i',
    ];
}