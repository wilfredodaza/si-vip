<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;

class Company extends ResourceController
{
    protected $format = 'json';
    protected $modelName = 'App\Models\Company';

    public function index(){
        $company = $this->model->find(Auth::querys()->companies_id);
        return $this->respond(['status' =>  200, 'data'  => $company], 200);
    }

}