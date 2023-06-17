<?php


namespace App\Controllers\Api;



use CodeIgniter\RESTful\ResourceController;


class Tax extends ResourceController
{
    protected $format = 'json';
    protected $modelName = 'App\Models\Tax';

    public function index()
    {
        $taxes = $this->model->get()->getResult();
        return $this->respond(['status' => 200, 'data' => $taxes]);
    }
}