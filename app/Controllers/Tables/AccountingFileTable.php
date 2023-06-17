<?php
namespace App\Controllers\Tables;

use App\Traits\Grocery;
use App\Controllers\Api\Auth;


class AccountingFileTable
{

    use Grocery;

    protected  $hidden 	= ['created_at', 'updated_at'];
    protected  $columns = ['title', 'observation', 'type', 'status', 'created_at', 'updated_at'];



    public function relations()
    {
        $this->crudTable->setRelation('company_id', 'companies', '{identification_number} -  {company}');
    }

    public function rules()
    {
        // TODO: Implement rules() method.
    }

    public function fieldType()
    {
        $this->crudTable->setFieldUpload('filename', 'upload/accounting_uploads', '/upload/accounting_uploads');
    }

    public function callback()
    {
        if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
            $this->crudTable->where(['company_id' => Auth::querys()->companies_id]);
            $this->crudTable->fieldType('company_id', 'hidden');
            $this->crudTable->callbackAddForm(function ($data) {
                $data['company_id'] = Auth::querys()->companies_id;
                return $data;
            });
        }
    }
}