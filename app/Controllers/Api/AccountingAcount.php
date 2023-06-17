<?php


namespace App\Controllers\Api;


use CodeIgniter\RESTful\ResourceController;

class AccountingAcount extends ResourceController
{
    protected $format = 'json';

    public function entryCredit()
    {
        $accountingAcount = new \App\Models\AccountingAcount();
        $data = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'nature'                        =>  'Crédito',
            'type_accounting_account_id'    =>  '1'
            ])
            ->get()
            ->getResult();

        return $this->respond([
            'status'    => 200,
            'data'      => $data
        ], 200);
    }

    public function entryDebit()
    {
        $accountingAcount = new \App\Models\AccountingAcount();
        $data = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'nature'                        =>  'Débito',
            'type_accounting_account_id'    =>  '1'
        ])
            ->get()
            ->getResult();

       return  $this->respond([
            'status'    => 200,
            'data'      => $data
        ], 200);
    }

    public function taxPay()
    {
        $accountingAcount = new \App\Models\AccountingAcount();
        $data = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'type_accounting_account_id'    =>  '2'
        ])
            ->get()
            ->getResult();

        return $this->respond([
            'status'    => 200,
            'data'      => $data
        ], 200);
    }


    public function taxAdvance()
    {
        $accountingAcount = new \App\Models\AccountingAcount();
        $data = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'type_accounting_account_id'    =>  '3'
        ])
            ->get()
            ->getResult();

        return $this->respond([
            'status'    => 200,
            'data'      => $data
        ], 200);
    }

    public function accountPay()
    {
        $accountingAcount = new \App\Models\AccountingAcount();
        $data = $accountingAcount->where([
            'companies_id'                  =>  Auth::querys()->companies_id,
            'type_accounting_account_id'    =>  '4'
        ])
            ->get()
            ->getResult();

        return $this->respond([
            'status'    => 200,
            'data'      => $data
        ], 200);
    }


}