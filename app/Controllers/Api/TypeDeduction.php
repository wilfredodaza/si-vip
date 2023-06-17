<?php 


namespace App\Controllers\Api;



use CodeIgniter\RESTful\ResourceController;


class TypeDeduction extends ResourceController
{
    protected $format = 'json';
    protected $modelName = 'App\Models\TypeDeduction';

    public function index()
    {
       $typeAccrueds = $this->model->get()->getResult();

       return $this->respond(['status' => 200, 'data' => $typeAccrueds ]);
    }
}