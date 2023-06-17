<?php


namespace App\Controllers\Api;


use App\Models\Wallet as WalletModel;
use CodeIgniter\RESTful\ResourceController;

class Wallet extends ResourceController
{
    protected $format = 'json';
    public function edit($id = null)
    {
        $wallet = new WalletModel();
        $wallet = $wallet->asObject()->find($id);
        return $this->respond([
            'status' => '200',
            'data' => $wallet
        ], 200);
    }
}