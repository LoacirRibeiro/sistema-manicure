<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_nome',
        'cliente_whatsapp',
        'servico_id',
        'manicure_id',     
        'data_escolhida',   
        'hora_escolhida',   
        'status',
        'forma_pagamento',
        'observacoes',
        'is_remarcado',
        'user_id',
    ];

    public function servico()
    {
        return $this->belongsTo(Servico::class);
    }
    public function manicure()
    {
        return $this->belongsTo(User::class, 'manicure_id');
    }
}