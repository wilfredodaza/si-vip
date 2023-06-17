<?php

namespace App\Controllers\Api;

use App\Models\User;
use App\Traits\ResponseApiTrait;
use CodeIgniter\RESTful\ResourceController;
use Config\Services;
use Firebase\JWT\JWT;
use CodeIgniter\API\ResponseTrait;

class Auth extends ResourceController
{

    use ResponseTrait, ResponseApiTrait;

    protected $format = 'json';

    public function create()
    {
        $data = $this->request->getJSON();
        $auth = new User();
        $user = $auth->where(['username' => $data->username])
            ->get()
            ->getResult();

        if (count($user) > 0) {
            if (password_verify($data->password, $user[0]->password)) {
                $key = Services::getSecretKey();
                $time = time();
                $payload = [
                    'iat'   => $time,
                    'exp'   => $time + 60 * 60 *60,
                    'data'  => $user[0]
                ];
             
               $user[0]->token   = JWT::encode($payload, $key);
      
                return $this->respond([
                    'status'    => 200,
                    'data'      => $user[0],
                ], 200);
            }
        }

        return $this->respond([
            'message' => 'Unauthorized',
            'status' => 401
        ], 401);
    }

    protected function validateToken($token)
    {
        try {
            $key = Services::getSecretKey();
            $data = JWT::decode($token, $key, ['HS256']);
            return $data;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function verifyToken()
    {
        if (isset(session('user')->name)) {
            return $this->messageSuccess(session('user'));
        }
        $key = Services::getSecretKey();
        $token = $_SERVER['HTTP_AUTHORIZATION'];
        $data = JWT::decode(str_replace('Bearer ', '',$token), $key, ['HS256']);
        unset($data->data->password);
        return $this->messageSuccess($data->data);
    }

    public static function querys()
    {

        if (isset(session('user')->name)) {
            return session('user');
        } else {
            try{
                $key = Services::getSecretKey();
                $token = $_SERVER['HTTP_AUTHORIZATION'];
                $data = JWT::decode($token, $key, ['HS256']);
                return $data->data;
            }catch (\Exception $e) {
                http_response_code(401);
                echo  json_encode([
                    'status' => 401,
                    'Message' => 'Unauthorized'
                ], 401);
                die();

            }

        }
    }
}