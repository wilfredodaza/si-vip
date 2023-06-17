<?php

namespace App\Controllers\Api;

use App\Models\User as Users;
use App\Models\Customer;

use CodeIgniter\RESTful\ResourceController;


class User extends ResourceController
{
    protected $format = 'json';


    public function index()
    {
        $user = new Users();
        $users = $user->where(['companies_id' => Auth::querys()->companies_id, 'role_id !=' => 5])->get()->getResult();
        return $this->respond(['status' => 200, 'data' => $users]);
    }

    public function create()
    {}

    public function show($id = null)
    {
        if(Auth::querys()->role_id == 5) {
            $customers = new Customer();
            $customer = $customers
            ->where(['email' => Auth::querys()->username])
            ->first();
  
            $customer['companyId'] = Auth::querys()->companies_id;
            $customer['roleId'] = Auth::querys()->role_id;
            return $this->respond(['status' => 200, 'data' => $customer]);
        }else if(Auth::querys()->role_id == 2 || Auth::querys()->role_id == 3)  {
            $users = new Users();
            $user = $users
            ->select([
                'users.role_id as roleId',
                'users.id',
                'users.name',
                'companies.type_document_identifications_id',
                'companies.identification_number',
                'companies.dv',
                'companies.phone',
                'companies.address',
                'companies.email',
                'companies.merchant_registration',
                'companies.type_regimes_id',
                'companies.municipalities_id',
                'companies.type_organizations_id',
                'companies.id  as companyId',
                'companies.municipalities_id as municipality_id'
            ])
            ->where(['users.id' => Auth::querys()->id])    
            ->join('companies', 'companies.id = users.companies_id')
            ->first();
            return $this->respond(['status' => 200, 'data' => $user]);
            
        }
       
    }
}