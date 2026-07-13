<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use App\Http\Requests\ServicoRequest;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ServicoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Servico::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/servico');
        CRUD::setEntityNameStrings('serviço', 'serviços');
    }

    /**
     * Define o que aparece na TABELA de listagem
     */
    protected function setupListOperation()
    {
        CRUD::column('foto_exemplo')
            ->type('image')
            ->label('Foto')
            ->prefix('storage/') // Garante que lê da pasta pública de uploads
            ->height('50px')
            ->width('50px');

        CRUD::column('nome')->type('text')->label('Nome do Serviço');
        
        CRUD::column('preco')
            ->type('number')
            ->label('Preço')
            ->prefix('R$ ')
            ->decimals(2)
            ->dec_point(',')
            ->thousands_sep('.');

        CRUD::column('duracao_minutos')->type('number')->label('Duração (Min)');
    }

    /**
     * Define o que aparece no FORMULÁRIO (Criar e Editar)
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(ServicoRequest::class);

        CRUD::field('nome')->type('text')->label('Nome do Serviço');
        
        CRUD::field('descricao')->type('textarea')->label('Breve Descrição');

        CRUD::field('preco')
            ->type('number')
            ->label('Preço (R$)')
            ->attributes(["step" => "0.01"]);

        CRUD::field('duracao_minutos')
            ->type('number')
            ->label('Duração (Em minutos)')
            ->default(60);

        // 🔥 Campo do Backpack para Upload de Imagens limpo e nativo
        CRUD::field('foto_exemplo')
            ->type('upload')
            ->label('Foto do Procedimento')
            ->upload(true)
            ->disk('public'); // Salva em storage/app/public/
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}