<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;


class PayrollTypeDocumentIdentification extends ResourceController 
{
    protected $format = "json";
    protected $modelName = 'App\Models\TypeDocumentIdentifications';

    public function index()
    {
        $payrollTypeDocumentIdentifications = $this->model
        ->select([
            'id',
            'name',
            'code'
        ])
        ->get()
        ->getResult();

        return $this->respond([
            'status'    => 200, 
            'data'      => $payrollTypeDocumentIdentifications
            ]);
    }
}