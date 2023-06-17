<?php


namespace App\Controllers\Api;


use App\Models\TypeRegimes;
use App\Traits\TransformTrait;
use CodeIgniter\RESTful\ResourceController;

class TypeRegime extends ResourceController
{

    use TransformTrait;

    protected $format ='json';

    public function index()
    {
        $typeRegime = new TypeRegimes();
        $data = $typeRegime->get()->getResult();

        $tranformArray = [];
        foreach ($data as $item) {
            $transform = self::data( $item, [
                'id'     =>     '_id',
                'name'   =>     'name',
                'code'   =>     'code'
            ]);
            array_push($tranformArray, $transform);
        }

        return $this->respond([
            'status' => 200,
            'data' => $tranformArray
        ]);
    }
}