<?php

namespace App\Controllers\Api;


use App\Traits\ResponseApiTrait;
use CodeIgniter\RESTful\ResourceController;

class DiscrepancyResponse extends ResourceController
{

    use ResponseApiTrait;

    protected $format = "json";
    protected $modelName = 'App\Models\DiscrepancyResponse';

    public function credit()
    {
        $credit = $this->model->where(['block' => 'NC'])
            ->get()
            ->getResult();

        return $this->messageSuccess($credit);
    }


    public function debit()
    {

        $debit = $this->model->where(['block' => 'NC'])
            ->get()
            ->getResult();
        return $this->messageSuccess($debit);
    }
}