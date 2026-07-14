<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servico extends Model
{
    use HasFactory;

    // Removemos o fillable e liberamos todos os campos para gravação sem travas
    protected $guarded = []; 
}