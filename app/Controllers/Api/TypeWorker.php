<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;


class TypeWorker extends ResourceController 
{
    protected $format = "json";
    protected $modelName = 'App\Models\TypeWorker';

    public function index()
    {
        $typeWorkers = $this->model->get()->getResult();

        return $this->respond([
            'status' => 200, 
            'data' =>  $typeWorkers
        ]);
    }
}