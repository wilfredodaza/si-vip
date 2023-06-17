<?php


namespace App\Controllers\Api;


use App\Traits\TransformTrait;
use CodeIgniter\RESTful\ResourceController;

class TypeCustomer extends  ResourceController
{
    use TransformTrait;

    protected $format = 'json';

    public function index()
    {
        $typeCustomer = new \App\Models\TypeCustomer();
        $data = $typeCustomer->get()->getResult();

        $tranformArray = [];
        foreach ($data as $item) {
            $transform = self::data( $item, [
                'id'     =>     '_id',
                'name'   =>     'name',
            ]);
            array_push($tranformArray, $transform);
        }

        return $this->respond([
            'status' => 200,
            'data' => $tranformArray
        ]);
    }
}