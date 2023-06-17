<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;


class TypeContract extends ResourceController 
{
    protected $format = "json";
    protected $modelName = 'App\Models\TypeContract';

    public function index()
    {
        $typeContracts = $this->model->get()->getResult();

        return $this->respond([
            'status' => 200, 
            'data' =>  $typeContracts
        ]);
    }
}