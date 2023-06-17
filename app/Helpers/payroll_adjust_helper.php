<?php


use App\Models\Invoice;
use App\Controllers\Api\Auth;

function previsualizationPdf($id) {
    $model = new Invoice();
    $removable = $model
    ->select(['payrolls.period_id', 'invoices.id', 'customers.identification_number'])
    ->join('customers', 'customers.id = invoices.customers_id')
    ->join('payrolls',  'payrolls.invoice_id = invoices.id')
    ->where([
        'invoices.companies_id'          =>  Auth::querys()->companies_id,
        'customers.companies_id'         =>  Auth::querys()->companies_id,
        'invoices.type_documents_id'     =>  109,
        'invoices.id'                    =>  $id
    ])
    ->orderBy('invoices.id', 'desc')
    ->asObject()
    ->first();



    $model = new Invoice();
    $removableTrim = $model
    ->select(['invoices.id'])
    ->join('customers', 'customers.id = invoices.customers_id')
    ->join('payrolls',  'payrolls.invoice_id = invoices.id')
    ->where([
        'customers.identification_number'            =>  $removable->identification_number,
        'invoices.companies_id'                      =>  Auth::querys()->companies_id,
        'customers.companies_id'                     =>  Auth::querys()->companies_id,
        'payrolls.period_id'                         =>  $removable->period_id,
        'invoices.type_documents_id'                 =>  110
    ])
    ->orderBy('invoices.id', 'DESC')
    ->asObject()
    ->first();

    if(!is_null($removableTrim)) {
        return $removableTrim->id;
    }else {
        return $removable->id;
    }

}
