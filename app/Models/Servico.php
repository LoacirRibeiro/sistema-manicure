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








// namespace App\Models;

// use Backpack\CRUD\app\Models\Traits\CrudTrait;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Illuminate\Database\Eloquent\Model;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Str;

// class Servico extends Model
// {
//     use CrudTrait;
//     use HasFactory;

//     // 🔥 Define explicitamente a tabela do banco
//     protected $table = 'servicos';

//     // 🚀 Campos liberados para gravação
//     protected $fillable = [
//         'nome',
//         'descricao',
//         'preco',
//         'duracao_minutos',
//         'foto_exemplo',
//     ];

//     // 🔗 Relacionamento: Um serviço pode ter vários agendamentos
//     public function agendamentos()
//     {
//         return $this->hasMany(Agendamento::class);
//     }

//     public function setFotoExemploAttribute($value)
//     {
//         $attribute_name = "foto_exemplo";
//         $disk = "public"; // disco público configurado
//         $destination_path = "servicos"; // pasta de destino dentro do disco

//         // Se o usuário limpou a imagem no formulário
//         if ($value == null) {
//             // Deleta o arquivo antigo do servidor
//             Storage::disk($disk)->delete($this->{$attribute_name});
//             $this->attributes[$attribute_name] = null;
//         }

//         // Se um novo arquivo foi enviado via requisição
//         if (request()->hasFile($attribute_name)) {
//             // Deleta o arquivo antigo
//             Storage::disk($disk)->delete($this->{$attribute_name});
            
//             // Salva o novo arquivo
//             $this->attributes[$attribute_name] = request()->file($attribute_name)->store($destination_path, $disk);
//         }
//     }
// }