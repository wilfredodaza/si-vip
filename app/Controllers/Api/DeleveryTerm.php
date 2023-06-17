<?php

namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;

class DeleveryTerm extends ResourceController
{
    protected $format           = 'json';
    protected $modelName        = 'App\Models\DeleveryTerm';

    public function index()
    {
        $data = $this->model->findAll();
        return $this->respond([
            'status'    => 200,
            'data'      => $data
        ], 200);
    }

}