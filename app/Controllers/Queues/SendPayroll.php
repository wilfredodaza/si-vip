<?php

namespace App\Controllers\Queues;

use App\Traits\RequestAPITrait;
use App\Traits\ValidateResponseAPITrait;
use CodeigniterExt\Queue\TaskInterface;
use App\Traits\PayrollTrait;
use App\Models\Company;
use App\Models\Invoice;


class SendPayroll implements TaskInterface
{

    use PayrollTrait, RequestAPITrait, ValidateResponseAPITrait;

    protected $_data;


    public function setData(array $data) 
    {
        $this->_data = (array)$data;
    }

    public function run() {

        $data =  $this->group($this->_data['id'], $this->_data['resolution'], $this->_data['companies_id']);

        $model = new Company();
        $company = $model->asObject()->find($this->_data['companies_id']);

        $link   = $data['type_document_id']       == 10 ?  'payroll-adjust-note' : 'payroll';
        $env    = $company->type_environment_payroll_id  == 2 ? '/'.$company->testId : '';
        $res            = $this->sendRequest(getenv('API').'/ubl2.1/'.$link.$env, $data, 'post', $company->token);
        $documentStatus = $this->validStatusCodeHTTP($this->_data['id'], 1, $res, $data['type_document_id']);

        if($documentStatus->error) {
            return true;
        } else {
            $invoice = new Invoice();
            $invoice->update($this->_data['id'], [
                'invoice_status_id'     => 14,
                'resolution'            => $data['consecutive'],
                'resolution_id'         => $data['resolution_number'],
                'prefix'                => $data['prefix']
            ]);
            return true;
        }
    }
    
}