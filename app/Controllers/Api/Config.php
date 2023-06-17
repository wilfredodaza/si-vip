<?php


namespace App\Controllers\Api;


use App\Models\Config as ConfigModel;
use CodeIgniter\RESTful\ResourceController;


class Config extends ResourceController {
    protected $format = 'json';

    public function index()
    {
        $config  = new ConfigModel();
        $configurations = $config->where(['companies_id' => Auth::querys()->companies_id ])->get()->getResult();
        if(count( $configurations) > 0 ) {
            return $this->respond(['data' => $configurations[0]->default_notes, 'status' => 200], 200);
        }
       return $this->respond(['data' => '', 'status' => 200], 200);
    }
}