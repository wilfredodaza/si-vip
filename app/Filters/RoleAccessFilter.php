<?php


namespace App\Filters;


use App\Controllers\Api\Auth;
use App\Models\Invoice;
use App\Models\ModuleRole;
use App\Models\Permission;
use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Zend\Json\Json;

class RoleAccessFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments=[])
    {
        $uri = service('uri');
        $id = null;
        for($i = 1; $i < 5; $i++) {
            if(is_numeric($uri->getSegment($i))) {
                $id = $uri->getSegment($i);
                break;
            }
        }



        if(is_null($id)) {
            echo view('errors/html/error_401');
            die();
        }

        $invoice = new Invoice();
        $document = $invoice->where(['id' => $id])
            ->asObject()
            ->first();

        if($document->companies_id != Auth::querys()->companies_id) {
            echo view('errors/html/error_401');
            die();
        }


    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments=[])
    {
        // TODO: Implement after() method.
    }
}