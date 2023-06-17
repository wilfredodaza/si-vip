<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Config\Services;
use Firebase\JWT\JWT;
use CodeIgniter\API\ResponseTrait;


class AuthApiFilter implements FilterInterface
{
    use ResponseTrait;

    public function before(RequestInterface $request, $arguments = [])
    {

  
        if(!isset(session('user')->name)){
            try {
                $key = Services::getSecretKey();
                $authHeader = $request->getServer('HTTP_AUTHORIZATION');
                $arr = explode(' ', $authHeader);
                $token = $arr[1];
                JWT::decode($token, $key, ['HS256']);
            } catch (\Exception $e) {
                http_response_code(401);
                echo json_encode([
                        'status' => 401,
                        'Message' => 'Unauthorized',
                    ]);
                die();
            }
        }

    }

//--------------------------------------------------------------------

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = [])
    {
// Do something here
    }
}
