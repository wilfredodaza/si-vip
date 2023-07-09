<?php


namespace App\Controllers\Api;


use App\Models\User;
use CodeIgniter\RESTful\ResourceController;

class Seller extends ResourceController
{
    public function index()
    {
        $userM = new User();
        $users = $userM
            // ->join('company')
            ->where(['role_id >=' => 15, 'status' => 'active'])
            ->get()->getResult();
        return $this->respond(['status' => 200, 'data' => $users]);
    }

    public function create()
    {}
}