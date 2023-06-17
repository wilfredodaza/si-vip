<?php


namespace App\Traits;


use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Resolution;
use CodeIgniter\I18n\Time;

trait DocumentTrait
{
    protected $format = 'json';

    protected function _resolution($typeDocument, $id = null ) {


        $resolution = new Resolution();
        //  $resolution = $resolution->where([ 'companies_id' => session('user')->companies_id]);
        $resolution = $resolution->where([ 'companies_id'=> Auth::querys()->companies_id]);

        if($id) {
            $resolution->where(['resolution' => $id]);
            $consulta =['type_documents_id' => 1];
            $resolution->where($consulta);
        }else{
            $resolution->where(['type_documents_id' => $typeDocument ]);
        }

        $resolution = $resolution
            ->orderBy('id', 'DESC')
            ->asObject()
            ->first();



        $invoices = new Invoice();
        $invoices->select('invoices.resolution');
        //->where([ 'companies_id' =>  1])
        if($id) {
            // $invoices->where([ 'companies_id'=> session('user')->companies_id, 'resolution_id =' => $id]);
            $invoices->where([ 'companies_id'=> Auth::querys()->companies_id, 'resolution_id =' => $id]);

        } else {
            // $invoices->where(['companies_id'=> session('user')->companies_id, 'type_documents_id' => $typeDocument ]);
            $invoices->where(['companies_id'=> Auth::querys()->companies_id, 'type_documents_id' => $typeDocument ]);
        }


        $invoices = $invoices->orderBy('id', 'DESC')
            ->asObject()
            ->first();

        if(!$invoices){
            return $this->respond(['resolution' => $resolution->from], 200);
        }else {
            return $this->respond(['resolution' => ( $invoices->resolution + 1 )], 200);
        }
    }

    protected function _lineInvoice($invoiceLines, $invoiceId)
    {
        foreach ($invoiceLines as $value) {
            $line = [
                'invoices_id'           => $invoiceId,
                'discount_amount'       => $value->allowance_charges[0]->amount,
                'discounts_id'          => 1,
                'quantity'              => $value->invoiced_quantity,
                'line_extension_amount' => $value->line_extension_amount,
                'price_amount'          => $value->price_amount,
                'products_id'           => $value->product_id,
                'description'           => $value->description
            ];
            $lineInvoice = new LineInvoice();
            $lineInvoiceId = $lineInvoice->insert($line);
            $this->_taxLineInvoice($value,  $lineInvoiceId);
        }
    }

    private  function _taxLineInvoice($taxTotal, $lineInvoiceId)
    {


        /* $cantidad = 1000;

         for ($i=0; $i < $cantidad ; $i++) {
             $tax = [
                 'taxes_id'          => $taxTotal->tax_totals[$i]->tax_id,
                 'tax_amount'        => $taxTotal->tax_totals[$i]->tax_amount,
                 'percent'           => $taxTotal->tax_totals[$i]->percent,
                 'taxable_amount'    => $taxTotal->tax_totals[$i]->taxable_amount,
                 'line_invoices_id'  => $lineInvoiceId
             ];
             $lineInvoiceTax = new LineInvoiceTax();
             $invoicetaxid = $lineInvoiceTax->insert($tax);
             if(!$taxTotal->tax_totals[$i]->tax_id){
                 $cantidad = 0;
             }

         }*/
        //echo json_encode($invoicetaxid);

        foreach ($taxTotal->tax_totals as $taxe) {

            $tax = [
                'taxes_id'          => $taxe->tax_id,
                'tax_amount'        => $taxe->tax_amount,
                'percent'           => $taxe->percent,
                'taxable_amount'    => $taxe->taxable_amount,
                'line_invoices_id'  => $lineInvoiceId
            ];
            $lineInvoiceTax = new LineInvoiceTax();
            $lineInvoiceTax->insert($tax);
        }
        if (isset($taxTotal->with_holding_tax_total)) {
            foreach ($taxTotal->with_holding_tax_total as $retention) {
                $tax = [
                    'taxes_id'          => $retention->tax_id,
                    'tax_amount'        => $retention->tax_amount,
                    'percent'           => $retention->percent,
                    'taxable_amount'    => $retention->taxable_amount,
                    'line_invoices_id'  => $lineInvoiceId
                ];

                $lineInvoiceTax = new LineInvoiceTax();
                $lineInvoiceTax->insert($tax);
            }
        }
        

        /*if (isset($taxTotal->with_holding_tax_total)) {
            $cantidadDescuento = 1000;
            for ($i=0; $i < $cantidadDescuento ; $i++) {
                $tax = [
                    'taxes_id'          => $taxTotal->with_holding_tax_total[$i]->tax_id,
                    'tax_amount'        => $taxTotal->with_holding_tax_total[$i]->tax_amount,
                    'percent'           => $taxTotal->with_holding_tax_total[$i]->percent,
                    'taxable_amount'    => $taxTotal->with_holding_tax_total[$i]->taxable_amount,
                    'line_invoices_id'  => $lineInvoiceId
                ];
                $lineInvoiceTax = new LineInvoiceTax();
                $lineInvoiceTax->insert($tax);
                if(!$taxTotal->with_holding_tax_total[$i]->tax_id){
                    $cantidadDescuento = 0;
                }
            }
        }*/

    }

    protected function _getInvoice($id)
    {
        $invoice = new Invoice();
        $invoice = $invoice
            ->select('*, invoices.id as id_invoice, invoices.notes as notes')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();
        $data = [];
        $data['resolution_number']                  = $invoice->resolution_id;
        $data['invoice_status_id']                  = $invoice->invoice_status_id;
        $data['calculationratedate']                = $invoice->calculationratedate;
        $data['calculationrate']                    = $invoice->calculationrate;
        $data['idcurrency']                         = (int)$invoice->idcurrency;
        $data['payable_amount']                     = $invoice->payable_amount;
        $data['payment_method_id']                  = $invoice->payment_forms_id;
        $data['id']                                 = $invoice->id_invoice;
        $data['customer']['id']                     = $invoice->customers_id;
        $data['customer']['name']                   = $invoice->name;
        $data['customer']['identification_number']  = $invoice->identification_number;
        $data['customer']['phone']                  = $invoice->phone;
        $data['customer']['address']                = $invoice->address;
        $data['customer']['email']                  = $invoice->email;
        $data['customer']['merchant_registration']  = $invoice->merchant_registration;
        $data['billing_reference']["number"]        = $invoice->resolution;
        $data['billing_reference']["uuid"]          = $invoice->uuid;
        $data['billing_reference']["issue_date"]    = date('Y-m-d', strtotime($invoice->created_at));
        $data['billing_reference']['date']          = date('Y-m-d');
        $data['notes']                              = $invoice->notes;
        $data['duration_measure']                   = $invoice->duration_measure;
        http_response_code(200);
        echo json_encode($data);
        die();
    }

    protected function  _getLineInvoice($id, $type = 1)
    {

        $invoice = new LineInvoice();
        $products = $invoice->select('*, products.id as products_id, line_invoices.id as id, line_invoices.description as line_invoice_description')
            ->join('products', 'line_invoices.products_id = products.id')
            ->where(['invoices_id' => $id])
            ->get()
            ->getResult();

        $i = 0;
        foreach ($products as $key) {
            $data[$i]['id'] = $key->id;
            $data[$i]['product_id'] = $key->products_id;
            $data[$i]['code'] = $key->code;
            $data[$i]['name'] = $key->name;
            $data[$i]['price_amount'] = $key->line_extension_amount;
            $data[$i]['description'] = $key->line_invoice_description;
            $data[$i]['unit_measure_id'] = $key->unit_measures_id;
            $data[$i]['type_item_identification_id'] = $key->type_item_identifications_id;
            $data[$i]['base_quantity'] = 1;
            $data[$i]['free_of_charge_indicator'] = false;
            $data[$i]['reference_price_id'] = $key->reference_prices_id;
            $data[$i]['value'] = (double) $key->price_amount;
            $data[$i]['invoiced_quantity'] = (int) $key->quantity;
            $data[$i]['allowance_charges'][0]['valor'] = (double) $key->discount_amount / $key->quantity;
            $data[$i]['allowance_charges'][0]['charge_indicator'] = false;
            $data[$i]['allowance_charges'][0]['amount'] = (int)$data[$i]['allowance_charges'][0]['valor'];
            $data[$i]['allowance_charges'][0]['base_amount'] = (int)$key->valor;
            $data[$i]['allowance_charges'][0]['discount_id'] = 1;//key->discounts_id;
            $data[$i]['allowance_charges'][0]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
            $l = 0;

            $taxLineInvoices = new LineInvoiceTax();
            $taxLineInvoice = $taxLineInvoices->where(['line_invoices_id' => $key->id])->get()->getResult();
            foreach ($taxLineInvoice as $value) {

                if ($value->taxes_id == 1) {
                    $data[$i]['tax_totals'][0]['id'] = (int)$value->id;
                    $data[$i]['tax_totals'][0]['tax_amount'] = (double) $value->tax_amount;
                    $data[$i]['tax_totals'][0]['taxable_amount'] = (double) $value->taxable_amount;
                    $data[$i]['tax_totals'][0]['percent'] = (double)$value->percent;
                    $data[$i]['tax_totals'][0]['tax_id'] = (int)$value->taxes_id;
                } else {
                    if($type == 1) {
                        $data[$i]['with_holding_tax_total'][$l]['id'] = (int)$value->id;
                        $data[$i]['with_holding_tax_total'][$l]['tax_amount'] = (double) $value->tax_amount;
                        $data[$i]['with_holding_tax_total'][$l]['taxable_amount'] = (double) $value->taxable_amount;
                        $data[$i]['with_holding_tax_total'][$l]['percent'] = (double) $value->percent;
                        $data[$i]['with_holding_tax_total'][$l]['tax_id'] = (int)$value->taxes_id;
                        $l++;
                    }
                }
            }


            $i++;
        }
        http_response_code(200);
        echo json_encode($data);
        die();
    }

}