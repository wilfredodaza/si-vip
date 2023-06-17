<?php


namespace App\Controllers\Api;

use App\Models\Currency as TypeCurrency;
use CodeIgniter\RESTful\ResourceController;


class Currency extends ResourceController
{
    public function index()
    {
        $currency = new TypeCurrency();
        $currency = $currency->findAll();
        return $this->respond($currency, 200);
    }

    public function list()
    {
        $currency = new TypeCurrency();
        $currency = $currency->findAll();
        return $this->respond([
            'data'      => $currency,
            'status'    => 200
        ], 200);
    }
}