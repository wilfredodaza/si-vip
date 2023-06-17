<?php
namespace App\Controllers;

use App\Controllers\Api\Auth;
use App\Models\Resolution;
use App\Models\Customer;
use App\Models\Municipalities;
use App\Models\TypeContract;
use App\Models\TypeDocumentIdentifications;
use App\Models\TypeWorker;
use App\Models\SubTypeWorker;
use App\Models\Bank;
use App\Models\Company;
use App\Models\CustomerWorker;
use App\Models\PaymentMethod;
use App\Models\PayrollPeriod;
use App\Models\TypeAccountBank;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\User;

use App\Models\PersonalizationLaborCertificate;



use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;



class WorkerController extends BaseController
{
 
    /**
     * View de table
     * 
     * @return string()
     */

    public function index()
    {
        $model = new Customer();
        $model->select([
            'type_document_identifications.name as type_document_identification_name',
            'customers.*',
            'customers.id as customer_id',
            'customer_worker.*',
            'customers.deleted_at  as deleted_at'
        ])
        ->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
        ->where([
            'type_customer_id'          =>  3, 
            'customers.deleted_at'      =>  null,
            'customers.companies_id'    =>  Auth::querys()->companies_id,
            ]);
        
       /* echo json_encode($this->search());
        die();*/
        if(count($this->search()) != 0) {
            $model->where($this->search());
        }
     
        $workers =    $model->asObject();
        

        $model = new TypeDocumentIdentifications();
        $typeDocumentIdentifications = $model
        ->asObject()
        ->get()
        ->getResult();

        return view('workers/index', [
            'workers'                       => $workers->paginate(10),
            'pager'                         => $workers->pager,
            'typeDocumentIdentifications'   => $typeDocumentIdentifications,
            'search'                        => $this->search()
        ]); 
    }

    public function create() 
    {

        $model = new TypeDocumentIdentifications();
        $typeDocumentIdentifications = $model
        ->asObject()
        ->get()
        ->getResult();


        $model = new Municipalities();
        $municipalities = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new TypeContract();
        $typeContracts  = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new TypeWorker();
        $typeWorker  = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new SubTypeWorker();
        $subTypeWorker = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new Bank();
        $banks = $model
        ->asObject()
        ->get()
        ->getResult();


        $model = new TypeAccountBank();
        $typeAccountBanks = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new PaymentMethod();
        $paymentMethods = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new PayrollPeriod();
        $payrollPeriods = $model
        ->asObject()
        ->get()
        ->getResult();

     
        return view('workers/create', [
            'municipalities'                => $municipalities,
            'typeDocumentIdentifications'   => $typeDocumentIdentifications,
            'typeContracts'                 => $typeContracts,
            'typeWorkers'                   => $typeWorker,
            'subTypeWorkers'                => $subTypeWorker,
            'banks'                         => $banks,
            'typeAccountBanks'              => $typeAccountBanks,
            'paymentMethods'                => $paymentMethods,
            'payrollPeriods'                => $payrollPeriods
        ]); 
    }

    public function new()
    {
        $security = \Config\Services::security();


        
        $validation = service('validation');
        $validation->setRules([
            'first_name'                            => 'required|max_length[60]',
            'second_name'                           => 'max_length[60]',
            'surname'                               => 'required|max_length[60]',
            'second_surname'                        => 'max_length[60]',
            'type_document_identification_id'       => 'required|is_not_unique[type_document_identifications.id]',
            'identification_number'                 => 'required|max_length[45]',
            'municipality_id'                       => 'required|is_not_unique[municipalities.id]',
            'address'                               => 'required|max_length[100]',
            'email'                                 => 'permit_empty|valid_email|max_length[45]',
            'phone'                                 => 'permit_empty|max_length[45]',
            'payment_method_id'                     => 'required|is_not_unique[payment_methods.id]',
            /*'bank_id'                               => 'required|is_not_unique[banks.id]',
            'bank_account_type_id'                  => 'required|is_not_unique[bank_account_types.id]',
            'account_number'                        => 'required|max_length[45]',*/
            'type_contract_id'                      => 'required|is_not_unique[type_contracts.id]',
            'integral_salary'                       => 'required|in_list[No,Si]',
            'admision_date'                         => 'required|valid_date[Y-m-d]',
            'salary'                                => 'required|numeric|max_length[23]',
            'type_worker_id'                        => 'required|is_not_unique[type_workers.id]',
            'high_risk_pension'                     => 'required|in_list[No,Si]',
            'payroll_period_id'                     => 'required|is_not_unique[payroll_periods.id]',
            'sub_type_worker_id'                    => 'required|is_not_unique[sub_type_workers.id]',
            'work'                                  => 'permit_empty|max_length[45]',
            'worker_code'                           => 'permit_empty|max_length[45]'
        ],
        [
            'first_name' => [
                'required'      => 'El campo primer nombre es obligatorio.',
                'max_length'    => 'El campo primer nombre no puede exceder los 60 caracteres de longitud.'   
            ],
            'second_name' => [
                'max_length'    => 'El campo segundo nombre no puede exceder los 60 caracteres de longitud.'
            ],
            'surname' => [
                'required'      => 'El campo primer apellido es obligatorio.',
                'max_length'    => 'El campo primer apellido no puede exceder los 60 caracteres de longitud.'
            ],
            'second_surname'    => [
                'required'      => 'El campo segundo apellido es obligatorio.',
                'max_length'    => 'El campo segundo apellido no puede exceder los 60 caracteres de longitud.'
            ],
            'type_document_identification_id' => [
                'required'      => 'El campo tipo de identificación es obligatorio.',
            ],
            'identification_number' => [
                'required'      => 'El campo número de identificación es obligatorio.',
                'max_length'    => 'El campo número de identificación no puede exceder los 45 caracteres de longitud.',
                'is_unique'     => 'El número de identificación ya se encuentra registrado.'
            ],
            'municipality_id' => [
                'required'      => 'El campo municipios es obligatorio.',
            ],
            'address'   => [
                'required'      => 'El campo dirección es obligatorio.',
                'max_length'    => 'El campo dirección no puede exceder los 100 caracteres de longitud.'
            ],
            'email'     => [
                'required'      => 'El campo correo electrónico es obligatorio.',
                'max_length'    => 'El campo correo electrónico no puede exceder los 191 caracteres de longitud.',
                'valid_email'   => 'El correo electrónico es invalido.',
                'is_unique'     => 'El correo electrónico ya se encuentra registrado en el sistema.'
            ],
            'phone'     => [
                'max_length'    => 'El campo teléfono no puede exceder los 45 caracteres de longitud.'
            ],
            'type_contract_id' => [
                'required'      => 'El campo tipo de contrato es obligatorio.',
            ],
            'integral_salary' => [
                'required'     => 'El campo salario integral es obligatorio.'
            ],
            'admision_date' => [
                'required'  =>  'El campo fecha de contratación es obligatorio.'
            ],
            'salary'    => [
                'required'      => 'El campo salario es obligatorio.',
                'numeric'       => 'El campo salario es de tipo numérico.',
                'max_length'    => 'El campo salario no puede exceder los 23 caracteres de longitud.'
            ],
            'type_worker_id' => [
                'required'      => 'El campo tipo de trabajador es obligatorio.',
            ],
            'high_risk_pension' => [
                'required'      => 'El campo pensión de alto riesgo es obligatorio.',
            ],
            'payroll_period_id' => [
                'required'      => 'El campo frecuencia de pago es obligatorio.', 
            ],
            'sub_type_worker_id' => [
                'required'      => 'El campo subtipo de trabajador es obligatorio.', 
            ],
            'work' => [
                'required'      => 'El campo cargo es obligatorio.', 
                'max_length'    => 'El campo cargo no puede exceder los 45 caracteres de longitud.'
            ],
            'payment_method_id' => [
                'required'      => 'El campo método de pago es obligatorio.', 
            ],
           /* 'bank_id' => [
                'required'     => 'El campo banco es obligatorio.', 
            ],
            'bank_account_type_id' => [
                'required'     => 'El campo tipo de cuenta es obligatorio.', 
            ],
            'account_number' => [
                'required'     => 'El campo número de cuenta es obligatorio.',
                'max_length'   => 'El campo número de cuenta no puede exceder los 45 caracteres de longitud.'
            ],*/
            'worker_code' => [
                'max_length'   => 'El campo código del empleado no puede exceder los 45 caracteres de longitud.'
            ]
        ]);



        if(!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        
        


        if(!empty($this->request->getPost('email'))) {
            $model = new User();
            $user = $model->insert([
                'name'          => $this->request->getPost('first_name').' '. $this->request->getPost('second_name'). ' '.$this->request->getPost('surname').'  '.$this->request->getPost('second_surname')  ,
                'username'      => $this->request->getPost('email'),
                'email'         => $this->request->getPost('email'),
                'password'      => password_hash($this->request->getPost('identification_number'), PASSWORD_DEFAULT),
                'status'        => 'active',
                'role_id'       => 7,
                'companies_id'  => Auth::querys()->companies_id,
            ]);
        
        }else {
            $user = null;
        }
  

        $model = new Customer();
        $customer = $model->insert([
            'name'                              => $this->request->getPost('first_name'),
            'type_document_identifications_id'  => $this->request->getPost('type_document_identification_id'),
            'identification_number'             => $this->request->getPost('identification_number'),
            'municipality_id'                   => $this->request->getPost('municipality_id'),
            'address'                           => $this->request->getPost('address'),
            'email'                             => $this->request->getPost('email'),
            'companies_id'                      => Auth::querys()->companies_id,
            'phone'                             => $this->request->getPost('phone'),
            'type_customer_id'                  => 3,
            'user_id'                           => $user,
            'status'                            => 'activo',
        ]);

     





        
        $model = new CustomerWorker();
        $info =$model->insert([
            'customer_id'                       => $customer,
            'second_name'                       => $this->request->getPost('second_name'),
            'surname'                           => $this->request->getPost('surname'),
            'second_surname'                    => $this->request->getPost('second_surname'),
            'payment_method_id'                 => $this->request->getPost('payment_method_id'),
            'bank_id'                           => $this->request->getPost('bank_id'),
            'bank_account_type_id'              => $this->request->getPost('bank_account_type_id'),
            'account_number'                    => $this->request->getPost('account_number'),
            'type_contract_id'                  => $this->request->getPost('type_contract_id'),
            'integral_salary'                   => ($this->request->getPost('integral_salary') == 'Si' ? 'true' : 'false'),
            'admision_date'                     => $this->request->getPost('admision_date'),
            'retirement_date'                   => $this->request->getPost('retirement_date') == '' ? null : $this->request->getPost('retirement_date'),
            'salary'                            => $this->request->getPost('salary'),
            'type_worker_id'                    => $this->request->getPost('type_worker_id'),
            'high_risk_pension'                 => ($this->request->getPost('high_risk_pensiony') == 'Si' ? 'true' : 'false'),
            'sub_type_worker_id'                => $this->request->getPost('sub_type_worker_id'),
            'work'                              => $this->request->getPost('work'),
            'worker_code'                       => $this->request->getPost('worker_code'),
            'payroll_period_id'                 => $this->request->getPost('payroll_period_id'),
            'transportation_assistance'         => $this->request->getPost('transportation_assistance'),
            'non_salary_payment'                => $this->request->getPost('non_salary_payment'),
            'other_payments'                    => $this->request->getPost('other_payments')
        ]);

        if($info) {
            return redirect()->to(base_url('workers'))->with('success', 'El empleado fue creado correctamente.');
        }



        
    
        return redirect()->to('workers')->with('errors', $this->validator);

    }

    public function edit($id = null) 
    {

        $model = new TypeDocumentIdentifications();
        $typeDocumentIdentifications = $model
        ->asObject()
        ->get()
        ->getResult();


        $model = new Municipalities();
        $municipalities = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new TypeContract();
        $typeContracts  = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new TypeWorker();
        $typeWorker  = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new SubTypeWorker();
        $subTypeWorker = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new Bank();
        $banks = $model
        ->asObject()
        ->get()
        ->getResult();


        $model = new TypeAccountBank();
        $typeAccountBanks = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new PaymentMethod();
        $paymentMethods = $model
        ->asObject()
        ->get()
        ->getResult();

        $model = new PayrollPeriod();
        $payrollPeriods = $model
        ->asObject()
        ->get()
        ->getResult();


        $model = new Customer();
        $customer = $model
        ->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->where(['customers.id' => $id])
        ->asObject()
        ->first();

       

        return view('workers/edit', [
            'municipalities'                => $municipalities,
            'typeDocumentIdentifications'   => $typeDocumentIdentifications,
            'typeContracts'                 => $typeContracts,
            'typeWorkers'                   => $typeWorker,
            'subTypeWorkers'                => $subTypeWorker,
            'banks'                         => $banks,
            'typeAccountBanks'              => $typeAccountBanks,
            'paymentMethods'                => $paymentMethods,
            'payrollPeriods'                => $payrollPeriods,
            'customer'                      => $customer,
            'id'                            => $id
            ]);
    }

    public function update($id = null)
    {

     
        
    
        $validation = service('validation');
        $validation->setRules([
            'first_name'                            => 'required|max_length[60]',
            'second_name'                           => 'max_length[60]',
            'surname'                               => 'required|max_length[60]',
            'second_surname'                        => 'max_length[60]',
            'type_document_identification_id'       => 'required|is_not_unique[type_document_identifications.id]',
            'identification_number'                 => 'required|max_length[45]',
            'municipality_id'                       => 'required|is_not_unique[municipalities.id]',
            'address'                               => 'required|max_length[100]',
            'email'                                 => 'permit_empty|valid_email|max_length[190]',
            'phone'                                 => 'permit_empty|numeric|max_length[45]',
            'payment_method_id'                     => 'required|is_not_unique[payment_methods.id]',
           /* 'bank_id'                               => 'required|is_not_unique[banks.id]',
            'bank_account_type_id'                  => 'required|is_not_unique[bank_account_types.id]',
            'account_number'                        => 'required|max_length[45]',*/
            'type_contract_id'                      => 'required|is_not_unique[type_contracts.id]',
            'integral_salary'                       => 'required|in_list[No,Si]',
            'admision_date'                         => 'required|valid_date[Y-m-d]',
            'salary'                                => 'required|numeric|max_length[23]',
            'type_worker_id'                        => 'required|is_not_unique[type_workers.id]',
            'high_risk_pension'                     => 'required|in_list[No,Si]',
            'payroll_period_id'                     => 'required|is_not_unique[payroll_periods.id]',
            'sub_type_worker_id'                    => 'required|is_not_unique[sub_type_workers.id]',
            'work'                                  => 'permit_empty|max_length[45]',
            'worker_code'                           => 'permit_empty|max_length[45]'
        ],
        [
            'first_name' => [
                'required'      => 'El campo primer nombre es obligatorio.',
                'max_length'    => 'El campo primer nombre no puede exceder los 60 caracteres de longitud.'   
            ],
            'second_name' => [
                'max_length'    => 'El campo segundo nombre no puede exceder los 60 caracteres de longitud.'
            ],
            'surname' => [
                'required'      => 'El campo primer apellido es obligatorio.',
                'max_length'    => 'El campo primer apellido no puede exceder los 60 caracteres de longitud.'
            ],
            'second_surname'    => [
                'required'      => 'El campo segundo apellido es obligatorio.',
                'max_length'    => 'El campo segundo apellido no puede exceder los 60 caracteres de longitud.'
            ],
            'type_document_identification_id' => [
                'required'      => 'El campo tipo de identificación es obligatorio.',
            ],
            'identification_number' => [
                'required'      => 'El campo número de identificación es obligatorio.',
                'max_length'    => 'El campo número de identificación no puede exceder los 45 caracteres de longitud.',
                'is_unique'     => 'El número de identificación ya se encuentra registrado.'
            ],
            'municipality_id' => [
                'required'      => 'El campo municipios es obligatorio.',
            ],
            'address'   => [
                'required'      => 'El campo dirección es obligatorio.',
                'max_length'    => 'El campo dirección no puede exceder los 100 caracteres de longitud.'
            ],
            'email'     => [
                'required'      => 'El campo correo electrónico es obligatorio.',
                'max_length'    => 'El campo correo electrónico no puede exceder los 190 caracteres de longitud.',
                //'is_unique'     => 'El correo electrónico ya se encuentra registrado en el sistema.',
                'valid_email'   => 'El correo electrónico es invalido.',
            ],
            'phone'     => [
                'required'      => 'El campo teléfono es obligatorio.',
                'numeric'       => 'El campo es de tipo númerico.',
                'max_length'    => 'El campo teléfono no puede exceder los 45 caracteres de longitud.'
            ],
            'type_contract_id' => [
                'required'      => 'El campo tipo de contrato es obligatorio.',
            ],
            'integral_salary' => [
                'required'     => 'El campo salario integral es obligatorio.'
            ],
            'admision_date' => [
                'required'  =>  'El campo fecha de contratación es obligatorio.'
            ],
            'salary'    => [
                'required'      => 'El campo salario es obligatorio.',
                'max_length'    => 'El campo salario no puede exceder los 23 caracteres de longitud.'
            ],
            'type_worker_id' => [
                'required'      => 'El campo tipo de trabajador es obligatorio.',
            ],
            'high_risk_pension' => [
                'required'      => 'El campo pensión de alto riesgo es obligatorio.',
            ],
            'payroll_period_id' => [
                'required'      => 'El campo frecuencia de pago es obligatorio.', 
            ],
            'sub_type_worker_id' => [
                'required'      => 'El campo subtipo de trabajador es obligatorio.', 
            ],
            'work' => [
                'required'      => 'El campo cargo es obligatorio.', 
                'max_length'    => 'El campo cargo no puede exceder los 45 caracteres de longitud.'
            ],
            'payment_method_id' => [
                'required'      => 'El campo método de pago es obligatorio.', 
            ],
          /*  'bank_id' => [
                'required'     => 'El campo banco es obligatorio.', 
            ],
            'bank_account_type_id' => [
                'required'     => 'El campo tipo de cuenta es obligatorio.', 
            ],
            'account_number' => [
                'required'     => 'El campo número de cuenta es obligatorio.',
                'max_length'   => 'El campo número de cuenta no puede exceder los 45 caracteres de longitud.'
            ],*/
            'worker_code' => [
                'max_length'   => 'El campo código del empleado no puede exceder los 45 caracteres de longitud.',
                'is_unique'    => 'El código del empleado ya se encuentra registrado.'
            ]
        ]);



        if(!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new Customer();
        $customer = $model->asObject()->find($id);

        if($customer->email ==  null) {
            $model = new User();
            $user = $model->insert([
                'name'          => $this->request->getPost('first_name').' '. $this->request->getPost('second_name'). ' '.$this->request->getPost('surname').'  '.$this->request->getPost('second_surname')  ,
                'username'      => $this->request->getPost('email'),
                'email'         => $this->request->getPost('email'),
                'password'      => password_hash($this->request->getPost('identification_number'), PASSWORD_DEFAULT),
                'status'        => 'active',
                'role_id'       => 7,
                'companies_id'  => Auth::querys()->companies_id,
            ]);
        }
        

   

        $model = new Customer();
        $model->update($id, [
            'name'                              => $this->request->getPost('first_name'),
            'type_document_identifications_id'  => $this->request->getPost('type_document_identification_id'),
            'identification_number'             => $this->request->getPost('identification_number'),
            'municipality_id'                   => $this->request->getPost('municipality_id'),
            'address'                           => $this->request->getPost('address'),
            'email'                             => $this->request->getPost('email'),
            'phone'                             => $this->request->getPost('phone'),
            'companies_id'                      => Auth::querys()->companies_id
        ]);



        $model = new CustomerWorker();
        $customerWorker = $model->where(['customer_id' => $id])->asObject()->first();

    
        
        $model = new CustomerWorker();
        $info = $model->update($customerWorker->id, [
            'second_name'                       => $this->request->getPost('second_name'),
            'surname'                           => $this->request->getPost('surname'),
            'second_surname'                    => $this->request->getPost('second_surname'),
            'payment_method_id'                 => $this->request->getPost('payment_method_id'),
            'bank_id'                           => $this->request->getPost('bank_id'),
            'bank_account_type_id'              => $this->request->getPost('bank_account_type_id'),
            'account_number'                    => $this->request->getPost('account_number'),
            'type_contract_id'                  => $this->request->getPost('type_contract_id'),
            'integral_salary'                   => ($this->request->getPost('integral_salary') == 'Si' ? 'true' : 'false'),
            'admision_date'                     => $this->request->getPost('admision_date'),
            'retirement_date'                   => $this->request->getPost('retirement_date') == '' ? null : $this->request->getPost('retirement_date'),
            'salary'                            => $this->request->getPost('salary'),
            'type_worker_id'                    => $this->request->getPost('type_worker_id'),
            'high_risk_pension'                 => ($this->request->getPost('high_risk_pension') == 'Si' ? 'true' : 'false'),
            'sub_type_worker_id'                => $this->request->getPost('sub_type_worker_id'),
            'work'                              => $this->request->getPost('work'),
            'worker_code'                       => $this->request->getPost('worker_code'),
            'payroll_period_id'                 => $this->request->getPost('payroll_period_id'),
            'transportation_assistance'         => $this->request->getPost('transportation_assistance'),
            'non_salary_payment'                => $this->request->getPost('non_salary_payment'),
            'other_payments'                    => $this->request->getPost('other_payments')
        ]);

        if($info) {
            return redirect()->to(base_url('workers'))->with('success', 'El empleado fue actualizado correctamente.');
        }


    
        return redirect()->to(base_url('worker/edit/'.$id))->with('errors', $this->validator);

    }

    public function show($id = null) 
    {

        $personalization = new PersonalizationLaborCertificate();
        $validation =  $personalization->where(['company_id' => Auth::querys()->companies_id])->countAllResults();;



        $model = new Customer();
        $customer  = $model->select([
            'customers.id as customer_id',
            'customers.name as first_name',
            'customer_worker.second_name',
            'customer_worker.surname',
            'customer_worker.second_surname',
            'type_document_identifications.name as type_document_identification_name',
            'customers.identification_number',
            'municipalities.name as municipality_name',
            'customers.address',
            'customers.phone',
            'customers.email',
            'customer_worker.account_number',
            'banks.name as bank_name',
            'payment_methods.name payment_method_name',
            'bank_account_types.name as bank_account_type_name',
            'customer_worker.integral_salary',
            'customer_worker.admision_date',
            'type_workers.name as type_worker_name',
            'customer_worker.high_risk_pension',
            'type_contracts.name as type_contract_name',
            'sub_type_workers.name as sub_type_worker_name',
            'customer_worker.salary',
            'customer_worker.work',
	    'customer_worker.holidays',
            'customer_worker.court_date'

        ])
        ->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->join('municipalities', 'customers.municipality_id = municipalities.id')
        ->join('banks', 'banks.id = customer_worker.bank_id')
        ->join('type_document_identifications', 'type_document_identifications.id = customers.type_document_identifications_id' )
        ->join('payment_methods', 'payment_methods.id = customer_worker.payment_method_id' )
        ->join('bank_account_types', 'bank_account_types.id = customer_worker.bank_account_type_id' )
        ->join('type_contracts', 'type_contracts.id = customer_worker.type_contract_id' )
        ->join('type_workers', 'type_workers.id = customer_worker.type_worker_id' )
        ->join('sub_type_workers', 'sub_type_workers.id = customer_worker.sub_type_worker_id' )
        ->where(['customers.id' => $id])
        ->asObject()
        ->first();

        $model = new Invoice();
        $invoice = $model->select(['count(invoices.id) as payroll_count'])
        ->where([
            'invoices.customers_id'         => $id,
            'invoices.companies_id'         => Auth::querys()->companies_id,
            'invoices.invoice_status_id'    => 14,
            ])
            ->whereIn('invoices.type_documents_id', [9, 10])

        ->asObject()
        ->first();

        $model = new Payroll();
        $invoices = $model
        ->select([
            'pay.id',
            'periods.month',
            'periods.year',
            'invoices.id as invoice_id',
            'type_documents.name as type_document_name',
            'type_documents_id',
            'invoices.errors',
            'invoices.uuid',
            'pay.type_payroll_adjust_note_id',
            'invoices.resolution_credit',
            'invoice_status.id as invoice_status_id',
            'invoice_status.name as invoice_status_name',
            '(SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) 
            FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id) AS accrued',
            '(SELECT IFNULL(sum(ded2.payment),0) 
            FROM   payrolls pay2 LEFT JOIN  deductions ded2 ON  ded2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id) AS deduction' 
            ])
        ->from('payrolls as pay')
        ->join('invoices', 'invoices.id = pay.invoice_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
        ->join('periods', 'periods.id = pay.period_id')
        ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
        ->join('customers', 'customers.id = invoices.customers_id')
        ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
        ->where([
            'customers.id'                              => $id,
            'customers.companies_id'                    => Auth::querys()->companies_id,
            'customers.type_customer_id'                => 3,
            'customers.deleted_at'                      => null,
		'invoices.companies_id'			 => Auth::querys()->companies_id
        ])
        ->whereIn('invoices.type_documents_id', [9, 10])

        ->groupBy([
            'pay.id', 
            'invoice_status.id'
            ])
            ->orderBy('periods.id', 'desc')
            ->orderBy('invoices.created_at', 'DESC')
            ->asObject();

        $model = new Resolution();
        $resolutions = $model->where([
            'companies_id' => Auth::querys()->companies_id,
            'type_documents_id' => 9
        ])
            ->get()
            ->getResult();

   


        return view('workers/show', [
            'customer'      => $customer, 
            'invoices'      => $invoices->paginate(10),
            'pager'         => $invoices->pager,
            'invoice'       => $invoice,
            'resolutions'   => $resolutions,
            'validation'    => $validation
        ]);

    }

    public function changeStatus($id = null)
    {
        $model =  new Customer();
        $customer = $model->asObject()->find($id);


        if($customer->status == 'Activo') {
            $model =  new Customer();
            $model->update($id, ['status' => 'Inactivo']);
       
        }else {
            $model =  new Customer();
            $model->update($id, ['status' => 'Activo']);
        }
       
        return redirect()->to(base_url('workers'))->with('success', 'El estado del empleado fue actualizado.');
    }

    public function delete($id = null) 
    {
        $model = new CustomerWorker();
        $model->where(['customer_id' => $id])->delete();

        $model = new Customer();
        $model->delete($id);

        return redirect()->to(base_url('workers'))->with('success', 'El empleado fue eliminado correctamente.');
    }

    public function search()
    {
        $data = [];
        if(!empty($this->request->getGet('first_name'))) {
            $data['customers.name'] = $this->request->getGet('first_name');
        }

        if(!empty($this->request->getGet('second_name'))) {
            $data['customer_worker.second_name'] = $this->request->getGet('second_name');
        }
        if(!empty($this->request->getGet('surname'))) {
            $data['customer_worker.surname'] = $this->request->getGet('surname');
        }
        if(!empty($this->request->getGet('second_surname'))) {
            $data['customer_worker.second_surname'] = $this->request->getGet('second_surname');
        }

        if(!empty($this->request->getGet('type_document_id'))) { 
            $data['customers.type_document_identifications_id'] = $this->request->getGet('type_document_id');
        }

        if(!empty($this->request->getGet('identification_number'))) {
            $data['customers.identification_number'] = $this->request->getGet('identification_number');
        }
        if(!empty($this->request->getGet('status'))) {
            $data['customers.status'] = $this->request->getGet('status');
        }


        return $data;
    }

    public function export()
    {
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }

        $model = new Customer();
        $customers = $model
        ->select([
            'customers.name', 
            'customer_worker.second_name', 
            'customer_worker.surname',
            'customer_worker.second_surname',
            'customers.identification_number',
            'type_document_identifications.name as type_document_identification_name',
            'municipalities.name as municipality_name',
            'customers.address',
            'customers.email',
            'customers.phone',
            'type_contracts.name as type_contract_name',
            'customer_worker.integral_salary',
            'customer_worker.admision_date',
            'customer_worker.retirement_date',
            'customer_worker.salary',
            'type_workers.name as type_worker_name',
            'sub_type_workers.name as sub_type_worker_name',
            'customer_worker.high_risk_pension',
            'payroll_periods.name as payroll_period_name',
            'customer_worker.work',
            'customer_worker.worker_code',
            'payment_methods.name as payment_method_name',
            'banks.name as bank_name',
            'bank_account_types.name as bank_account_type_name',
            'customer_worker.account_number'
            ])
        ->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
        ->join('municipalities', 'customers.municipality_id = municipalities.id')
        ->join('type_contracts', 'customer_worker.type_contract_id = type_contracts.id')
        ->join('type_workers', 'customer_worker.type_worker_id = type_workers.id')
        ->join('sub_type_workers', 'customer_worker.sub_type_worker_id = sub_type_workers.id')
        ->join('payroll_periods', 'customer_worker.payroll_period_id = payroll_periods.id')
        ->join('payment_methods', 'customer_worker.payment_method_id = payment_methods.id')
        ->join('banks', 'customer_worker.bank_id = banks.id')
        ->join('bank_account_types', 'customer_worker.bank_account_type_id = bank_account_types.id')
        ->where(['customers.companies_id' => Auth::querys()->companies_id, 'customers.type_customer_id' => 3])
        ->get()
        ->getResult();


        $model = new Company();
        $companies = $model->asObject()->find(Auth::querys()->companies_id);




        //echo json_encode($customers);die();

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Empleados')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');



        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Empresa')->getStyle('A2')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Identificación')->getStyle('A3')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', 'Fecha de reporte')->getStyle('A4')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', 'Fecha de generación')->getStyle('A5')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', 'Software de Facturación')->getStyle('A6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B2', $companies->company)
            ->setCellValue('B3', number_format($companies->identification_number, 0, '.', '.'))
            ->setCellValue('B4', date('Y-m-d H:i:s'))
            ->setCellValue('B5', date('Y-m-d H:i:s'))
            ->setCellValue('B6','MiFacturaLegal.com');

            

        $spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->getColor()->setARGB('FF2874A6');
        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->getColor()->setARGB('FF2874A6');
        $spreadsheet->setActiveSheetIndex(0)->getStyle('B6')->getFont()->setBold(true);

        $spreadsheet->getActiveSheet()->setShowGridlines(false);


        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFD7DBDD',
                ],

            ]
        ];

        $spreadsheet->getActiveSheet()->getStyle('A8:Z8')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('X')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);



        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A8', '#')
        ->setCellValue('B8', 'Primer Nombre')
        ->setCellValue('C8', 'Segundo Nombre')
        ->setCellValue('D8', 'Primer Apellido')
        ->setCellValue('E8', 'Segundo Apellido')
        ->setCellValue('F8', 'Tipo de Identificación')
        ->setCellValue('G8', 'Numero de Identificación')
        ->setCellValue('H8', 'Municipio')
        ->setCellValue('I8', 'Dirección')
        ->setCellValue('J8', 'Correo Electrónico')
        ->setCellValue('K8', 'Teléfono')
        ->setCellValue('L8', 'Tipo de Contrato')
        ->setCellValue('M8', 'Salario Integral')
        ->setCellValue('N8', 'Fecha de Admisión')
        ->setCellValue('O8', 'Fecha de Retiro')
        ->setCellValue('P8', 'Salario')
        ->setCellValue('Q8', 'Tipo de Trabajo')
        ->setCellValue('R8', 'Subtipo de Trabajo')
        ->setCellValue('S8', 'Pensión de Alto Riesgo')
        ->setCellValue('T8', 'Periodo de Nomina')
        ->setCellValue('U8', 'Cargo')
        ->setCellValue('V8', 'Código del empleado')
        ->setCellValue('W8', 'Método de Pago')
        ->setCellValue('X8', 'Banco')
        ->setCellValue('Y8', 'Tipo de Cuenta')
        ->setCellValue('Z8', 'Número de Cuenta');




    
            $l = 1;
            $i = 9;
            foreach ($customers as $item) {
                $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $l)
                ->setCellValue('B' . $i, $item->name)
                ->setCellValue('C' . $i, $item->second_name)
                ->setCellValue('D' . $i, $item->surname)
                ->setCellValue('E' . $i, $item->second_surname)
                ->setCellValue('F' . $i, $item->type_document_identification_name)
                ->setCellValue('G' . $i, $item->identification_number)
                ->setCellValue('H' . $i, $item->municipality_name)
                ->setCellValue('I' . $i, $item->address)
                ->setCellValue('J' . $i, $item->email)
                ->setCellValue('K' . $i, $item->phone)
                ->setCellValue('L' . $i, $item->type_contract_name)
                ->setCellValue('M' . $i, $item->integral_salary == 'false' ? 'No': 'Si')
                ->setCellValue('N' . $i, $item->admision_date)
                ->setCellValue('O' . $i, $item->retirement_date)
                ->setCellValue('P' . $i, number_format($item->salary, '2', '.', ','))
                ->setCellValue('Q' . $i, $item->type_worker_name)
                ->setCellValue('R' . $i, $item->sub_type_worker_name)
                ->setCellValue('S' . $i, $item->high_risk_pension == 'false' ? 'No': 'Si')
                ->setCellValue('T' . $i, $item->payroll_period_name)
                ->setCellValue('U' . $i, $item->work)
                ->setCellValue('V' . $i, $item->worker_code)
                ->setCellValue('W' . $i, $item->payment_method_name)
                ->setCellValue('X' . $i, $item->bank_name)
                ->setCellValue('Y' . $i, $item->bank_account_type_name)
                ->setCellValue('Z' . $i, $item->account_number);
                $i++;
                $l++;
                //number_format(($item->payable_amount - $item->tax_exclusive_amount), '2', '.', ','))
            }


            $spreadsheet->getActiveSheet()->setTitle('Empleados');
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Emplados.xls"');
            header('Cache-Control: max-age=0');
            header('Cache-Control: max-age=1');
    
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: cache, must-revalidate');
            header('Pragma: public');
    
            $writer = IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
            die();
    }

    public function import()
    {

    
        if ($file = $this->request->getFile('file')) {
         
            if($file->getClientExtension() == 'xlsx') {
                $reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                $reader->setShouldFormatDates(true);
                $reader->open($file->getTempName());
        
                $count = 0;
                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        if($count > 0) {
                        $cells = $row->getCells();
                           
                            $model = new Customer();
                            $customer = $model->insert([
                                'name'                                  => $cells[0],
                                'type_document_identifications_id'      => $cells[4],
                                'identification_number'                 => $cells[5],
                                'phone'                                 => $cells[9],
                                'address'                               => $cells[7],
                                'email'                                 => $cells[8],
                                'type_customer_id'                      => 3,
                                'municipality_id'                       => $cells[6],
                                'companies_id'                          => Auth::querys()->companies_id,
                                'status'                                => 'Activo',
                            ]);
                 
                            $model = new CustomerWorker();
                            $model->insert([
                                'customer_id' =>  $customer,
                                'second_name'                       => $cells[1],
                                'surname'                           => $cells[2],
                                'second_surname'                    => $cells[3],
                                'type_contract_id'                  => $cells[15],
                                'high_risk_pension'                 => $cells[17],
                                'type_worker_id'                    => $cells[15],
                                'integral_salary'                   => $cells[11],
                                'admision_date'                     => $cells[12],
                                'retirement_date'                   => $cells[13],
                                'sub_type_worker_id'                => $cells[16],
                                'salary'                            => $cells[14],
                                'work'                              => $cells[19],
                                'worker_code'                       => $cells[20],
                                'bank_id'                           => $cells[22],
                                'payment_method_id'                 => $cells[21],
                                'bank_account_type_id'              => $cells[23],
                                'account_number'                    => $cells[24],
                                'payroll_period_id'                 => $cells[18] 
                            ]);
                        }
                        $count++;
                    }
                }  

            }else {
                return redirect()->with('warning', 'El formato del Excel xls');
            }
        }
    }


    

}