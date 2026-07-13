<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FotoPortfolio extends Model
{
    use CrudTrait;
    use HasFactory;

    // Define explicitamente a tabela caso use o padrão em português
    protected $table = 'fotos_portfolio';

    protected $fillable = [
        'caminho_foto',
        'titulo',
        'legenda',
        'manicure_id',
    ];

// Dentro da classe FotoPortfolio:

    /**
 * Mutator para upload do Backpack com proteção contra valores nulos
 */
    public function setCaminhoFotoAttribute($value)
    {
        $attribute_name = "caminho_foto";
        $disk = "public";
        $destination_path = "portfolio"; 

        // Se o valor for limpo e já existia um arquivo, deleta
        if ($value == null) {
            if (!empty($this->{$attribute_name})) {
                \Illuminate\Support\Facades\Storage::disk($disk)->delete($this->{$attribute_name});
            }
            $this->attributes[$attribute_name] = null;
        }

        // Se receber um novo arquivo enviado pelo Request
        if (request()->hasFile($attribute_name)) {
            // CORREÇÃO: Só deleta o antigo se ele realmente existir (evita o erro no Create)
            if (!empty($this->{$attribute_name})) {
                \Illuminate\Support\Facades\Storage::disk($disk)->delete($this->{$attribute_name});
            }
            
            // Salva o novo arquivo
            $path = request()->file($attribute_name)->store($destination_path, $disk);
            
            $this->attributes[$attribute_name] = $path;
        }
    }

    /**
     * Retorna a profissional (User) que executou o trabalho da foto
     */
    public function manicure(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manicure_id');
    }
}