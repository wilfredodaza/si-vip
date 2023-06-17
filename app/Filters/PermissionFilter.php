<?php


namespace App\Filters;


use App\Controllers\Api\Auth;
use App\Models\ModuleRole;
use App\Models\Permission;
use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Zend\Json\Json;

class PermissionFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments=[])
    {
        $permission = new Permission();
        $moduleRole = new ModuleRole();

        $request = Services::request();
        $url = $request->uri->getSegment(1);

        $method =  $request->uri->getSegment(2);

      if($url == 'table') {

            $moduleRoleId = $moduleRole->select('id')
                ->where([
                    'id' => session('module'),
                    'role_id' => Auth::querys()->role_id
                ])
                ->asObject()
                ->first();
            $data = $permission->select('*')
                ->join('module_role', 'permissions.module_role_id = module_role.id')
                ->join('menus', 'permissions.menu_id = menus.id')
                ->where(['menus.url' =>  $method, 'module_role_id' => $moduleRoleId->id])
                ->first();

            if(!$data && session('user')->role_id != 1) {
                echo view('errors/html/error_401');
                die();
            }
        } else  if($url != 'home' && $url != 'table' && $url != 'config' ) {

            $moduleRoleId = $moduleRole->select('id')
                ->where([
                    'id'     => session('module'),
                    'role_id'       => Auth::querys()->role_id,
                ])->asObject()
                ->first();

            $data = $permission->select('*')
                ->join('module_role', 'permissions.module_role_id = module_role.id')
                ->join('menus', 'permissions.menu_id = menus.id')
                ->where(['module_role_id' => $moduleRoleId->id,  'menus.url' =>  $url ])
                ->first();

            if (!$data && session('user')->role_id != 1) {
                echo view('errors/html/error_401');
                die();
            }
        }
        if($url == 'config' && Auth::querys()->role_id != 1) {
            echo view('errors/html/error_401');
            die();
        }


    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments=[])
    {
        // TODO: Implement after() method.
    }
}