<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;


class SubTypeWorker extends ResourceController 
{
    protected $format = "json";
    protected $modelName = 'App\Models\SubTypeWorker';

    public function index()
    {
        $subTypeWorkers = $this->model->get()->getResult();

        return $this->respond([
            'status'    =>  200, 
            'data'      =>  $subTypeWorkers
        ]);
    }
}