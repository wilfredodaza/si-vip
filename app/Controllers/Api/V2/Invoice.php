<?php


namespace App\Controllers\Api\V2;


use App\Controllers\Api\Auth;
use App\Controllers\ApiController;
use App\Controllers\HeadquartersController;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Wallet;
use App\Traits\ResponseApiTrait;
use App\Traits\ValidationsTrait2;
use App\Models\Resolution;
use CodeIgniter\RESTful\ResourceController;

class Invoice extends ResourceController
{
    use ValidationsTrait2, ResponseApiTrait;

    public function index()
    {

    }

    public function create()
    {
        $headquartersController = new HeadquartersController();
        $manager = $headquartersController->permissionManager(session('user')->role_id);
        $input = $this->getRequestInput($this->request);
        $validate = $this->validateRequest($input, [
            'currency_id'                                   => 'required|is_not_unique[type_currencies.id]',
          /*  'currency_rate'                                 => 'required',
            'currency_rate_date'                            => 'required|valid_date[Y-m-d]',
            'notes'                                         => 'permit_empty',
            'type_document_id'                              => 'required|is_not_unique[type_documents.id]',
            'customer_id'                                   => 'required|is_not_unique[customers.id]',
            'payment_form.payment_form_id'                  => 'required|is_not_unique[payment_forms.id]',
            'payment_form.payment_method_id'                => 'required|is_not_unique[payment_methods.id]',
            'payment_form.payment_due_date'                 => 'required|valid_date[Y-m-d]',
            'payment_form.duration_measure'                 => 'required|integer',
            'invoice_lines.*'                               => 'required',
            'invoice_lines.*.product_id'                    => 'required|is_not_unique[products.id]',
            'invoice_lines.*.invoiced_quantity'             => 'required|decimal',
            'invoice_lines.*.line_extension_amount'         => 'required|decimal',
            'invoice_lines.*.provider_id'                   => 'permit_empty|is_not_unique[customers.id]',
            'invoice_lines.*.free_of_charge_indicator'      => 'required',
            'invoice_lines.*.description'                   => 'required',
            'invoice_lines.*.price_amount'                  => 'required|decimal',
  //          'invoice_lines.*.tax_totals.*.tax_id'           => 'required|is_not_unique[taxes.id]',
   //         'invoice_lines.*.tax_totals.*.tax_amount'       => 'required|decimal',
    //        'invoice_lines.*.tax_totals.*.percent'          => 'required|decimal',
   //         'invoice_lines.*.tax_totals.*.taxable_amount'   => 'required|decimal',
            'invoice_lines.*.allowance_charges.*'           => 'required',
            'invoice_lines.*.allowance_charges.*.amount'    => 'required|decimal',
            'legal_monetary_totals.line_extension_amount'   => 'required|decimal',
            'legal_monetary_totals.tax_exclusive_amount'    => 'required|decimal',
            'legal_monetary_totals.tax_inclusive_amount'    => 'required|decimal',
            'legal_monetary_totals.allowance_total_amount'  => 'required|decimal',
            'legal_monetary_totals.charge_total_amount'     => 'required|decimal',
            'legal_monetary_totals.payable_amount'          => 'required|decimal',*/
        ]);
        if(!$validate) {
            return $this->respondHTTP422();
        } else {
            $walletDiscount = 0;
            if($manager){
                $idCompany = $headquartersController->idSearchBodega();
            }else{
               $idCompany = Auth::querys()->companies_id;
            }
            $json = $this->request->getJSON();
            $model = new \App\Models\Invoice();
            $invoice = $model->insert([
                'payment_forms_id'          => $json->payment_form->payment_form_id,
                'payment_methods_id'        => $json->payment_form->payment_method_id,
                'payment_due_date'          => ($json->payment_form->duration_measure == 0) ? date('Y-m-d') : $json->payment_form->payment_due_date,
                'duration_measure'          => $json->payment_form->duration_measure,
                'type_documents_id'         => $json->type_document_id,
                'line_extesion_amount'      => $json->legal_monetary_totals->line_extension_amount,
                'tax_exclusive_amount'      => $json->legal_monetary_totals->tax_exclusive_amount,
                'tax_inclusive_amount'      => $json->legal_monetary_totals->tax_inclusive_amount,
                'allowance_total_amount'    => $json->legal_monetary_totals->allowance_total_amount,
                'charge_total_amount'       => $json->legal_monetary_totals->charge_total_amount,
                'payable_amount'            => $json->legal_monetary_totals->payable_amount,
                'customers_id'              => $json->customer_id,
                'created_at'                => $json->created_at.' '.date('H:i:s'),
                'issue_date'                => date('Y-m-d'),
                'invoice_status_id'         => 1,
                'notes'                     => $json->notes,
                'companies_id'              => $idCompany,
                'idcurrency'                => $json->currency_id ?? 35,
                'calculationrate'           => isset($json->currency_rate) ? (float) $json->currency_rate : 1,
                'calculationratedate'       => $json->currency_rate_date ?? date('Y-m-d'),
                'status_wallet'             => ($json->payment_form->payment_form_id == 1)?'Paga':'Pendiente',
                'user_id'                   => Auth::querys()->id,
                'seller_id'                 => $json->seller_id ?? null,
                'delevery_term_id'          => $json->type_document_id == 2 ? $json->delevery_term_id : NULL,
                'send'                      => 'False'
            ]);


            $id = $invoice;

            foreach ($json->invoice_lines as $value) {
                $line = [
                    'invoices_id'           => $id,
                    'discount_amount'       => $value->allowance_charges[0]->amount,
                    'quantity'              => $value->invoiced_quantity,
                    'line_extension_amount' => (float) $value->line_extension_amount,
                    'price_amount'          => (float) $value->price_amount,
                    'products_id'           => $value->product_id,
                    'description'           => $value->description,
                    'discounts_id'           => 1,
                    'provider_id'           => $value->providerId ?? null
                ];
                $lineInvoice = new LineInvoice();
                $lineInvoiceId = $lineInvoice->insert($line);
                foreach ($value->tax_totals as $taxe) {
                    $tax = [
                        'taxes_id'          => (string)$taxe->tax_id,
                        'tax_amount'        => (float) $taxe->tax_amount,
                        'percent'           => (float) $taxe->percent,
                        'discounts_id'      => 1,
                        'taxable_amount'    => (float) $taxe->taxable_amount,
                        'line_invoices_id'  => $lineInvoiceId
                    ];
                    if($taxe->tax_id == 6 || $taxe->tax_id == 7){
                        $walletDiscount +=  $taxe->tax_amount;
                    }
                    $lineInvoiceTax = new LineInvoiceTax();
                    $lineInvoiceTax->insert($tax);
                }
            }
            if($json->payment_form->payment_form_id == 1){
                $wallet = [
                    'value' => $json->legal_monetary_totals->payable_amount - $walletDiscount,
                    'description' => "Se realiza pago de Contado",
                    'payment_method_id' => 7,
                    'invoices_id' => $id,
                    'created_at' => date("Y-m-d H:i:s"),
                    'user_id' => Auth::querys()->id
                ];
                $tableWallet = new Wallet();
                $tableWallet->save($wallet);
            }
            $json->id = $id;
            if ($id) {
                return $this->respond(['status' => 201, 'code' => 201, 'data' => $json]);
            }
        }

    }

    public function edit($id = null)
    {

        $model = new \App\Models\Invoice();
        $invoice  = $model->where(['id' => $id])
            ->asObject()
            ->first();

        // echo json_encode($invoice);die();

        if(is_null($invoice)){
            return $this->respond(['status' => 404, 'code' => 404, 'data' => 'Not Found']);
        }

        $data                                           = [];
        $data['prefix']                                 = $invoice->prefix;
        $data['number']                                 = $invoice->resolution;
        $data['resolution']                             = $invoice->resolution_id;
        $data['uuid']                                   = $invoice->uuid;
        $data['delevery_term_id']                       = $invoice->delevery_term_id;
        $data['currency_id']                            = $invoice->idcurrency;
        $data['currency_rate']                          = (float) $invoice->calculationrate;
        $data['currency_rate_date']                     = $invoice->calculationratedate;
        $data['notes']                                  = $invoice->notes;
        $data['seller_id']                              = $invoice->seller_id;
        $data['customer_id']                            = $invoice->customers_id;
        $data['payment_form']['payment_form_id']        = $invoice->payment_forms_id;
        $data['payment_form']['payment_method_id']      = $invoice->payment_methods_id;
        $data['payment_form']['payment_due_date']       = $invoice->payment_due_date;
        $data['type_document_id']                       = (int) $invoice->type_documents_id;
        $data['payment_form']['duration_measure']       = $invoice->duration_measure;
        $data['issue_date']                             =  explode(' ',$invoice->created_at)[0];
        $data['created_at']                             =  $data['issue_date'];

        $model = new LineInvoice();
        $lineInvoice = $model
            ->select([
                'line_invoices.id',
                'line_invoices.quantity',
                'line_invoices.line_extension_amount',
                'line_invoices.description',
                'products.free_of_charge_indicator',
                'products.code',
                'products.name',
                'line_invoices.products_id',
                'line_invoices.price_amount',
                'line_invoices.provider_id',
                'line_invoices.discount_amount'
            ])
            ->join('products', 'products.id = line_invoices.products_id')
            ->where(['invoices_id' => $id])
            ->asObject()
            ->findAll();


        $i = 0;
        foreach ($lineInvoice as $item) {
            $data['invoice_lines'][$i]['product_id']                                            = $item->products_id;
            $data['invoice_lines'][$i]['invoice_line_id']                                       = $item->id;
            $data['invoice_lines'][$i]['unit_measure_id']                                       = 70;
            $data['invoice_lines'][$i]['invoiced_quantity']                                     = (int) $item->quantity;
            $data['invoice_lines'][$i]['line_extension_amount']                                 = (int) $item->line_extension_amount;
            $data['invoice_lines'][$i]['free_of_charge_indicator']                              = $item->free_of_charge_indicator;
            $data['invoice_lines'][$i]['description']                                           = $item->description;
            $data['invoice_lines'][$i]['code']                                                  = $item->code;
            $data['invoice_lines'][$i]['type_item_identification_id']                           = 4;
            $data['invoice_lines'][$i]['base_quantity']                                         = (int) $item->quantity;
            $data['invoice_lines'][$i]['name']                                                  = $item->name;
            $data['invoice_lines'][$i]['price_amount']                                          = (int) $item->price_amount;
            $data['invoice_lines'][$i]['provider_id']                                           = $item->provider_id;
            $data['invoice_lines'][$i]['allowance_charges'][0]['id']                            = 0;
            $data['invoice_lines'][$i]['allowance_charges'][0]['discount_id']                   = 12;
            $data['invoice_lines'][$i]['allowance_charges'][0]['charge_indicator']              = false;
            $data['invoice_lines'][$i]['allowance_charges'][0]['allowance_charge_reason']       = 'Descuento General';
            $data['invoice_lines'][$i]['allowance_charges'][0]['amount']                        = (float) $item->discount_amount;
            $data['invoice_lines'][$i]['allowance_charges'][0]['base_amount']                   = (float) $item->price_amount * $item->quantity;
            $data['invoice_lines'][$i]['allowance_charges'][0]['type']                          = 0;
            $data['invoice_lines'][$i]['allowance_charges'][0]['percentage']                    =  (float) (100 * $item->discount_amount) / (($item->price_amount * $item->quantity) / $item->quantity);
            $data['invoice_lines'][$i]['allowance_charges'][0]['value_total']                   = (float) $item->discount_amount / $item->quantity;
            $l = 0;
            $model = new LineInvoiceTax();
            $lineInvoiceTax = $model->where(['line_invoices_id' => $item->id])
                ->asObject()
                ->findAll();
            foreach ($lineInvoiceTax as $item2) {
                $data['invoice_lines'][$i]['tax_totals'][$l]['tax_id']               =  $item2->taxes_id;
                $data['invoice_lines'][$i]['tax_totals'][$l]['tax_amount']           = (float) $item2->tax_amount;
                $data['invoice_lines'][$i]['tax_totals'][$l]['percent']              = (float) $item2->percent;
                $data['invoice_lines'][$i]['tax_totals'][$l]['taxable_amount']       = (float) $item2->taxable_amount;
                $l++;
            }

            $i++;
        }

        $data['legal_monetary_totals']['line_extension_amount']     = (float) $invoice->line_extesion_amount;
        $data['legal_monetary_totals']['tax_exclusive_amount']      = (float) $invoice->tax_exclusive_amount;
        $data['legal_monetary_totals']['tax_inclusive_amount']      = (float) $invoice->tax_inclusive_amount;
        $data['legal_monetary_totals']['allowance_total_amount']    = (float) $invoice->allowance_total_amount;
        $data['legal_monetary_totals']['charge_total_amount']       = (float) $invoice->charge_total_amount;
        $data['legal_monetary_totals']['payable_amount']            = (float) $invoice->payable_amount;

        return $this->respond(['status' => 201, 'code' => 201, 'data' => $data]);
    }

    public function update($id =null )
    {
        $data = $this->request->getJSON();
        $model = new \App\Models\Invoice();
        $invoiceId = $model
            ->set('notes', $data->notes)
            ->set('type_documents_id', $data->type_document_id)
            ->set('idcurrency', $data->currency_id)
            ->set('calculationrate', $data->currency_rate)
            ->set('calculationratedate', $data->currency_rate_date)
            ->set('customers_id', $data->customer_id)
            ->set('payment_forms_id',$data->payment_form->payment_form_id)
            ->set('payment_methods_id', $data->payment_form->payment_method_id)
            ->set('payment_due_date', $data->payment_form->payment_due_date)
            ->set('duration_measure', $data->payment_form->duration_measure)
            ->set('line_extesion_amount', $data->legal_monetary_totals->line_extension_amount)
            ->set('tax_exclusive_amount', $data->legal_monetary_totals->tax_exclusive_amount)
            ->set('tax_inclusive_amount', $data->legal_monetary_totals->tax_inclusive_amount)
            ->set('allowance_total_amount', $data->legal_monetary_totals->allowance_total_amount)
            ->set('charge_total_amount', $data->legal_monetary_totals->charge_total_amount)
            ->set('pre_paid_amount',0)
            ->set('payable_amount' , $data->legal_monetary_totals->payable_amount)
            ->set('created_at' , $data->created_at.date(' H:i:s'))
            ->where(['id' => $id])
            ->update();

        $lineInvoice = new LineInvoice();
        $lineInvoices = $lineInvoice->where(['invoices_id' => $id])
            ->get()
            ->getResult();

        foreach($lineInvoices as $lines) {
            $model = new LineInvoiceTax();
            $model->where(['line_invoices_id' => $lines->id])->delete();
            $lineInvoice->delete($lines->id);
        }


        foreach($data->invoice_lines as $line) {
            $model = new LineInvoice();
            $lineInvoiceId = $model->insert([
                'invoices_id'                       => $id,
                'discounts_id'                      => 1,
                'products_id'                       => $line->product_id,
                'discount_amount'                   => (float) $line->allowance_charges[0]->amount,
                'quantity'                          => $line->invoiced_quantity,
                'line_extension_amount'             => (float) $line->line_extension_amount,
                'provider_id'                       => $line->provider_id,
                'price_amount'                      => (float) $line->price_amount,
                'description'                       => $line->description,
                'type_generation_transmition_id'    => null,
                'start_date'                        => null
            ]);

            foreach ($line->tax_totals as $tax) {
                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id'      => $lineInvoiceId,
                    'taxes_id'              => (int) $tax->tax_id,
                    'tax_amount'            => (float) $tax->tax_amount,
                    'taxable_amount'        => (float) $tax->taxable_amount,
                    'percent'               =>  (float) $tax->percent
                ]);
            }
        }
        return $this->messageSuccess($data);
    }

}