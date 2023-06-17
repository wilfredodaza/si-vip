<?php

namespace App\Controllers\Api;

use \CodeIgniter\RESTful\ResourceController;

class TypeGenerationTransmition extends ResourceController
{
    protected $format = 'json';
    protected $modelName = 'App\Models\TypeGenerationTransmition';

    public function index()
    {
        $typeGenerationTransmition = $this->model->get()->getResult();

        return $this->respond(['status' => 200, 'data' => $typeGenerationTransmition ]);
    }
}