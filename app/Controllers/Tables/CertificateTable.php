<?php

namespace App\Controllers\Tables;

use App\Models\Company;
use App\Traits\Grocery;
use Config\Services;

class CertificateTable
{
    use Grocery;

    protected $hidden = [
      'created_at', 'updated_at'
    ];

    protected function relations()
    {
        $this->crudTable->setRelation('companies_id', 'companies', 'company');
    }

    protected function rules()
    {
        $this->crudTable->setRule('name', 'required');
        $this->crudTable->setRule('password', 'required');
    }

    protected function fieldType()
    {
        $this->crudTable->setFieldUpload('name', 'assets/upload/certificates', '/assets/upload/certificates');
    }

    protected function callback()
    {
        $this->crudTable->callbackAfterInsert(function ($data) {
            $this->certificate($data);
            return $data;
        });
        $this->crudTable->callbackAfterUpdate(function ($data) {
            $this->certificate($data);
            return $data;
        });
    }


    public function certificate($data)
    {
        $client = Services::curlrequest();
        $companies = new Company();
        $token = $companies->find($data->data['companies_id']);
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $token['token']);

        $res = $client->put(
            getenv('API').'/ubl2.1/config/certificate', [
                'form_params' => [
                    'certificate' => base64_encode(file_get_contents(base_url() . '/assets/upload/certificates/' . $data->data['name'])),
                    'password' => $data->data['password']
                ],
            ]
        );
        $json = json_decode($res->getBody());
        if (isset($json->errors)) {
            echo json_encode($json);
            die();
        }

    }
}