<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Http\Requests\UserRequest;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('usuário', 'usuários');
    }

    // 📋 O que aparece na tabela de listagem
    protected function setupListOperation()
    {
        CRUD::column('name')->label('Nome');
        CRUD::column('email')->label('E-mail');
        
        // Exibe as funções (roles) do usuário na listagem
        CRUD::addColumn([
            'name'      => 'roles',
            'type'      => 'relationship',
            'label'     => 'Permissões / Cargo',
            'attribute' => 'name',
        ]);
    }

    // ➕ O que aparece no formulário de Criar/Editar
    protected function setupCreateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        CRUD::field('name')->type('text')->label('Nome Completo');
        CRUD::field('email')->type('email')->label('E-mail');
        CRUD::field('password')->type('password')->label('Senha');

        // 🔥 O campo mágico que vincula as Roles (Admin, Manicure, Caixa)
        CRUD::addField([
            'name'      => 'roles',
            'label'     => 'Cargo / Nível de Acesso',
            'type'      => 'relationship',
            'attribute' => 'name',
            // Permite escolher mais de um papel se necessário, usando checkboxes organizados
            'pivot'     => true, 
            'inline_create' => false,
        ]);
    }

    protected function setupUpdateOperation()
    {
        // Reaproveita os campos da criação, mas remove a obrigatoriedade da senha se ficar em branco
        $this->setupCreateOperation();
        
        // Altera o campo password para não substituir a senha antiga caso o admin não digite nada
        CRUD::field('password')->type('password')->label('Nova Senha (deixe em branco para manter a atual)')->attributes(['autocomplete' => 'new-password']);
    }
}