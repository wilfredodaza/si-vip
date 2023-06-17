<?php


namespace App\Controllers\Api;


use App\Models\Invoice;



class NoteDebit extends ServiceController
{
    public function create()
    {
        $json = file_get_contents('php://input');
        $invoice = json_decode($json);


        $data = [
            'resolution'                => $invoice->number,
            'resolution_id'             => $invoice->resolution_id,
            'resolution_credit'         => $invoice->billing_reference->number,
            'issue_date'                => $invoice->billing_reference->issue_date,
            'uuid'                      => $invoice->billing_reference->uuid,
            'type_documents_id'         => $invoice->type_document_id,
            'line_extesion_amount'      => $invoice->requested_monetary_totals->line_extension_amount,
            'tax_exclusive_amount'      => $invoice->requested_monetary_totals->tax_exclusive_amount,
            'tax_inclusive_amount'      => $invoice->requested_monetary_totals->tax_inclusive_amount,
            'allowance_total_amount'    => $invoice->requested_monetary_totals->allowance_total_amount,
            'charge_total_amount'       => $invoice->requested_monetary_totals->charge_total_amount,
            'payable_amount'            => $invoice->requested_monetary_totals->payable_amount,
            'customers_id'              => $invoice->customer->id,
            'created_at'                => date('Y-m-d H:i:s'),
            'invoice_status_id'         => 1,
            'companies_id'              => Auth::querys()->companies_id,
            'user_id'                   => Auth::querys()->id,
            'notes'                     => $invoice->notes,
            'idcurrency'                => isset($invoice->idcurrency) ? $invoice->idcurrency : 35,
            'calculationrate'           => isset($invoice->calculationrate) ? $invoice->calculationrate : 1,
            'calculationratedate'       => isset($invoice->calculationratedate) ? $invoice->calculationratedate: date('Y-m-d'),
        ];
        $invoiceLines = $invoice->debit_note_lines;
        $invoice  = new Invoice();
        $idInvoice = $invoice->insert($data);
        $this->_lineInvoice($invoiceLines, $idInvoice);

        if ($idInvoice) {
            http_response_code(201);
            return json_encode(['status' => 'ok', 'message' => 'Guardado Correctamente.']);
        }
    }

    public function resolution()
    {
       return  $this->_resolution(5);
    }

    public function invoice($id)
    {
        $this->_getInvoice($id);
    }

    public function line_invoice($id)
    {
        $this->_getLineInvoice($id, $type = 2);
    }

}