<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;
use App\Traits\ValidationsTrait;
use App\Models\Customer;
use App\Models\CustomerWorker;

class Worker extends ResourceController
{
    use ValidationsTrait;

    protected $format       = 'json';
    protected $modelName    =  'App\Models\Customer';


    public function index()
    {
        $workers = $this->model
        ->select([
            'customers.id',
            'customer_worker.worker_code',
            'customers.name as first_name',
            'customer_worker.second_name',
            'customer_worker.surname',
            'customer_worker.second_surname',
            'customers.address',
            'customers.email',
            'customers.type_document_identifications_id as type_document_identification_id',
            'customers.identification_number',
            'customer_worker.salary',
            'customers.municipality_id',
            'customer_worker.type_worker_id',
            'customer_worker.sub_type_worker_id',
            'customer_worker.type_contract_id',
            'customer_worker.high_risk_pension',
            'customer_worker.integral_salary',
            'customer_worker.bank_id',
            'customer_worker.bank_account_type_id',
            'customer_worker.account_number',
            'customer_worker.admision_date',
            'customer_worker.retirement_date'

            
            
        ])
        ->join('customer_worker', 'customer_worker.customer_id = customers.id')->get()->getResult();
        return $this->respond([
            'status' => 200, 
            'data' => $workers
        ]);
    }


    public function new()
    {

        try{
            $this->validateRequest(
                $this->request,
                [
                    'type_worker_id'                    => 'required|numeric|is_not_unique[type_workers.id]',
                    'sub_type_worker_id'                => 'required|numeric|is_not_unique[sub_type_workers.id]',
                    'type_document_identification_id'   => 'required|numeric|is_not_unique[type_document_identifications.id]',
                    'municipality_id'                   => 'required|numeric|is_not_unique[municipalities.id]',
                    'type_contract_id'                  => 'required|numeric|is_not_unique[type_contracts.id]',
                    'high_risk_pension'                 => 'required|in_list[true, falso]',
                    'identification_number'             => 'required|max_length[45]|is_unique[customers.identification_number,companies_id,{!$companies_id}]|is_unique[customers.identification_number,type_customer_id,3]',
                    'surname'                           => 'required|max_length[45]',
                    'second_surname'                    => 'max_length[45]',
                    'first_name'                        => 'required|max_length[45]',
                    'second_name'                       => 'required|max_length[45]',
                    'address'                           => 'required|max_length[100]',
                    'email'                             => 'required|valid_email',
                    'integral_salary'                   => 'required|in_list[true, falso]',
                    'salary'                            => 'required|numeric|max_length[23]',
                    'bank_id'                           => 'required|numeric|is_not_unique[banks.id]',
                    'bank_account_type_id'              => 'required|numeric|is_not_unique[bank_account_types.id]',
                    'account_number'                    => 'required|numeric|max_length[45]',
                    'admision_date'                     => 'required|valid_date',
                    'payment_method_id'                 => 'required|numeric|is_not_unique[payment_methods.id]',
                    'worker_code'                       => 'if_exist|max_length[5]'
                ]
            );
        } catch(\Exception $e) {

        }

        if($this->validator->getErrors()) {
            return $this->respond([
                'status'    => 400,
                'errors'    => $this->validator->getErrors()
            ], 400);
        }
    
        $json = $this->request->getJSON();
        $customerData = [
            'name'                              =>  $json->first_name,
            'identification_number'             =>  $json->identification_number,
            'address'                           =>  $json->address,
            'email'                             =>  $json->email,
            'companies_id'                      =>  Auth::querys()->companies_id,
            'municipality_id'                   =>  $json->municipality_id,
            'type_customer_id'                  =>  3,
            'type_document_identifications_id'  =>  $json->type_document_identification_id
        ];


        $model = new Customer();
        $customer = $model->insert($customerData);

       


        $customerWorkerData = [
            'type_worker_id'        => $json->type_worker_id,
            'sub_type_worker_id'    => $json->sub_type_worker_id,
            'type_contract_id'      => $json->type_contract_id,
            'payment_method_id'     => $json->payment_method_id,
            'high_risk_pension'     => $json->high_risk_pension,
            'second_name'           => $json->second_name,
            'surname'               => $json->surname,
            'second_surname'        => $json->second_surname,
            'integral_salary'       => $json->integral_salary,
            'salary'                => $json->salary,
            'bank_id'               => $json->bank_id,
            'bank_account_type_id'  => $json->bank_account_type_id,
            'account_number'        => $json->account_number,
            'admision_date'         => $json->admision_date,
            'retirement_date'       => $json->retirement_date ?? NULL,
            'worker_code'           => $json->worker_code ?? NULL,
            'customer_id'           => $customer
        ];
        

        $model  = new CustomerWorker();
       

        if($id = $model->insert($customerWorkerData)) {
            $customerData['id'] = $id;
            unset($customerWorkerData['customer_id']);
            unset($customerData['companies_id']);
            return $this->respondCreated([
                'status'    => 201,
                'data'      => array_merge($customerData, $customerWorkerData)
            ]);
        }

    }

   
}