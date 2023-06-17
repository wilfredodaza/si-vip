<?php

namespace App\Controllers\Queues;

use App\Models\Company;
use App\Models\Invoice;
use App\Traits\DocumentSupportTrait;
use App\Traits\PayrollTrait;
use App\Traits\RequestAPITrait;
use App\Traits\ValidateResponseAPITrait;
use CodeigniterExt\Queue\TaskInterface;


class SendDocumentSupport implements TaskInterface
{

    use DocumentSupportTrait, ValidateResponseAPITrait, RequestAPITrait;

    protected $queue;


    public function setData(array $data)
    {
        $this->_data = (array) $data;
    }

    public function run() {

        $data           = $this->createDocument($this->queue['id'], $this->queue['resolution']);
        $model          = new Company();
        $company        = $model->asObject()->find($this->queue['companies_id']);

        $model = new Invoice();
        $model->set('prefix', $data['prefix'])
            ->set('resolution_id', $data['resolution_number'])
            ->set('resolution', $data['number'])
            ->where(['id' => $data['id']])
            ->update();

        $link           = $data['type_document_id']              == 13 ? 'sd-credit-note' : 'support-document';
        $env            = $company->type_environment_payroll_id  == 2 ? '/'.$company->testId : '';
        $res            = $this->sendRequest(getenv('API').'/ubl2.1/'.$link.$env, $data, 'post', $company->token);
        $documentStatus = $this->validStatusCodeHTTP($this->_data['id'], 1, $res, $data['type_document_id']);

        if($documentStatus->error) {
            return true;
        } else {
            return true;
        }

    }

}