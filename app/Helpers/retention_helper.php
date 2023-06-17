<?php


function retentions($id_invoice)
{
    $linetax = new \App\Models\LineInvoiceTax();
    $lineinvoicesTax = $linetax->select('line_invoice_taxs.tax_amount as retention, line_invoice_taxs.taxes_id')->where(['line_invoices.invoices_id' => $id_invoice])
        ->join('line_invoices', 'line_invoices.id = line_invoice_taxs.line_invoices_id')
       ->get()
       ->getResult();
   $retention = 0;
   foreach ($lineinvoicesTax as $item) {
       if($item->taxes_id != 1){
           $retention +=  $item->retention;
       }
   }
    return $retention;
}