<?php


namespace App\Controllers\Xml;


use App\Controllers\Api\Auth;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;

class DocumentXml
{
    protected $xml;
    protected $format       = [];
    protected $errors       = [];
    protected $namespaces;
    protected $position     = 0;
    protected $inputId      = 0;
    protected $inputs       = [];


    protected function zero($value, $subvalue = null)
    {
        if(count((array) $value) > 0) {
            return ((array) $value)[0];
        } else  {
            return $subvalue;
        }

    }



    static public function   registerDocumentXml(Array $xml, $id)
    {
     
        try {
            $customer = new Customer();
            $customers = $customer->where(['identification_number' => $xml['company']['identification_number'], 'companies_id' => Auth::querys()->companies_id])
                ->asObject()
                ->get()
                ->getResult();

            if(count($customers) ==  0) {
                $customerRegister = [];
                isset($xml['company']['name'])                             ? $customerRegister['name']                              = $xml['company']['name'] : NULL;
                isset($xml['company']['type_document_identification_id'])  ? $customerRegister['type_document_identifications_id']  = $xml['company']['type_document_identification_id'] : NULL;
                isset($xml['company']['identification_number'])            ? $customerRegister['identification_number']             = $xml['company']['identification_number'] : NULL;
                isset($xml['company']['dv'])                               ? $customerRegister['dv']                                = $xml['company']['dv'] : NULL;
                isset($xml['company']['phone'])                            ? $customerRegister['phone']                             = $xml['company']['phone'] : NULL;
                isset($xml['company']['address'])                          ? $customerRegister['address']                           = $xml['company']['address'] : NULL;
                isset($xml['company']['email'])                            ? $customerRegister['email']                             = $xml['company']['email'] : NULL;
                isset($xml['company']['merchant_registration'])            ? $customerRegister['merchant_registration']             = $xml['company']['merchant_registration'] : NULL;
                isset($xml['company']['type_organization_id'])             ? $customerRegister['type_organization_id']              = $xml['company']['type_organization_id'] : NULL;
                isset($xml['company']['type_regime_id'])                   ? $customerRegister['type_regime_id']                    = $xml['company']['type_regime_id'] : NULL;
                isset($xml['company']['municipality_id'])                  ? $customerRegister['municipality_id']                   = $xml['company']['municipality_id'] : NULL;
                $customerRegister['type_customer_id']                      = 2;
                $customerRegister['companies_id']                          = Auth::querys()->companies_id;
                $customer->save($customerRegister);

                self::invoiceCreate( $xml, $customer->getInsertID(), $id);
            }else {
                self::invoiceCreate( $xml, $customers[0]->id, $id);
            }
        }
        catch (\Exception $e) {
           echo $e;
        }
    }

    static public function invoiceCreate($xml, $customerId, $id)
    {
        try{

            isset($xml['resolution_number']) ? $data['resolution_id'] = $xml['resolution_number'] : NULL;
            isset($xml['number']) ? $data['resolution'] = $xml['number'] : NULL;
            isset($xml['type_document_id']) ? $data['type_documents_id'] = $xml['type_document_id'] : NULL;
            $data['companies_id'] = Auth::querys()->companies_id;
            $data['companies_id'] = 50;

            $invoice = new Invoice();
            $invoices = $invoice->where($data)
                ->get()
                ->getResult();

            if(count($invoices) == 0 ) {
                $createInvoice = [];

                isset($xml['number'])                               ? $createInvoice['resolution']              = $xml['number'] : NULL;
                isset($xml['prefix'])                               ? $createInvoice['prefix']                  = $xml['prefix'] : NULL;
                isset($xml['resolution_number'])                    ? $createInvoice['resolution_id']           = $xml['resolution_number'] : NULL;
                isset($xml['payable_amount'])                       ? $createInvoice['payable_amount']          = $xml['legal_monetary_totals']['payable_amount'] : NULL;
                isset($xml['payment_form']['payment_due_date'])     ? $createInvoice['payment_due_date']        = $xml['payment_form']['payment_due_date'] : NULL;
                isset($xml['payment_form']['payment_forms_id'])     ? $createInvoice['payment_forms_id']        = $xml['payment_form']['payment_forms_id'] : NULL;
                isset($xml['payment_form']['payment_methods_id'])   ? $createInvoice['payment_methods_id']      = $xml['payment_form']['payment_methods_id'] : NULL;
                isset($xml['payment_form']['duration_measure'])     ? $createInvoice['duration_measure']        = $xml['payment_form']['duration_measure'] : NULL;
                isset($xml['notes'])                                ? $createInvoice['notes']                   = $xml['notes'] : NULL;
                isset($xml['idcurrency'])                           ? $createInvoice['idcurrency']              = $xml['idcurrency'] : NULL;
                isset($xml['calculationrate'])                      ? $createInvoice['calculationrate']         = $xml['calculationrate'] : NULL;
                isset($xml['calculationratedate'])                  ? $createInvoice['calculationratedate']     = $xml['calculationratedate'] : NULL;
     

                switch($xml['type_document_id']) {
                    case '1':
                        $createInvoice['type_documents_id'] = 101; // Venta nacional
                        $type =  'invoice_lines';
                        $typeDocument = 'legal_monetary_totals';
                        break;
                    case '2':
                        $createInvoice['type_documents_id'] = 102; // Venta de exportacion
                        $type =  'invoice_lines';
                        $typeDocument = 'legal_monetary_totals';
                        break;
                    case '4':
                        $createInvoice['type_documents_id'] = 103; // Nota credito
                        $createInvoice['resolution_credit'] = null;
                        $type =  'credit_note_lines';
                        $typeDocument = 'legal_monetary_totals';
                        break;
                    case '5':
                        $createInvoice['type_documents_id'] = 104; // Nota debito
                        $createInvoice['resolution_credit'] = null;
                        $type =  'debit_note_lines';
                        $typeDocument = 'requested_monetary_totals';
                        break;
                }
    
                isset($xml[ $typeDocument ]['line_extesion_amount'])                 ? $createInvoice['line_extesion_amount']    = $xml[ $typeDocument ]['line_extesion_amount'] : NULL;
                isset($xml[ $typeDocument ]['tax_exclusive_amount'])                 ? $createInvoice['tax_exclusive_amount']    = $xml[ $typeDocument ]['tax_exclusive_amount'] : NULL;
                isset($xml[ $typeDocument ]['tax_inclusive_amount'])                 ? $createInvoice['tax_inclusive_amount']    = $xml[ $typeDocument ]['tax_inclusive_amount'] : NULL;
                isset($xml[ $typeDocument ]['allowance_total_amount'])               ? $createInvoice['allowance_total_amount']  = $xml[ $typeDocument ]['allowance_total_amount'] : NULL;
                isset($xml[ $typeDocument ]['charge_total_amount'])                  ? $createInvoice['charge_total_amount']     = $xml[ $typeDocument ]['charge_total_amount'] : NULL;
                isset($xml[ $typeDocument ]['payable_amount'])                       ? $createInvoice['payable_amount']          = $xml[ $typeDocument ]['payable_amount'] : NULL;
             
                $createInvoice['companies_id']          = Auth::querys()->companies_id;
                $createInvoice['seller_id']             =  null;
                $createInvoice['user_id']               = Auth::querys()->id;
                $createInvoice['created_at']            = $xml['date'] .' '.$xml['time'];
                $createInvoice['customers_id']          = $customerId;
                $createInvoice['invoice_status_id']     = 19;
                $createInvoice['uuid']                  = $xml['uuid'];
                $createInvoice['status_wallet']         = 'Paga';
                $createInvoice['issue_date']            = date('Y-m-d');

                



                $invoice->save($createInvoice);
                self::LineInvoice($xml, $invoice->getInsertID(), $type);
                $document = new Document();
                $document->update($id, ['document_status_id' => 2, 'invoice_id' => $invoice->getInsertID()]);




            } else {
                return redirect()->to(base_url('documents'))->with('warning', 'La factura ya se encuentra cargada.');
            }
        } catch (\Exception $e){
            echo $e;
        }
    
    }

    static public function LineInvoice($xml, $invoiceId, $type)
    {
  
        
        $lineInvoice = new LineInvoice();
      
        foreach ($xml[$type] as $lineInvoiceItem) {
            $lineInvoiceCreate = [];
            $lineInvoiceCreate['invoices_id']               = $invoiceId;
            isset($lineInvoiceItem['allowance_charges'])    ? $lineInvoiceCreate['discount_amount'] = $lineInvoiceItem['allowance_charges'][0]['amount'] : NULL;
            $lineInvoiceCreate['quantity']                  = $lineInvoiceItem['invoiced_quantity'];
            $lineInvoiceCreate['line_extension_amount']     = $lineInvoiceItem['line_extension_amount'];
            $lineInvoiceCreate['price_amount']              = $lineInvoiceItem['price_amount'];
            isset($lineInvoiceItem['code'])                  ? $lineInvoiceCreate['code']  = $lineInvoiceItem['code']: Null;
            $lineInvoiceCreate['products_id']               = NULL;
            $lineInvoiceCreate['description']               = $lineInvoiceItem['description'];
            $lineInvoiceCreate['provider_id']               = NULL;
            $lineInvoiceCreate['discounts_id']              = 1;
        
            $lineInvoice->save($lineInvoiceCreate);
            self::LineInvoiceTax($lineInvoiceItem, $lineInvoice->getInsertID());

        }
        $lineInvoicesTax = new  LineInvoiceTax();
        if(isset($xml['with_holding_tax_total'])) {
            foreach($xml['with_holding_tax_total'] as $withHoldingTaxTotal) {
                $lineInvoiceTaxCreate = [];
                $lineInvoiceTaxCreate['line_invoices_id']          = $lineInvoice->getInsertID();
                $lineInvoiceTaxCreate['tax_amount']                = $withHoldingTaxTotal['tax_amount'];
                $lineInvoiceTaxCreate['taxable_amount']            = $withHoldingTaxTotal['taxable_amount'];
                $lineInvoiceTaxCreate['percent']                   = $withHoldingTaxTotal['percent'];
                $lineInvoiceTaxCreate['taxes_id']                  = $withHoldingTaxTotal['tax_id'];
                $lineInvoicesTax->save($lineInvoiceTaxCreate);
            }
        }
    }
    static public function LineInvoiceTax($xml, $lineInvoicesId)
    {
        $lineInvoicesTax = new  LineInvoiceTax();
        if(isset($xml['tax_totals'])) {
            foreach ($xml['tax_totals'] as $lineInvoiceTaxItem) {
                $lineInvoiceTaxCreate = [];
                $lineInvoiceTaxCreate['line_invoices_id']          = $lineInvoicesId;
                $lineInvoiceTaxCreate['tax_amount']                = $lineInvoiceTaxItem['tax_amount'];
                $lineInvoiceTaxCreate['taxable_amount']            = $lineInvoiceTaxItem['taxable_amount'];
                $lineInvoiceTaxCreate['percent']                   = $lineInvoiceTaxItem['percent'];
                $lineInvoiceTaxCreate['taxes_id']                  = $lineInvoiceTaxItem['tax_id'];
                $lineInvoicesTax->save($lineInvoiceTaxCreate);
            }
        }

    }
}