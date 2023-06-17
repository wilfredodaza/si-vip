<?php
namespace App\Controllers\Tables;

use App\Models\Company;
use App\Traits\Grocery;
use Config\Services;


class ConnectEmailTable
{

    use Grocery;

    protected  $hidden = [
        'created_at',
        'updated_at'
    ];

    protected  $columns = [
        'company_id',
        'server',
        'email',
        'password',
        'port'
    ];


    public function relations() 
    {
        $this->crudTable->setRelation('company_id', 'companies', 'company');
    }


    public function rules()
    {
        $this->crudTable->setRule('company_id', 'required');
        $this->crudTable->setRule('server', 'required');
        $this->crudTable->setRule('email', 'required');
        $this->crudTable->setRule('password', 'required');
        $this->crudTable->setRule('port', 'required');
    }

    public function fieldType()
    {
        $this->crudTable->fieldType('password', 'password');
        $this->crudTable->fieldType('email', 'email');
        $this->crudTable->fieldType('port', 'numeric');
    }

    public function callback()
    {
    
    }
}