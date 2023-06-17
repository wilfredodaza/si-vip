<?php
namespace App\Filters;

use App\Models\User;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Zend\Json\Json;

class AuthFilter implements FilterInterface
{


    public function before(RequestInterface $request , $arguments=[])
    {
        if(!session('user') && '/' != uri_string()) {
            return redirect()->to(base_url('/'));
        }


       if(session('user')) {
            $model = new User();
            $user = $model
            ->select(['users.status', 'roles.status as role_status'])
            ->where(['users.id' => session('user')->id])
            ->join('roles', 'roles.id = users.role_id')
            ->asObject()
            ->first();
            if($user->status != 'active' || $user->role_status != 'Activo') {
                unset($_SESSION['user']);
                return redirect()->to(base_url('/'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments=[])
    {

    }
}