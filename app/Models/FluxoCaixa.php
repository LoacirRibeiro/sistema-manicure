<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FluxoCaixa extends Model
{
    use HasFactory;

    // 🔥 Define o nome correto da tabela se o Laravel tentar procurar no plural automático incorreto
    protected $table = 'fluxo_caixas';

    // 🚀 Campos que podem ser preenchidos em massa no banco
    protected $fillable = [
        'tipo',
        'valor',
        'descricao',
        'agendamento_id',
        'forma_pagamento',
    ];

    // 🔗 Relacionamento: Um registro no caixa pode pertencer a um agendamento
    public function agendamento()
    {
        return $this->belongsTo(Agendamento::class);
    }
}