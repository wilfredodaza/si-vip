<?php

namespace App\Controllers\Tables;

use App\Traits\Grocery;

class PartnershipTable
{
    use Grocery;

    protected  $hidden = [
        'created_at',
        'updated_at'
    ];

    protected  $columns = [
        'company_id',
        'type_document_identification_id',
        'identification_number',
        'dv',
        'participation_percentage'
    ];

    protected function relations()
    {
        $this->crudTable->setRelation('company_id', 'companies', '{identification_number} -  {company}');
        $this->crudTable->setRelation('tax_id', 'taxes', '{code} -  {name}');
        $this->crudTable->setRelation('type_liability_id', 'type_liabilities', '{code} -  {name}');
        $this->crudTable->setRelation('type_document_identification_id', 'type_document_identifications', '{code} - {name}');
        $this->crudTable->setRelation('type_regime_id', 'type_regimes', '{code} - {name}');
    }

    protected function rules()
    {
        $this->crudTable->setRule('company_id', 'required');
        $this->crudTable->setRule('type_document_identification_id', 'required');
        $this->crudTable->setRule('type_liability_id', 'required');
        $this->crudTable->setRule('identification_number', 'required');
        $this->crudTable->setRule('type_regime_id', 'required');
        $this->crudTable->setRule('dv', 'required');
        $this->crudTable->setRule('tax_id', 'required');
    }

    protected function fieldType()
    {
        $this->crudTable->fieldType('dv', 'numeric');
        $this->crudTable->fieldType('participation_percentage', 'numeric');
    }

    protected function callback()
    {
        // TODO: Implement callback() method.
    }
}