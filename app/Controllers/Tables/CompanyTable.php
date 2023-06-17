<?php
namespace App\Controllers\Tables;

use App\Models\Company;
use App\Models\Customer;
use App\Traits\Grocery;
use Config\Services;


class CompanyTable
{

    use Grocery;

    protected  $hidden = [
        'token',
        'created_at',
        'updated_at'
    ];

    protected  $columns = [
        'company',
        'identification_number',
        'dv',
        'email',
        'phone',
        'type_environments_id',
        'type_environment_payroll_id',
        'headquarters_id'
    ];


    public function relations() {
        $this->crudTable->setRelation('taxes_id', 'taxes', '{code} -  {name}');
        $this->crudTable->setRelation('type_currencies_id', 'type_currencies', '{code} -  {name}');
        $this->crudTable->setRelation('type_liabilities_id', 'type_liabilities', '{code} - {name}');
        $this->crudTable->setRelation('type_organizations_id', 'type_organizations', '{code} - {name}');
        $this->crudTable->setRelation('type_document_identifications_id', 'type_document_identifications', '{code} - {name}');
        $this->crudTable->setRelation('municipalities_id', 'municipalities', '{code} - {name}');
        $this->crudTable->setRelation('departments_id', 'departments', '{code} - {name}');
        $this->crudTable->setRelation('departments_id', 'departments', '{code} - {name}');
        $this->crudTable->setRelation('languages_id', 'languages', '{code} - {name}');
        $this->crudTable->setRelation('type_environments_id', 'type_environments', '{code} - {name}');
        $this->crudTable->setRelation('type_environment_payroll_id', 'type_environments', '{code} - {name}');
        $this->crudTable->setRelation('type_operations_id', 'type_operations', '{code} - {name}');
        $this->crudTable->setRelation('countries_id', 'countries', '{code} - {name}');
        $this->crudTable->setRelation('type_regimes_id', 'type_regimes', '{code} - {name}');
        $this->crudTable->setRelation('template_pdf_id', 'template_pdf', 'name');
        $this->crudTable->setRelation('type_company_id', 'type_companies', 'name');
        $this->crudTable->setRelation('headquarters_id', 'headquarters', '{id} -  {name}');
    }


    public function rules()
    {
        $this->crudTable->setRule('taxes_id', 'required');
        $this->crudTable->setRule('type_currencies_id', 'required');
        $this->crudTable->setRule('type_liabilities_id', 'required');
        //$this->crudTable->setRule('type_organizations_id', 'required');
        $this->crudTable->setRule('type_document_identifications_id', 'required');
        $this->crudTable->setRule('countries_id', 'required');
        $this->crudTable->setRule('departments_id', 'required');
        $this->crudTable->setRule('municipalities_id', 'required');
        $this->crudTable->setRule('languages_id', 'required');
        $this->crudTable->setRule('type_operations_id', 'required');
        $this->crudTable->setRule('type_regimes_id', 'required');
        $this->crudTable->setRule('type_environments_id', 'required');
        $this->crudTable->setRule('template_pdf_id', 'required');
        $this->crudTable->setRule('type_environment_payroll_id', 'required');
        $this->crudTable->setRule('company', 'required');
        $this->crudTable->setRule('company', 'lengthBetween', ['1' , '191']);
        $this->crudTable->setRule('identification_number', 'required');
        $this->crudTable->setRule('identification_number',  'lengthBetween', ['1' , '20']);
        $this->crudTable->setRule('dv', 'required');
        $this->crudTable->setRule('dv', 'min', '0');
        $this->crudTable->setRule('dv', 'max', '9');
        $this->crudTable->setRule('address', 'required');
        $this->crudTable->setRule('address',  'lengthBetween', ['1' , '191']);
        $this->crudTable->setRule('email', 'required');
        $this->crudTable->setRule('email', 'email');
        $this->crudTable->setRule('email',  'lengthBetween', ['1' , '100']);
        $this->crudTable->setRule('phone', 'required');
        $this->crudTable->setRule('phone', 'lengthBetween', ['1' , '30']);
        $this->crudTable->setRule('merchant_registration', 'required');
        $this->crudTable->setRule('merchant_registration', 'lengthBetween', ['1' , '30']);
        $this->crudTable->setRule('testId', 'lengthBetween', ['0' , '255']);
        $this->crudTable->setRule('headquarters_id', 'required');
    }

    public function fieldType()
    {
        $this->crudTable->fieldType('dv', 'numeric');
        $this->crudTable->fieldType('email', 'email');
    }

    public function callback()
    {

        $this->crudTable->callbackAfterUpdate(function ($data) {
            if($data->data['headquarters_id'] == 1){
                $this->companies($data, 'put');
                return $data;
            }
        });

        $this->crudTable->callbackAfterInsert(function ($data) {
            if($data->data['headquarters_id'] == 1){
                $this->companies($data, 'post');
                return $data;
            }else{
                $dataCustomers = [];
                $customers = new Customer();
                $companiesModel = new Company();
                $companies = $companiesModel
                    ->where(['identification_number' => $data->data['identification_number'] ])
                    ->asObject()->get()->getResult();
                foreach($companies as $company){
                    $customer = (object)[
                        'name' => $company->company,
                        'type_document_identifications_id' => $company->type_document_identifications_id,
                        'identification_number' => $company->identification_number,
                        'dv' => $company->dv,
                        'phone' => $company->phone,
                        'address' => $company->address,
                        'email' => $company->email,
                        'merchant_registration' => $company->merchant_registration,
                        'type_customer_id' => 2,
                        'type_regime_id' => $company->type_regimes_id,
                        'municipality_id' => $company->municipalities_id,
                        'type_organization_id' => $company->type_organizations_id,
                        'status' => 'Activo',
                        'headquarters_id' => $company->id
                    ];
                    array_push($dataCustomers,$customer);
                }
                foreach($companies as $company){
                    foreach($dataCustomers as $dataCustomer){
                        if($company->id != $dataCustomer->headquarters_id){
                            $existsCustomer = $customers->where(['companies_id' => $company->id, 'headquarters_id' => $dataCustomer->headquarters_id])->asObject()->first();
                            if(is_null($existsCustomer)){
                                $dataCustomer->companies_id = $company->id;
                                $customers->save($dataCustomer);
                            }
                        }
                    }
                }
            }
        });
    }




    private function companies($data, $method)
    {
        $client = Services::curlrequest();
        $client->setHeader('Content-Type', 'application/json');
        $client->setHeader('Accept', 'application/json');

        $res = $client->post(
            getenv('API').'/ubl2.1/config/'. $data->data['identification_number'] . "/" . $data->data['dv'], [
                'form_params' => [
                    'type_document_identification_id'   => $data->data['type_document_identifications_id'],
                    'type_organization_id'              => $data->data['type_organizations_id'],
                    'type_regime_id'                    => $data->data['type_regimes_id'],
                    'type_liability_id'                 => $data->data['type_liabilities_id'],
                    'business_name'                     => $data->data['company'],
                    'merchant_registration'             => $data->data['merchant_registration'],
                    'municipality_id'                   => $data->data['municipalities_id'],
                    'address'                           => $data->data['address'],
                    'phone'                             => $data->data['phone'],
                    'email'                             => $data->data['email']
                ],
            ]
        );

        $json = json_decode($res->getBody());
        if (!isset($json->errors)) {
            if($method == 'post') {
                $this->updateToken($data->insertId, $json->token);
            }else {
                $this->updateToken($data->primaryKeyValue, $json->token);
            }
        } else {
            echo json_encode($json);
            die();
        }
    }

    private function updateToken(int $id, string $token)
    {
        $companies = new Company();
        $companies->set(['token' => $token])->where(['id' => $id])->update();
    }

}