<?php


namespace App\Controllers\Api;



use App\Controllers\ApiController;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Resolution;
use App\Models\Currency;


class Invoices extends ServiceController
{
    public function create()
    {
        $json =  file_get_contents('php://input');
        $invoice = json_decode($json);
        $data = [
            //'resolution'                => ($invoice->resolution_number == 0) ? null :$this->resolutionData([$invoice->type_document_id], $invoice->resolution_number),
            //'resolution_id'             => $invoice->resolution_number,
            'payment_forms_id'          => $invoice->payment_form->payment_form_id,
            'payment_methods_id'        => $invoice->payment_form->payment_method_id,
            'payment_due_date'          => ($invoice->payment_form->duration_measure == 0) ? date('Y-m-d') : $invoice->payment_form->payment_due_date,
            'duration_measure'          => $invoice->payment_form->duration_measure,
            'type_documents_id'         => $invoice->type_document_id,
            'line_extesion_amount'      => $invoice->legal_monetary_totals->line_extension_amount,
            'tax_exclusive_amount'      => $invoice->legal_monetary_totals->tax_exclusive_amount,
            'tax_inclusive_amount'      => $invoice->legal_monetary_totals->tax_inclusive_amount,
            'allowance_total_amount'    => $invoice->legal_monetary_totals->allowance_total_amount,
            'charge_total_amount'       => $invoice->legal_monetary_totals->charge_total_amount,
            'payable_amount'            => $invoice->legal_monetary_totals->payable_amount,
            'customers_id'              => $invoice->customer->id,
            'created_at'                => date('Y-m-d H:i:s'),
            'invoice_status_id'         => 1,
            'notes'                     => $invoice->notes,
            'companies_id'              => Auth::querys()->companies_id,
            'idcurrency'                => isset($invoice->idcurrency) ? $invoice->idcurrency : 35,
            'calculationrate'           => isset($invoice->calculationrate) ? $invoice->calculationrate : 1,
            'calculationratedate'       => isset($invoice->calculationratedate) ? $invoice->calculationratedate: date('Y-m-d'),
            'status_wallet'             => 'Pendiente',
            'user_id'                   => Auth::querys()->id,
            'seller_id'                 => isset($invoice->seller_id) ? $invoice->seller_id : null,
            'delevery_term_id'          => $invoice->type_document_id == 2 ? $invoice->delevery_term_id : NULL,
            'issue_date'                => ($invoice->date ?? null),
            'send'                      => 'False'
        ];

        $invoices = new Invoice();
        $invoiceId = $invoices->insert($data);
        $invoiceLines = $invoice->invoice_lines;
        $this->_lineInvoice($invoiceLines, $invoiceId);


        if ($invoiceId) {
            $api = new ApiController();
            $api->preview(Auth::querys()->companies_id, $invoiceId);
            http_response_code(201);
            $this->respond(['status' => 'ok', 'message' => 'Guardado Correctamente.']);
        }
    }

    public function resolution($id)
    {
        return $this->_resolution(1, $id);
    }

    public function update($id = null)
    {


        $invoice = new Invoice();
        $invoices = $invoice->where([
            'companies_id'          => Auth::querys()->companies_id,
            'id'                    => $id,
            'type_documents_id <'   => 100,
            'invoice_status_id'     => 1
        ])->countAllResults();

        if($invoices != 0) {
        $json = file_get_contents('php://input');
        $invoice = json_decode($json);

        $invoiceLines = $invoice->invoice_lines;

        $data = [
            //'resolution'                => $invoice->number,
            'payment_forms_id'          => $invoice->payment_form->payment_form_id,
            'payment_methods_id'        => $invoice->payment_form->payment_method_id,
            'payment_due_date'          => ($invoice->payment_form->duration_measure == 0) ? date('Y-m-d') : $invoice->payment_form->payment_due_date,
            'duration_measure'          => $invoice->payment_form->duration_measure,
            'line_extesion_amount'      => $invoice->legal_monetary_totals->line_extension_amount,
            'tax_exclusive_amount'      => $invoice->legal_monetary_totals->tax_exclusive_amount,
            'tax_inclusive_amount'      => $invoice->legal_monetary_totals->tax_inclusive_amount,
            'allowance_total_amount'    => $invoice->legal_monetary_totals->allowance_total_amount,
            'charge_total_amount'       => $invoice->legal_monetary_totals->charge_total_amount,
            'payable_amount'            => $invoice->legal_monetary_totals->payable_amount,
            'type_documents_id'         => $invoice->type_document_id,
            'customers_id'              => $invoice->customer->id,
            'invoice_status_id'         => 1,
            'notes'                     => $invoice->notes,
            'idcurrency'                => isset($invoice->idcurrency) ? $invoice->idcurrency : 35,
            'calculationrate'           => isset($invoice->calculationrate) ? $invoice->calculationrate : 1,
            'calculationratedate'       => isset($invoice->calculationratedate) ? $invoice->calculationratedate: date('Y-m-d'),
            'seller_id'                 => $invoice->seller_id,
            'delevery_term_id'          => $invoice->type_document_id == 2 ? $invoice->delevery_term_id : NULL


        ];
        if(isset($invoice->update_date) && $invoice->update_date ==  true) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }


        foreach ($invoice->idDelete as $item) {
            $lineInvoiceTax = new LineInvoiceTax();
            $lineInvoiceTax->where(['line_invoices_id' => $item])->delete();
            $lineInvoice = new LineInvoice();
            $lineInvoice->where(['id' => $item])->delete();
        }
     
        $invoice = new Invoice();
        $invoice->set($data)
            ->where(['id' => $id])
            ->update();

        foreach ($invoiceLines as $value) {
            if(isset($value->id)){
                $line = [
                    'discount_amount'       => $value->allowance_charges[0]->amount,
                    'discounts_id'          => 1,
                    'quantity'              => $value->invoiced_quantity,
                    'line_extension_amount' => $value->line_extension_amount,
                    'price_amount'          => $value->price_amount,
                    'cost_amount'           => $this->_costProduct($value->product_id),
                    'products_id'           => $value->product_id,
                    'description'           => $value->description,
                    'provider_id'           => isset($value->providerId) ? $value->providerId : null
                ];
                $lineInvoice = new LineInvoice();
                $lineInvoice->set($line)
                    ->where(['id' => $value->id])
                    ->update();

                foreach ($value->tax_totals as $taxe) {
                    $tax = [
                        "taxes_id" => $taxe->tax_id,
                        "tax_amount" => $taxe->tax_amount,
                        "percent" => $taxe->percent,
                        "taxable_amount" => $taxe->taxable_amount
                    ];
                    $lineInvoiceTax = new LineInvoiceTax();
                    $lineInvoiceTax->set($tax)
                        ->where(['taxes_id' => $taxe->tax_id,'line_invoices_id' =>  $value->id ])
                        ->update();
                }
                if (isset($value->with_holding_tax_total)) {
                    foreach ($value->with_holding_tax_total as $retention) {
                        $tax = [
                            "taxes_id" => $retention->tax_id,
                            "tax_amount" => $retention->tax_amount,
                            "percent" => $retention->percent,
                            "taxable_amount" => $retention->taxable_amount
                        ];
                        $lineInvoiceTax = new LineInvoiceTax();
                        $lineInvoiceTax->set($tax)
                            ->where(['taxes_id' => $retention->tax_id, 'line_invoices_id' =>  $value->id])
                            ->update();
                    }

                }
            }else {
                $line = [
                    'discount_amount' => $value->allowance_charges[0]->amount,
                    'discounts_id' => 1,
                    'quantity' => $value->invoiced_quantity,
                    'line_extension_amount' => $value->line_extension_amount,
                    'price_amount' => $value->price_amount,
                    'cost_amount'           => $this->_costProduct($value->product_id),
                    'products_id' => $value->product_id,
                    'description' => $value->name,
                    'invoices_id' => $id
                ];
                $lineInvoice = new LineInvoice();
                $lineId = $lineInvoice->insert($line);

                foreach ($value->tax_totals as $taxe) {
                    $tax = [
                        "taxes_id" => $taxe->tax_id,
                        "tax_amount" => $taxe->tax_amount,
                        "percent" => $taxe->percent,
                        "taxable_amount" => $taxe->taxable_amount,
                        "line_invoices_id" => $lineId
                    ];
                    $lineInvoiceTax = new LineInvoiceTax();
                    $lineInvoiceTax->insert($tax);
                }
                if (isset($value->with_holding_tax_total)) {
                    foreach ($value->with_holding_tax_total as $retention) {
                        $tax = [
                            "taxes_id" => $retention->tax_id,
                            "tax_amount" => $retention->tax_amount,
                            "percent" => $retention->percent,
                            "taxable_amount" => $retention->taxable_amount,
                            "line_invoices_id" => $lineId
                        ];
                        $lineInvoiceTax = new LineInvoiceTax();
                        $lineInvoiceTax->insert($tax);
                    }

                }

            }

        }

            if ($invoice) {
                $api = new ApiController();
            $api->preview(session('user')->companies_id, $id);
                http_response_code(201);
                echo json_encode(['status' => 'ok', 'message' => 'Guardado Correctamente.']);
                die();
            }
        }else {
                echo json_encode(['status' => 'ok', 'message' => 'Guardado Correctamente.']);
                die();
        }
    }
    
    public function invoice($id)
    {

        $this->_getInvoice($id);
    }

    public function line_invoice($id)
    {
        $this->_getLineInvoice($id);
    }

    public function multipleResolution()
    {
        $resolutions = new Resolution();
        $resolution = $resolutions->where(['companies_id' => Auth::querys()->companies_id, 'type_documents_id' => 1, 'status' => null ])
            ->get()
            ->getResult();
        echo json_encode($resolution);
        die();
    }

    public function currency()
    {
        $currency = new Currency();
        $currencys = $currency->findAll();

        echo json_encode($currencys);
        die();
    }

    public function cufe($id)
    {
        $invoices = new Invoice();
        $invoice = $invoices->select('uuid')->where(['companies_id' => session('user')->companies_id, 'id' => $id ])
            ->get()
            ->getResult()[0];


        return $this->respond(['url' => 'https://catalogo-vpfe.dian.gov.co/document/searchqr?documentKey='.$invoice->uuid]);
        die();

    }
    //john
    public function multipleResolutionOdoo($id)
    {
        $resolutions = new Resolution();
        //$resolution = $resolutions->where(['companies_id' => 1, 'type_documents_id' => 1 ])
        $resolution = $resolutions->where(['companies_id' => $id, 'type_documents_id' => 1 ])
            ->get()
            ->getResult();
        echo json_encode($resolution);
        die();
    }
    public function resolutionOdoo($typeDocument,$company, $id = null)
    {
        if($typeDocument == 4){
            return $this->_resolutionOdoo($typeDocument, $company);
        }else{
            return $this->_resolutionOdoo($typeDocument, $company, $id );
        }

    }

    public function invoices()
    {
        $invoice = new Invoice();
        $invoices = $invoice
            ->select('*, invoices.id as id_invoice, invoices.notes as notes')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where(['send' => 'False', 'invoices.type_documents_id' => 1])
            ->asObject()
            ->get()
            ->getResult();
       
        $invoiceData = [];
        foreach($invoices as $invoice){
            $data = [];
	    $data['date']						= date('Y-m-d');
	    $data['time']						= date('H:i:s');
            $data['invoice_id']                                         = $invoice->id_invoice;
            $data['number']                                             = $invoice->resolution;
            $data['type_document_id']                                   = $invoice->type_documents_id;
            $data['resolution_number']                                  = $invoice->resolution_id;
            $data['payment_form']['duration_measure']                   = $invoice->duration_measure;
            $data['payment_form']['payment_form_id']                    = $invoice->payment_forms_id;
            $data['payment_form']['payment_method_id']                  = $invoice->payment_methods_id;
            $data['payment_form']['payment_due_date']                   = date('Y-m-d');
            $data['customer']['name']                                   = $invoice->name;
            $data['customer']['type_document_identification_id']        = $invoice->type_document_identifications_id;
            $data['customer']['identification_number']                  = $invoice->identification_number;
            $data['customer']['phone']                                  = $invoice->phone;
            $data['customer']['address']                                = $invoice->address;
            $data['customer']['email']                                  = $invoice->email;
            $data['customer']['merchant_registration']                  = $invoice->merchant_registration;
            $data['customer']['type_organization_id']                   = $invoice->type_organization_id;
            $data['customer']['municipality_id']                        = $invoice->municipality_id;
            $data['customer']['type_regime_id']                         = $invoice->type_regime_id;
            $data['notes']                                              = $invoice->notes;
            $data['duration_measure']                                   = $invoice->duration_measure;
            $data['idcurrency']                                         = $invoice->idcurrency;
            $data['calculationrate']                                    = $invoice->calculationrate;
            $data['calculationratedate']                                = $invoice->calculationratedate;
            $data['legal_monetary_totals']['line_extension_amount']     = $invoice->line_extesion_amount;
            $data['legal_monetary_totals']['tax_exclusive_amount']      = $invoice->tax_exclusive_amount;
            $data['legal_monetary_totals']['tax_inclusive_amount']      = $invoice->tax_inclusive_amount;
            $data['legal_monetary_totals']['allowance_total_amount']    = '0.00';
            $data['legal_monetary_totals']['charge_total_amount']       = '0.00';
            $data['legal_monetary_totals']['payable_amount']            = $invoice->payable_amount;




            $lineInvoice = new LineInvoice();
            $products = $lineInvoice->select('*, products.id as products_id, line_invoices.id as id, line_invoices.description as line_invoice_description')
                ->join('products', 'line_invoices.products_id = products.id')
                ->where(['line_invoices.invoices_id' => $invoice->id_invoice])
                ->asObject()
                ->get()
                ->getResult();

        
            $i = 0;
            foreach ($products as $key) {
                
                $data['invoice_lines'][$i]['code']                                              = $key->code;
                $data['invoice_lines'][$i]['name']                                              = $key->name;
                $data['invoice_lines'][$i]['line_extension_amount']                             = $key->line_extension_amount;
                $data['invoice_lines'][$i]['price_amount']                                      = $key->line_extension_amount;
                $data['invoice_lines'][$i]['description']                                       = $key->line_invoice_description;
                $data['invoice_lines'][$i]['unit_measure_id']                                   = $key->unit_measures_id;
                $data['invoice_lines'][$i]['type_item_identification_id']                       = $key->type_item_identifications_id;
                $data['invoice_lines'][$i]['base_quantity']                                     = 1;
                $data['invoice_lines'][$i]['free_of_charge_indicator']                          = $key->free_of_charge_indicator == 'true' ? true : false;
                $data['invoice_lines'][$i]['reference_price_id']                                = $key->reference_prices_id;
                $data['invoice_lines'][$i]['invoiced_quantity']                                 = (double) $key->quantity;
                $data['invoice_lines'][$i]['allowance_charges'][0]['charge_indicator']          = false;
                $data['invoice_lines'][$i]['allowance_charges'][0]['amount']                    = (double) $key->discount_amount / $key->quantity;
                $data['invoice_lines'][$i]['allowance_charges'][0]['base_amount']               = (double) $key->line_extension_amount;
                $data['invoice_lines'][$i]['allowance_charges'][0]['discount_id']               = 1;//key->discounts_id;
                $data['invoice_lines'][$i]['allowance_charges'][0]['allowance_charge_reason']   = 'DESCUENTO GENERAL';

                $l = 0;

                $taxLineInvoices = new LineInvoiceTax();
                $taxLineInvoice = $taxLineInvoices->where(['line_invoices_id' => $key->id])->get()->getResult();
                foreach ($taxLineInvoice as $value) {
                    if ($value->taxes_id == 1) {
                        $data['tax_totals'][0]['id']                = (int)$value->id;
                        $data['tax_totals'][0]['tax_amount']        = (double) $value->tax_amount;
                        $data['tax_totals'][0]['taxable_amount']    = (double) $value->taxable_amount;
                        $data['tax_totals'][0]['percent']           = (double)$value->percent;
                        $data['tax_totals'][0]['tax_id']            = (int)$value->taxes_id;


                        $data['invoice_lines'][$i]['tax_totals'][0]['id']                = (int)$value->id;
                        $data['invoice_lines'][$i]['tax_totals'][0]['tax_amount']        = (double) $value->tax_amount;
                        $data['invoice_lines'][$i]['tax_totals'][0]['taxable_amount']    = (double) $value->taxable_amount;
                        $data['invoice_lines'][$i]['tax_totals'][0]['percent']           = (double)$value->percent;
                        $data['invoice_lines'][$i]['tax_totals'][0]['tax_id']            = (int)$value->taxes_id;
                    }
                }


                $i++;
            }

            array_push($invoiceData, $data);

        }

        return $this->respond(['status' =>  200, 'data' => $invoiceData], 200);
    }

    public function sendDIAN($id = null, $cufe = null) 
    {
        $invoice = new Invoice();
        $invoice->update($id, ['send' => 'True', 'invoice_status_id' => 3, 'uuid' => $cufe]);
        return $this->respond(['status' => 201, 'data' => 'success create'], 201);
    
    }

public function annexe() {
echo json_encode(['data' => []]);
}

public function status($status, $id)
    {
        $invoices = new Invoice();
        if($status == 'accept'){
            $edit = 21;
        }else{
            $edit = 20;
        }
        $invoicesOrigin = $invoices->where(['id' => $id])->asObject()->first();
        $invoices->set(['invoice_status_id' => $edit])->where(['id' => $invoicesOrigin->resolution_credit])->update();
        $invoices->set(['invoice_status_id' => $edit])->where(['id' => $invoicesOrigin->id])->update();

        return $this->respond(['status' =>  200, 'data' => $edit], 200);
    }

}

