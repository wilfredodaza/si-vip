<?php

namespace App\Controllers\Tables;

use App\Traits\Grocery;
use App\Models\Company;

class PersonalizationLaborCertificateTable
{
    use Grocery;

    protected  $hidden = [
        'created_at',
        'updated_at',
    ];

    protected  $columns = [
        'municipality_id',
        'telephone',
        'web_page',
        'address'
    ];

    protected function relations()
    {
        $this->crudTable->setRelation('company_id', 'companies', '{identification_number} -  {company}');
        $this->crudTable->setRelation('municipality_id', 'municipalities', '{code} -  {name}');
    }

    protected function rules()
    {}
    
    protected function fieldType()
    {
        $this->crudTable->setFieldUpload('firm', 'assets/upload/images', '/assets/upload/images');
        $this->crudTable->setFieldUpload('stamp', 'assets/upload/images', '/assets/upload/images');
    }

    protected function callback()
    {

        if(session('user')->role_id >= 2) {
            $this->crudTable->where(['company_id' => session('user')->companies_id]);
            $company = new Company();
            $count = $company->join('personalization_labor_certificates', 'personalization_labor_certificates.company_id =companies.id')
            ->where(['companies.id' => session('user')->companies_id])
            ->asObject()
            ->countAllResults();

            if($count != 0) {
                $this->crudTable->unsetAdd();
            }
            $this->crudTable->fieldType('company_id','hidden');
            $this->crudTable->callbackAddForm(function ($data) {
                $data['company_id'] = session('user')->companies_id;
                return $data;
            });
        }else if (session('user')->role_id == 1) {
            $this->crudTable->columns([
                'company_id',
                'municipality_id',
                'telephone',
                'web_page',
                'address'
            ]);
        }
        
    }
}