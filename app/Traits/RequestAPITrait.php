<?php

namespace App\Traits;

use Config\Services;

trait RequestAPITrait
{
    /**
     * @var \CodeIgniter\HTTP\CURLRequest
     */

    private $client;

    /**
     * Initialize vars
     */

    public function __Construct()
    {
        $this->client   = Services::curlrequest();
    }

    /**
     * Send of request the APIDIANREST
     * @param String $url
     * @param array $data
     * @param string $method
     * @param String|null $token
     * @return Object
     */

    protected function sendRequest(String $url, Array $data, String $method = 'post', String $token = null) : Object
    {
        $this->client->setHeader('Content-Type', 'application/json');
        $this->client->setHeader('Accept', 'application/json');
        if($token != null) {
            $this->client->setHeader('Authorization', "Bearer " . $token);
        }
        $res = $this->$method($url, $data);
        return (Object) [
            'status'    => $res->getStatusCode(),
            'data'      => json_decode($res->getBody())
        ];
    }

    /**
     * Send of data in  method HTTP  POST
     * @param $url
     * @param $data
     * @return \CodeIgniter\HTTP\ResponseInterface
     */

    private function post($url, $data)
    {
        return  $this->client->post($url, ['http_errors' => false, 'form_params' => $data]);
    }

    /**
     * Send of data in method HTTP PUT
     * @param $url
     * @param $data
     * @return \CodeIgniter\HTTP\ResponseInterface
     */

    private function put($url, $data)
    {
        return  $this->client->put($url, ['http_errors' => false, 'form_params' => $data]);
    }

    /**
     * Download Files
     * @param String $url
     * @param String $extend
     */

    private function downloadFile(String $url,String $extend, $fileName): void
    {
        header('Content-disposition: attachment; filename='.$fileName);
        header('Content-type: '.$extend);
        readfile($url);
    }

}