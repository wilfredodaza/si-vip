<?php

namespace App\Controllers\Tables;

use App\Controllers\Api\Auth;
use App\Controllers\HeadquartersController;
use App\Models\Company;
use App\Models\Role;
use App\Traits\Grocery;

class UserTable
{
    use Grocery;

    protected $hidden = [];
    protected $columns = ['username', 'name','email', 'companies_id'];

    protected function relations()
    {
        $this->crudTable->setRelation('companies_id', 'companies', 'company');
        $this->crudTable->setRelation('role_id', 'roles', 'name', ['status' => 'Activo', 'id > ?' => 1] );
    }

    protected function rules()
    {
        $this->crudTable->uniqueFields(['username']);
        $this->crudTable->setRule('identification_number', 'required');
        $this->crudTable->setRule('phone', 'required');
        $this->crudTable->setRule('name', 'required');
        $this->crudTable->setRule('phone', 'lengthBetween', ['7', '10']);
        $this->crudTable->setRule('address', 'required');
        $this->crudTable->setRule('email', 'required');
        $this->crudTable->setRule('neighborhood', 'required');
    }

    protected function fieldType()
    {
        $ids = [];
        
        $this->crudTable->displayAs([
            'companies_id' => 'Sede',
            'type_document_identifications_id' => 'Tipo de documento',
            'identification_number' => 'Numero de identificacion',
            'phone' => 'Numero de telefono',
            'address' => 'Dirección',
            'neighborhood' => 'Barrio',
            'password' => 'Contraseña'
        ]);
        $this->crudTable->fieldType('password', 'password');
        $this->crudTable->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');
        $company = new Company();
        $headquartersController = new HeadquartersController();
        $companies = $company->select(['id', 'company'])->whereIn('id', $headquartersController->idsCompaniesHeadquarters())->where(['headquarters_id !=' => 1])->asObject()->get()->getResult();
        foreach ($companies as $item) {
            $ids[$item->id] = $item->company;
        }
        $this->crudTable->fieldType('companies_id', 'dropdown_search', $ids);
        $this->crudTable->fieldType('phone', 'int');

    }

    protected function callback()
    {
        
        $this->crudTable->setRelation('type_document_identifications_id', 'type_document_identifications', 'name');
        $this->crudTable->where('role_id >= 15');

        if (session('user')->role_id == 2) {
            $role = new Role();
            $roles = $role->select(['id', 'name'])
                ->whereNotIn('id', [1, 2, 4, 5, 6])
                ->whereIn('id', [3, 7, 10])
                ->orWhereIn('companies_id', [Auth::querys()->companies_id])
                ->get()
                ->getResult();

            $rolesData = [];
            foreach ($roles as $rol) {
                $rolesData[(string) $rol->id] = $rol->name;
            }
            $this->crudTable->fieldType('role_id','dropdown', $rolesData);
            $this->crudTable->where(['companies_id' => session('user')->companies_id]);
            $this->crudTable->callbackAddForm(function ($data) {
                $data['companies_id'] = session('user')->companies_id;
                return $data;
            });

        }else if(session('user')->role_id == 1){
            $this->crudTable->setRelation('role_id', 'roles', 'name', ['status' => 'Activo', 'id > ?' => 1] );
        }

        $this->crudTable->callbackBeforeInsert(function ($stateParameters) {
            $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
            return $stateParameters;
        });

        $this->crudTable->callbackBeforeUpdate(function ($stateParameters) {
            if (strlen($stateParameters->data['password']) <= 20) {
                $stateParameters->data['password'] = password_hash($stateParameters->data['password'], PASSWORD_DEFAULT);
            }
            return $stateParameters;
        });

        $this->crudTable->setActionButton('Perfil', 'fa fa-user', function ($row) {
            return base_url('customers/employee') . '/' . $row->id;
        }, false);
    }
}