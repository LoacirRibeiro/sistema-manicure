<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\FotoPortfolioRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class FotoPortfolioCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\FotoPortfolio::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/foto-portfolio');
        CRUD::setEntityNameStrings('foto do portfólio', 'galeria do portfólio');
    }

    /**
     * Define o que aparece na listagem (Tabela)
     */
    protected function setupListOperation()
    {
        // Coluna da foto em miniatura
        CRUD::addColumn([
            'name'      => 'caminho_foto',
            'label'     => 'Foto',
            'type'      => 'image',
            'prefix'    => 'storage/', // Garante que lê da pasta pública de storage
            'height'    => '50px',
            'width'     => '50px',
        ]);

        CRUD::addColumn([
            'name'  => 'titulo',
            'label' => 'Título',
            'type'  => 'text',
        ]);

        // Exibe o nome da manicure que realizou o trabalho
        CRUD::addColumn([
            'name'      => 'manicure', 
            'label'     => 'Profissional',
            'type'      => 'relationship',
            'attribute' => 'name', // Coluna no model User
        ]);
    }

    /**
     * Define os campos do formulário (Adicionar / Editar)
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(FotoPortfolioRequest::class);

        // Campo de Upload da Imagem
        CRUD::addField([
            'name'      => 'caminho_foto',
            'label'     => 'Foto do Trabalho',
            'type'      => 'upload',
            'upload'    => true,
            'disk'      => 'public', // Salva no disco 'public' (storage/app/public)
        ]);

        CRUD::addField([
            'name'  => 'titulo',
            'label' => 'Título / Procedimento',
            'type'        => 'select_from_array',
            'options'     => \App\Models\Servico::pluck('nome', 'nome')->toArray(), 
            // O pluck('nome', 'nome') salva o NOME do serviço diretamente na coluna 'titulo'
            'allows_null' => false,
        ]);

        CRUD::addField([
            'name'  => 'legenda',
            'label' => 'Legenda / Detalhes',
            'type'  => 'textarea',
            'attributes' => [
                'placeholder' => 'Ex: Formato amendoado com esmaltação em gel e nail art sutil.',
            ],
        ]);

        // Select para vincular à manicure cadastrada no sistema
        CRUD::addField([
            'name'      => 'manicure_id',
            'label'     => 'Profissional responsável',
            'type'      => 'select',
            'entity'    => 'manicure',
            'model'     => "App\Models\User",
            'attribute' => 'name',
            'options'   => (function ($query) {
                // Filtra os usuários que possuem a role de 'manicure'
                // Ajuste o nome da relação ('roles') caso seu pacote de permissões use outro nome
                return $query->whereHas('roles', function ($q) {
                    $q->where('name', 'manicure');
                })->orderBy('name', 'asc')->get();
            }),
        ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}