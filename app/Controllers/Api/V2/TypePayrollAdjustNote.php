<?php


namespace App\Controllers\Api\V2;


use CodeIgniter\RESTful\ResourceController;

class TypePayrollAdjustNote extends ResourceController
{
    protected $modelName    = 'App\Models\TypePayrollAdjustNote';
    protected $format       = 'json';

    public function index()
    {
        $data = $this->model->findAll();
        return  $this->respond(['status' => 200, 'data' => $data]);
    }
}