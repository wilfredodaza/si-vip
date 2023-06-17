<?php 


namespace App\Controllers\Api;



use CodeIgniter\RESTful\ResourceController;


class TypeLawDeduction extends ResourceController
{
    protected $format = 'json';
    protected $modelName = 'App\Models\TypeLawDeduction';

    public function index()
    {
       $typeAccrueds = $this->model->get()->getResult();

       return $this->respond(['status' => 200, 'data' => $typeAccrueds ]);
    }
}