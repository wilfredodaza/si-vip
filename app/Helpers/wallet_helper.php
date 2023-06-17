<?php


use App\Models\Invoice;

function validateWallet($resolution)
{
    $invoice = new Invoice();

    $invoices = $invoice->where([
        'resolution_credit' => $resolution,
        'companies_id'      => session('user')->companies_id,
        'invoice_status_id >='=> 2
    ])
        ->asObject()
        ->orderBy('resolution_credit', 'desc')
        ->get()
        ->getResult();

    $data = [];
    if(count($invoices) > 0 ){
        foreach($invoices as $invoice) {
            array_push($data, [
                'payable_amount' => $invoice->payable_amount,
                'type_document_id' => $invoice->type_documents_id,
                'data' => $invoice
            ]);
        }
        return $data;
    }else {
        return null;
    }


}
function statusPay($id){
    $invoice = new Invoice();
    $invoice->set(['status_wallet' => 'Paga'])->where(['id' => $id])->update();
}