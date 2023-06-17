<?php


namespace App\Controllers\Api;

use App\Controllers\Api\Auth;
use App\Controllers\HeadquartersController;
use App\Controllers\WalletController;
use App\Models\Customer;
use App\Models\Invoice;
use App\Traits\ApiTrait;
use App\Traits\TransformTrait;
use App\Traits\ValidationsTrait;
use CodeIgniter\RESTful\ResourceController;

class Customers extends ResourceController
{
    use ApiTrait, TransformTrait, ValidationsTrait;

    protected $format = 'json';

    public function list()
    {
        $customer = new Customer();
        $customer->where(['companies_id' => Auth::querys()->companies_id])->get()->getResult();
        $items = $this->pagination($customer, 'customers')
            ->select('customers.*,
            type_document_identifications.id  as type_document_identifications_id,
            type_document_identifications.name as type_document_identifications_name,
            type_document_identifications.code as type_document_identifications_code,
            type_customer.id as type_customer_id,
            type_customer.name as type_customer_name,
            type_regimes.id as type_regimes_id,
            type_regimes.name as type_regimes_name,
            type_regimes.code as type_regimes_code,
            municipalities.id as municipalities_id,
            municipalities.name as municipalities_name,
            municipalities.code as municipalities_code,
            type_organizations.id as type_organizations_id,
            type_organizations.name as type_organizations_name,
            type_organizations.code as type_organizations_code
           ')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('type_customer', 'customers.type_customer_id = type_customer.id')
            ->join('type_regimes', 'customers.type_regime_id = type_regimes.id')
            ->join('municipalities', 'customers.municipality_id = municipalities.id')
            ->join('type_organizations', 'customers.type_organization_id = type_organizations.id')
            ->get()
            ->getResult();

        $dates = [];
        foreach ($items as $item) {
            $data = $this->_transform($item);
            array_push($dates, $data);
        }

        return $this->respond([
            'status' => 200,
            'message' => 'success',
            'data' => $dates,
            'pagination' => $this->paginate

        ], 200);
    }

    public function index()
    {
        $ids = [];
        $idsValidate= [];
        $headquartersController = new HeadquartersController();
        $controllerWallet = new WalletController();
        $customers = new Customer();
        $query = [
            'status' => 'Activo',
            'type_customer_id <'    => 3,
        ];

        switch ($this->request->getGet('type_customer_id')) {
            case 1:
                $query['type_customer_id'] = 1;
                break;
            case 2:
                $query['type_customer_id'] = 2;
                break;
        }
        $customers = $customers->where($query)
            ->select([
                'customers.*',
                'payment_policies.days'
            ])
            ->join('payment_policies', 'customers.payment_policies = payment_policies.id', 'left')
            ->whereIn('companies_id',$headquartersController->idsCompaniesHeadquarters(Auth::querys()->companies_id))
            ->whereNotIn('name', ['gerente', 'Gerente'])
            ->get()->getResult();
        foreach ($customers as $key => $customer) {
            $customer->debt = $controllerWallet->totalPerson($customer->id);
            if(!is_null($customer->headquarters_id)) {
                if (in_array($customer->headquarters_id, $idsValidate)) {
                    array_push($ids, $key);
                }else{
                    array_push($idsValidate, $customer->headquarters_id);
                }
            }
        }
       //echo json_encode();die();
        foreach($ids as $key => $id){
            unset($customers[$id]);
        }
        $customers = array_values($customers);
        //$customers = $customers->where(['companies_id' => 1])->get()->getResult();
        return $this->respond($customers);
    }


    public function providers()
    {
        $customers = new Customer();
        $customers = $customers->where(['companies_id' => Auth::querys()->companies_id, 'type_customer_id' => 2])
            ->get()
            ->getResult();
        return $this->respond([
            'status' => 200,
            'message' => 'success',
            'data' => $customers
        ], 200);

    }


    public function store()
    {
        $customers = new Customer();
        $json = file_get_contents('php://input');
        $customer = json_decode($json);
        $data = [

            'name' => $customer->name,
            'type_document_identifications_id' => $customer->type_document_identifications_id,
            'identification_number' => $customer->identification_number,
            'dv' => '0',
            'phone' => $customer->phone,
            'address' => $customer->address,
            'email' => $customer->email,
            'merchant_registration' => $customer->merchant_registration,
            'type_customer_id' => $customer->type_customer_id,
            'type_regime_id' => $customer->type_regime_id,
            'municipality_id' => $customer->municipality_id,
            'companies_id' => session('user')->companies_id,
            //'companies_id' => 1, 
            'type_organization_id' => $customer->type_organization_id
        ];

        $dato = $customers->insert($data);

        if ($dato != 0) {
            return $this->respond(
                [
                    'status' => '201',
                    'message' => 'Created.',
                    'data' => $data,
                    'id' => $dato
                ], 201);
        } else {
            return $this->respond([
                'status' => '400',
                'message' => 'Bat Request'
            ], 400);
        }
    }


    public function create()
    {

        $data = $this->request->getJSON();


        $transform = self::data($data, [
            'name' => 'name',
            'identificationNumber' => 'identification_number',
            'typeDocumentIdentificationId' => 'type_document_identifications_id',
            'phone' => 'phone',
            'address' => 'address',
            'email' => 'email',
            'merchantRegistration' => 'merchant_registration',
            'typeCustomerId' => 'type_customer_id',
            'typeRegimeId' => 'type_regime_id',
            'municipalityId' => 'municipality_id',
            'typeOrganizationId' => 'type_organization_id'
        ]);

        $transform['dv'] = $this->_calcularDV($transform['identification_number']);
        $transform['companies_id'] = Auth::querys()->companies_id;

        $customers = new Customer();
        if ($customers->save($transform)) {
            $customer = new Customer();
            $customer->where(['companies_id' => Auth::querys()->companies_id])->get()->getResult();
            $items = $this->pagination($customer, 'customers')
                ->select('customers.*,
            type_document_identifications.id  as type_document_identifications_id,
            type_document_identifications.name as type_document_identifications_name,
            type_document_identifications.code as type_document_identifications_code,
            type_customer.id as type_customer_id,
            type_customer.name as type_customer_name,
            type_regimes.id as type_regimes_id,
            type_regimes.name as type_regimes_name,
            type_regimes.code as type_regimes_code,
            municipalities.id as municipalities_id,
            municipalities.name as municipalities_name,
            municipalities.code as municipalities_code,
            type_organizations.id as type_organizations_id,
            type_organizations.name as type_organizations_name,
            type_organizations.code as type_organizations_code
            ')
                ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
                ->join('type_customer', 'customers.type_customer_id = type_customer.id')
                ->join('type_regimes', 'customers.type_regime_id = type_regimes.id')
                ->join('municipalities', 'customers.municipality_id = municipalities.id')
                ->join('type_organizations', 'customers.type_organization_id = type_organizations.id')
                ->where(['customers.id' => $customers->getInsertID()])
                ->get()
                ->getResult();

        }
        $data = $this->_transform($items[0]);
        $data['_id'] = $customers->getInsertID();
        return $this->respond([
            'status' => 201,
            'message' => 'Created',
            'data' => $data
        ], 201);
    }

    private function _transform($data)
    {
        return self::data($data, [
            'id' => '_id',
            'name' => 'name',
            'type_document_identifications_id' => [
                'typeDocumentIdentifiacionId' => self::data($data, [
                    'type_document_identifications_id' => '_id',
                    'type_document_identifications_name' => 'name',
                    'type_document_identifications_code' => 'code',
                ])
            ],
            'identification_number' => 'identification_number',
            'dv' => 'dv',
            'phone' => 'phone',
            'address' => 'address',
            'email' => 'email',
            'email2' => 'email2',
            'email3' => 'email3',
            'merchant_registration' => 'merchantRegistration',
            'type_customer_id' => [
                'typeCustomerId' => self::data($data, [
                    'type_customer_id' => '_id',
                    'type_customer_name' => 'name'
                ])
            ],
            'type_regime_id' => [
                'typeRegimeId' => self::data($data, [
                    'type_regimes_id' => '_id',
                    'type_regimes_name' => 'name',
                    'type_regimes_code' => 'code'
                ])
            ],
            'municipality_id' => [
                'municipalityId' => self::data($data, [
                    'municipalities_id' => '_id',
                    'municipalities_name' => 'name',
                    'municipalities_code' => 'code'
                ])
            ],
            'type_organization_id' => [
                'typeOrganizationId' => self::data($data, [
                    'type_organizations_id' => '_id',
                    'type_organizations_name' => 'name',
                    'type_organizations_code' => 'code',
                ])
            ]
        ]);


    }



    private function _calcularDV($nit) {

        if (!is_numeric($nit)) {
            return 0;
        }

        $arr = array(1 => 3, 4 => 17, 7 => 29, 10 => 43, 13 => 59, 2 => 7, 5 => 19,
            8 => 37, 11 => 47, 14 => 67, 3 => 13, 6 => 23, 9 => 41, 12 => 53, 15 => 71);
        $x = 0;
        $y = 0;
        $z = strlen($nit);
        $dv = '';

        for ($i = 0; $i < $z; $i++) {
            $y = substr($nit, $i, 1);
            $x += ($y * $arr[$z - $i]);
        }

        $y = $x % 11;

        if ($y > 1) {
            $dv = 11 - $y;
            return $dv;


        } else {
            $dv = $y;
            return $dv;
        }
    }
}