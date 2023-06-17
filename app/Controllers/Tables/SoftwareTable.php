<?php

namespace App\Controllers\Tables;

use App\Interfaces\ITable;
use App\Models\Company;
use App\Traits\Grocery;
use Config\Services;

class SoftwareTable
{
    use Grocery;

    protected  $hidden =  ['created_at', 'updated_at'];
    protected  $columns = [
        'companies_id',
        'identifier',
        'pin',
        'created_at',
        'updated_at'
    ];

    public function relations()
    {
        $this->crudTable->setRelation('companies_id', 'companies', 'company');
    }

    public function rules()
    {
        $this->crudTable->setRule('identifier', 'lengthBetween', ['0' , '191']);
        $this->crudTable->setRule('pin', 'lengthBetween', ['0' , '5']);
        $this->crudTable->setRule('identifier_payroll', 'lengthBetween', ['0' , '191']);
        $this->crudTable->setRule('pin_payroll', 'lengthBetween', ['0' , '5']);
    }

    public function fieldType()
    {
        $this->crudTable->fieldType('pin', 'numeric');
        $this->crudTable->fieldType('pin_payroll', 'numeric');
    }

    public function callback()
    {
        $this->crudTable->callbackAfterInsert(function ($data) {
            $this->software($data);
            return $data;
        });
        $this->crudTable->callbackAfterUpdate(function ($data) {
            $this->software($data);
            return $data;
        });
    }

    public function table()
    {

        $this->rules();
        $this->callback();
     $this->init();
        $this->timestamps();
        $output = $this->crudTable->render();
        if (isset($output->isJSONResponse) && $output->isJSONResponse) {
            header('Content-Type: application/json; charset=utf-8');
            echo $output->output;
            exit;
        }
    }

    private function software($data)
    {
        $client = Services::curlrequest();
        $companies = new Company();
        $token = $companies->find($data->data['companies_id']);
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $token['token']);

        $res = $client->put(getenv('API').'/ubl2.1/config/software', [
                'form_params' => [
                    'id'    => $data->data['identifier'],
                    'pin'   => $data->data['pin']
                ],
            ]
        );

        if (!empty($data->data['identifier_payroll']) && !is_null($data->data['identifier_payroll'])){
            $res = $client->put(
                getenv('API').'/ubl2.1/config/softwarepayroll', [
                    'form_params' => [
                        'idpayroll'     => $data->data['identifier_payroll'],
                        'pinpayroll'    => $data->data['pin_payroll']
                    ],
                ]
            );
        }


        $json = json_decode($res->getBody());
        if (isset($json->errors)) {
            echo json_encode($json);
            die();
        }
    }
}