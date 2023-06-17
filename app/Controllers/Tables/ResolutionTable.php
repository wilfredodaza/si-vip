<?php

namespace App\Controllers\Tables;

use App\Models\Company;
use App\Traits\Grocery;
use Config\Services;

class ResolutionTable
{
    use Grocery;

    protected $columns = [ 'type_documents_id', 'companies_id', 'prefix', 'resolution','from', 'to'];
    protected $hidden = ['created_at', 'updated_at'];

    protected function relations()
    {
        $this->crudTable->setRelation('companies_id', 'companies', '{identification_number} - {company}');
        $this->crudTable->setRelation('type_documents_id', 'type_documents', '{code} - {name}');
    }

    protected function rules()
    {
        $this->crudTable->setRule('resolution_date', 'required');
        $this->crudTable->setRule('from', 'required');
        $this->crudTable->setRule('to', 'required');
        $this->crudTable->setRule('date_from', 'required');
        $this->crudTable->setRule('date_to', 'required');
    }
    protected function fieldType()
    {
        // TODO: Implement fieldType() method.
    }
    protected function callback()
    {
        $this->crudTable->callbackAfterInsert(function ($data) {
            $this->resolution($data);
            return $data;
        });
        $this->crudTable->callbackAfterUpdate(function ($data) {
            $this->resolution($data);
            return $data;
        });
    }


    private function resolution($data)
    {
        $client = Services::curlrequest();
        $companies = new Company();
        $token = $companies->find($data->data['companies_id']);
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');
        $client->setHeader('Authorization', "Bearer " . $token['token']);

        $res = $client->put(
            getenv('API').'/ubl2.1/config/resolution', [
                'form_params' => [
                    'type_document_id'  => $data->data['type_documents_id'],
                    'prefix'            => $data->data['prefix'],
                    'resolution'        => $data->data['resolution'],
                    'resolution_date'   => date("Y-m-d", strtotime($date = str_replace('/', '-', $data->data['resolution_date']))),
                    'technical_key'     => $data->data['technical_key'],
                    'from'              => $data->data['from'],
                    'to'                => $data->data['to'],
                    'date_from'         => date("Y-m-d", strtotime($date = str_replace('/', '-', $data->data['date_from']))),
                    'date_to'           => date("Y-m-d", strtotime($date = str_replace('/', '-', $data->data['date_to']))),
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