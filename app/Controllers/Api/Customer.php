<?php 


namespace App\Controllers\Api;


use App\Controllers\Api\Auth;
use CodeIgniter\RESTful\ResourceController;


class Customer extends ResourceController
{

    protected $format       = 'json';
    protected $modelName    =  'App\Models\Customer';


    public function index()
    {

        $customers = $this->model
        ->select([ 
            'id',
            'name',
            'type_document_identifications_id as type_document_identification_id',
            'identification_number',
            'dv',
            'phone',
            'address',
            'email',
            'email2',
            'email3',
            'merchant_registration',
            'type_customer_id',
            'type_regime_id',
            'municipality_id',
            'type_organization_id',
            'status',
            'created_at',
            'updated_at',
            'user_id'
        ])
        ->where([
            'companies_id'          => Auth::querys()->companies_id, 
            'type_customer_id <'    => 3, 
            'status'            => 'Activo'
        ])
            ->findAll();

        return $this->respond([
            'status' =>  200 , 
            'data' => $customers
        ], 200);

    }

    public function new()
    {

        try {
            $customer = $this->request->getJSON();
            $customer->companies_id = Auth::querys()->companies_id;
            $customer->status =  'Activo';
            if($this->model->insert($customer)) {
                $customer->id = $this->model->getInsertID();
                return $this->respondCreated(['status' =>  201, 'data' => $customer]);
            }else {

                $errors = explode('.', trim(strip_tags($this->model->validation->listErrors())));
                $data = [];
                foreach($errors as $error) {
                    if(!empty(trim($error))) {
                        array_push($data, trim($error).'.');
                    }
                 
                }

                return $this->respond($data);
            }
        
        }catch(\Exception $e) {
            return $this->failServerError('An error has occurred on the server.'.$e);
        }

    }


    public function create()
    {

        try {
            $customer = $this->request->getJSON();
            $customer->companies_id = Auth::querys()->companies_id;
            $customer->status =  'Activo';
            if($this->model->insert($customer)) {
                $customer->id = $this->model->getInsertID();
                return $this->respondCreated(['status' =>  201, 'data' => $customer]);
            }else {

                $errors = explode('.', trim(strip_tags($this->model->validation->listErrors())));
                $data = [];
                foreach($errors as $error) {
                    if(!empty(trim($error))) {
                        array_push($data, trim($error).'.');
                    }

                }

                return $this->respond($data);
            }

        }catch(\Exception $e) {
            return $this->failServerError('An error has occurred on the server.'.$e);
        }

    }

    public function show($id = null)
    {

    }

    public function update($id = null)
    {

    }

    public function delete($id = null)
    {

    }

}