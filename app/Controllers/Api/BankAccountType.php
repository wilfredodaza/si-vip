<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;


class BankAccountType extends ResourceController 
{
    protected $format = "json";
    protected $modelName = 'App\Models\TypeAccountBank';

    public function index()
    {
        $payrollPeriods = $this->model->get()->getResult();

        return $this->respond([
            'status' => 200, 
            'data' =>  $payrollPeriods
            ]);
    }
}