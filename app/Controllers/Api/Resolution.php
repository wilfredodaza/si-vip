<?php


namespace App\Controllers\Api;


use App\Models\Invoice;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Resolution as ResolutionModel;

class Resolution extends ResourceController
{
    protected $format       = 'json';
    protected $modelName    = 'App\Models\Resolution';

    public function resolutions($id = null)
    {
        $resolutions = $this->model
        ->select([
            'id',
            'prefix',
            'resolution',
            'resolution_date',
            'technical_key',
            'from',
            'to',
            'date_from',
            'date_to',
            'type_documents_id as type_document_id'
        ])
        ->where([
            'type_documents_id'     => $id,
            'companies_id'          => Auth::querys()->companies_id,
            'status'                =>  NULL
        ])
            ->orderBy('priority', 'asc')
            ->get()
            ->getResult();


        return $this->respond(['status' => 200, 'data' => $resolutions]);
    }


    public function invoice($id = null)
    {
        return $this->respond(['status' => 200, 'data' => ['number' => $this->_consecutive([1, 2], $id)]]);
    }

    public function creditNote($id = null)
    {
        return $this->respond(['status' => 200, 'data' => ['number' => $this->_consecutive([4], $id)]]);
    
    }

 
    public function debitNote($id = null)
    {
        return $this->respond(['status' => 200, 'data' => ['number' => $this->_consecutive([5], $id)]]);
    }

    public function quatation()
    {
        $id = 0;
        $invoice = new Invoice();
        $data = $invoice->select('resolution')
            ->where(['type_documents_id' => 100, 'companies_id' => Auth::querys()->companies_id])
            ->orderBy('id', 'desc')
            ->get()
            ->getResult();

        if(count($data) > 0) {
            $id = $data[0]->resolution;
        }
        return $this->respond([
            'status' =>  200,
            'data' => [
                'resolution' => $id + 1
            ]
        ], 200);
    }

    public function purchaseOrder()
    {
        $id = 0;
        $invoice = new Invoice();
        $data = $invoice->select('resolution')
            ->where(['type_documents_id' => 114, 'companies_id' => Auth::querys()->companies_id])
            ->orderBy('id', 'desc')
            ->get()
            ->getResult();

        if(count($data) > 0) {
            $id = $data[0]->resolution;
        }
        return $this->respond([
            'status' =>  200,
            'data' => [
                'resolution' => $id + 1
            ]
        ], 200);
    }


    public function payroll($id = null) {
        return $this->respond(['status' => 200, 'data' => ['number' => $this->_consecutive([9], $id)]]);
    }

    private function _consecutive($typeDocument, $resolutionNumber) 
    {

        $data = [
            'companies_id'          => Auth::querys()->companies_id
        ];

        if($typeDocument[0] == 1 || ($resolutionNumber != 'null' && $typeDocument[0] == 4)) {
            $data['resolution_id'] =   $resolutionNumber;
         }

        $invoice = new Invoice();
        $model = $invoice->select(['CONVERT(resolution,UNSIGNED INTEGER) as resolution'])
            ->where($data)
            ->whereIn('type_documents_id', $typeDocument)
            ->orderBy('resolution', 'DESC')
            ->asObject()
            ->first();


        $data = [];
        if(!$model) {
            $data = [
                'companies_id'  => Auth::querys()->companies_id
            ];
            if($typeDocument[0] == 1 || $typeDocument[0] == 4) {
                $data['resolution'] =   $resolutionNumber;
            }

            $resolutions = $this->model->select(['`from`'])
                ->whereIn('type_documents_id', $typeDocument)
                ->where($data)
                ->asObject()
                ->first();       


            return  (int) $resolutions->from;
        }else {
            return  $model->resolution + 1;
        }
    }
}