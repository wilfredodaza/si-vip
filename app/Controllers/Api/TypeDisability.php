<?php 


namespace App\Controllers\Api;



use CodeIgniter\RESTful\ResourceController;


class TypeDisability extends ResourceController
{
    protected $format = 'json';
    protected $modelName = 'App\Models\TypeDisability';

    public function index()
    {
       $typeDisability = $this->model->get()->getResult();

       return $this->respond(['status' => 200, 'data' => $typeDisability ]);
    }
}