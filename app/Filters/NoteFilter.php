<?php


namespace App\Filters;
use App\Models\Invoice;
use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;


class NoteFilter implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = [])
    {
        $request = Services::request();
        if($url = $request->uri->getSegment(2)){
            if(session('user')->role_id != 1){
                if(!empty($url) && is_numeric($url)){
                    $invoice = new Invoice();
                    $data = $invoice->find(['id' => $url]);
                    if (!$data) {
                        echo  view('errors/html/error_401');
                        die();
                    }
                }
            }

        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = [])
    {
        // TODO: Implement after() method.
    }
}