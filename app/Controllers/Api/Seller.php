<?php


namespace App\Controllers\Api;


use App\Models\User;
use CodeIgniter\RESTful\ResourceController;

class Seller extends ResourceController
{
    public function index()
    {
        $customer = new \App\Models\Customer();
        $users = $customer->whereIn('type_customer_id',[ 3,4] )->get()->getResult();
        return $this->respond(['status' => 200, 'data' => $users]);
    }

    public function create()
    {}
}