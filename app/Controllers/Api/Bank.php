<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;


class Bank extends ResourceController 
{
    protected $format = "json";
    protected $modelName = 'App\Models\Bank';

    public function index()
    {
        $banks = $this->model->get()->getResult();

        return $this->respond([
            'status' => 200, 
            'data' =>  $banks
            ]);
    }
}