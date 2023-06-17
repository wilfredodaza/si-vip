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
        $this->crudTable->setRelation('role_id', 'roles', 'name' );
    }

    protected function rules()
    {
        $this->crudTable->uniqueFields(['username']);
    }

    protected function fieldType()
    {
        $ids = [];
        $this->crudTable->fieldType('password', 'password');
        $this->crudTable->setFieldUpload('photo', 'assets/upload/images', '/assets/upload/images');
        $company = new Company();
        $headquartersController = new HeadquartersController();
        $companies = $company->select(['id', 'company'])->whereIn('id', $headquartersController->idsCompaniesHeadquarters())->where(['id !=' => 1])->asObject()->get()->getResult();
        foreach ($companies as $item) {
            $ids[$item->id] = $item->company;
        }
        $this->crudTable->fieldType('companies_id', 'dropdown_search', $ids);
    }

    protected function callback()
    {
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
            $this->crudTable->setRelation('role_id', 'roles', 'name' );
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
    }
}