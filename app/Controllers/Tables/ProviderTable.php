<?php

namespace App\Controllers\Tables;

use App\Models\User;
use App\Traits\Grocery;

class ProviderTable
{
    use Grocery;
    protected $hidden = [
        'dv',
        'type_customer_i',
        'firm',
        'email2',
        'email3',
        'created_at',
        'updated_at',
        'deleted_at',
        'user_id',
        'status',
        'rut',
        'bank_certificate'
    ];

    protected $columns = [
        'name',
        'type_document_identifications_id',
        'identification_number',
        'dv',
        'email'
    ];


    protected function relations()
    {
        $this->crudTable->setRelation('companies_id', 'companies', '{identification_number} - {company}');
        $this->crudTable->setRelation('type_document_identifications_id', 'type_document_identifications', '{code} - {name}');
        $this->crudTable->setRelation('type_customer_id', 'type_customer', 'name');
        $this->crudTable->setRelation('type_regime_id', 'type_regimes', '{code} - {name}');
        $this->crudTable->setRelation('municipality_id', 'municipalities', '{code} - {name}');
        $this->crudTable->setRelation('type_organization_id', 'type_organizations', '{code} - {name}');
        $this->crudTable->setRelation('type_liability_id', 'type_liabilities', '{code} - {name}');
    }

    protected function rules()
    {
        $this->crudTable->setRule('name', 'required');
        $this->crudTable->setRule('identification_number', 'required');
        $this->crudTable->setRule('phone', 'required');
        $this->crudTable->setRule('phone', 'lengthBetween', ['7', '10']);
        $this->crudTable->setRule('address', 'required');
        $this->crudTable->setRule('email', 'required');
        $this->crudTable->setRule('merchant_registration', 'required');
        $this->crudTable->setRule('type_regime_id', 'required');
        $this->crudTable->setRule('type_document_identifications_id', 'required');
        $this->crudTable->setRule('type_organization_id', 'required');
        $this->crudTable->setRule('municipality_id', 'required');
    }

    protected function fieldType()
    {
        $this->crudTable->fieldType('phone', 'int');
    }

    protected function callback()
    {
        $this->crudTable->setActionButton('Enviar invitación', 'fa fa-envelope', function ($row) {
            return base_url('/document_support/sending_invitation_provider/' . $row->id);
        }, false);

        $this->crudTable->unsetDeleteMultiple();
        $this->crudTable->where([
            'type_customer_id' => 2,
            'companies_id' => session('user')->companies_id
        ]);
        $this->crudTable->callbackBeforeInsert(function ($data) {
            $data->data['identification_number']    = $this->_clearNumber($data->data['identification_number']);
            $data->data['phone']                    = $this->_clearNumber($data->data['phone']);
            $data->data['status']                   = 'Activo';
            $data->data['dv']                       = $this->calcularDV($data->data['identification_number']);
            return $data;
        });
        $this->crudTable->callbackBeforeUpdate(function ($data) {
            $data->data['identification_number']    = $this->_clearNumber($data->data['identification_number']);
            $data->data['phone']                    = $this->_clearNumber($data->data['phone']);
            $data->data['dv']                       = $this->calcularDV($data->data['identification_number']);
            return $data;
        });



        if (session('user')->role_id == 2 || session('user')->role_id >= 3) {
            $this->crudTable->unsetColumns(['companies_id']);
            $this->crudTable->where([
                'companies_id' => session('user')->companies_id,
                'type_customer_id' => 2
            ]);

            $this->crudTable->fieldType('companies_id', 'hidden');
            $this->crudTable->callbackAddForm(function ($data) {
                $data['companies_id']               = session('user')->companies_id;
                $data['dv']                         = $this->calcularDV($data['identification_number']);
                $data['merchant_registration']      = '00000';
                $data['type_customer_id']           = 2;
                return $data;
            });
        } else {
            $this->crudTable->callbackAddForm(function ($data) {
                $data['merchant_registration']          = '00000';
                $data['dv']                             = $this->calcularDV($data['identification_number']);
                $data['type_customer_id']               = 2;
                return $data;
            });
        }
    }

    private function calcularDV($nit) {
        if (! is_numeric($nit)) {
            return false;
        }

        $arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19,
            8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
        $x = 0;
        $y = 0;
        $z = strlen($nit);
        $dv = '';

        for ($i=0; $i<$z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y*$arr[$z-$i]);
        }

        $y = $x%11;

        if ($y > 1) {
            $dv = 11 - $y;
            return $dv;
        } else {
            $dv = $y;
            return $dv;
        }

    }

    private function _clearNumber($data)
    {
        $data = str_replace('-', '', $data);
        return $data;
    }

}