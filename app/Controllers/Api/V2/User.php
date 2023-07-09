<?php

namespace App\Controllers\Api\V2;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ValidationsTrait2;
use App\Traits\ResponseApiTrait;
use App\Models\User as UserModel;

class User extends ResourceController
{
    use ValidationsTrait2, ResponseApiTrait;
    public function index(){
      $userM = new UserModel();
      $usuarios = $userM
        ->select([
          'users.id as id',
          'concat(users.name," [", roles.name, "]") as name'
        ])
        ->join('roles', 'roles.id = users.role_id', 'inner')
        ->where(['users.id !=' => 1, 'users.status' => 'active'])->asObject()->get()->getResult();
      return $this->respond(['data' => $usuarios]);
    }
}