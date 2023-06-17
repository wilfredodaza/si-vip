<?php

namespace App\Controllers\Api;

use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Traits\ResponseApiTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDocumentUpload;
use App\Models\WithholdingInvoice;
use App\Traits\DocumentSupportTrait;

class DocumentSupport extends ResourceController
{
    use ResponseApiTrait, DocumentSupportTrait;

    protected $format = 'json';



    public function providers()
    {
        if(Auth::querys()->role_id == 5) {
            $customer = new Customer();
            $companies = $customer->select([
             'companies.id',
             'companies.company as name',
             'companies.identification_number as identificationNumber', 
             'companies.dv',
             'companies.municipalities_id as municipality_id'
            ])
            ->join('companies', 'companies.id = customers.companies_id')
            ->where(['customers.email' => Auth::querys()->username])
            ->get()
            ->getResult();
                
            return $this->respond(['status' => 200, 'data' => $companies]);

        } else if(Auth::querys()->role_id == 1 || Auth::querys()->role_id == 2 || Auth::querys()->role_id == 3) {
            $customer = new Customer();
            $customers = $customer
            ->select([
                'id',
                'name',
                'customers.identification_number as identificationNumber',
                'customers.dv',
                'municipality_id'
            ])
            ->where(['companies_id' => Auth::querys()->companies_id, 'type_customer_id' => 2])
            ->get()
            ->getResult();
            
            return $this->respond(['status' => 200, 'data' => $customers]);
        }
    }

    /*public function create()
    {
        $invoice = $this->request->getJSON();    
        $data = [
            'resolution'                => NULL,
            'payment_forms_id'          => NULL,
            'payment_methods_id'        => NULL,
            'payment_due_date'          => date('Y-m-d'),
            'duration_measure'          => 0,
            'line_extesion_amount'      => 0,
            'tax_exclusive_amount'      => 0,
            'tax_inclusive_amount'      => 0,
            'allowance_total_amount'    => 0,
            'charge_total_amount'       => 0,
            'payable_amount'            => $invoice->total,
            'type_documents_id'         => Auth::querys()->role_id == 5 ? 106: 105,
            'customers_id'              =>  (Auth::querys()->role_id != 5 ? $invoice->customerId : $invoice->companyId),
            'invoice_status_id'         => 8,
            'notes'                     => $invoice->note,
            'idcurrency'                => 35,
            'calculationrate'           => 0,
            'created_at'                => date('Y-m-d H:i:s'),
            'calculationratedate'       => date('Y-m-d'),
            'user_id'                   => Auth::querys()->id,
           'seller_id'                 => NULL,
            'companies_id'              => (Auth::querys()->role_id == 5 ? $invoice->customerId : Auth::querys()->companies_id)
        ];
            
        $invoiceSupport = new Invoice();
        $invoiceSupport->save($data);

        $id = $invoiceSupport->insertId();
        if(Auth::querys()->role_id == 5) {
         
            $invoices = new Invoice();
            $invoice = $invoices
            ->select('customers.firm')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->asObject()
            ->find($id);

                
            $invoices = new Invoice();
            $invoices->update($id, ['invoice_status_id' => 9]);
        
            $data = [
                'title'         => 'firma',
                'file'          =>  $invoice->firm,
                'invoice_id'    => $id
            ];
        
            $invoiceDocumentUpload = new InvoiceDocumentUpload();
             $count = $invoiceDocumentUpload->where(['invoice_id' => $id, 'title' => 'firma'])->countAllResults();
        
            $invoiceDocumentUpload = new InvoiceDocumentUpload();
            if($count > 0) {
                if( $invoiceDocumentUpload->update($id, $data)) {
                    
                }
            }
            
            if($invoiceDocumentUpload->save($data)) {
                
            }
        }
        if($invoiceSupport->insertId()) {
             return $this->respond(['status' =>  201, 'data' => [ 'message ' => 'success created', 'invoiceId' => $id]], 201);
        }else {
            return $this->respond(['status' => 400, 'data' => 'Bat request'], 400);
        }
     
    }*/

    public function uploadFile($id = null)
    {
        if($imagefile = $this->request->getFiles()){
            foreach($imagefile['file'] as $file){          
                $newName = $file->getRandomName();
                $data = [
                    'title'         =>  $file->getName()    ,
                    'file'          =>  $newName,
                    'invoice_id'    =>  $id
                ];

                $file->move('upload/attached_document', $newName);
                $invoiceDocumentUpload = new InvoiceDocumentUpload();
                $invoiceDocumentUpload->save($data);
            }

            return $this->respond(['status' => 201, 'message' => 'success created'], 201);
        }
        return $this->respond(['status' => 201, 'message' => 'success created'], 201);
      
    }

    public function attachmentDocument($id = null) 
    {
        $invoiceDocumentUpload = new InvoiceDocumentUpload();
        $invoiceDocumentUploads = $invoiceDocumentUpload->where(['invoice_id' => $id, 'title !=' => 'firma'])->get()->getResult();
        return $this->respond(['status' => 200, 'data' => $invoiceDocumentUploads]);
    }

    public function attachmentDocumentDelete($id = null) 
    {
        $invoiceDocumentUpload = new InvoiceDocumentUpload();
        $invoiceDocumentUploads =  $invoiceDocumentUpload
        ->join('invoices', 'invoices.id = invoice_document_upload.invoice_id')
        ->where(['invoice_document_upload.id' => $id])
        ->asObject()
        ->first();
        if($invoiceDocumentUploads->invoice_status_id != 10) {
            $invoiceDocumentUpload = new InvoiceDocumentUpload();
            $invoiceDocumentUpload->delete($id);
        }
        return $this->respond(['status' => 200, 'data' => $invoiceDocumentUpload]);
    }

    public function show($id =  null)
    {
        $invoices = new Invoice();
        $invoice = $invoices->where(['id' => $id])->whereIn('type_documents_id', [105, 106])->first(); 

        return $this->respond(['status' => 200, 'data' => $invoice],200);
    }

    /*public function update($id = null)
    {

        $invoiceDatas = new Invoice();
        $invoiceData = $invoiceDatas->asObject()->find($id);

        if($invoiceData->invoice_status_id != 10) {
            $invoice = $this->request->getJSON();
        
            $data = [
                'resolution'                => NULL,
                'payment_forms_id'          => NULL,
                'payment_methods_id'        => NULL,
                'payment_due_date'          => date('Y-m-d'),
                'duration_measure'          => 0,
                'line_extesion_amount'      => 0,
                'tax_exclusive_amount'      => 0,
                'tax_inclusive_amount'      => 0,
                'allowance_total_amount'    => 0,
                'charge_total_amount'       => 0,
                'payable_amount'            => $invoice->total,
                'type_documents_id'         =>  Auth::querys()->role_id == 5 ? 106: 105,
                'customers_id'              =>  (Auth::querys()->role_id != 5 ? $invoice->customerId : $invoice->companyId),
                'invoice_status_id'         => (Auth::querys()->role_id != 5 ? 8 : 9),
                'notes'                     => $invoice->note,
                'idcurrency'                => 35,
                'calculationrate'           => 0,
                'created_at'                => date('Y-m-d H:i:s'),
                'calculationratedate'       => date('Y-m-d'),
                'user_id'                   => Auth::querys()->id,
                'seller_id'                 => NULL,
                'companies_id'              => (Auth::querys()->role_id == 5 ? $invoice->customerId : Auth::querys()->companies_id)
            ];
            
            $customers = new Customer();
            $customer = $customers->asObject()->find($data['customers_id']);
            
            $invoiceDocumentUpload = new InvoiceDocumentUpload();
            $invoiceDocumentUploads = $invoiceDocumentUpload
            ->where(['invoice_id' => $id, 'title' => 'firma'])
            ->set(['file' => $customer->firm])
            ->update();
            $invoice = new Invoice();
            if($invoice->update($id,  $data)) {
                return $this->respond(['status' =>  201, 'data' => 'sucess created'], 201);
            }else {
                return $this->respond(['status' => 400, 'data' => 'Bat request'], 400);
            }
        }else {
            return $this->respond(['status' =>  201, 'data' => 'sucess created'], 201);
        }
    }*/

    /**
     * @author wilson andres bachiller ortiz
     * @date 16/03/2021
     * @description list of withholdings
     * @param id = number 
     * return json_encode
     */

    public function   withholdings($id = null) 
    {
        
        
        $withholdings   = new WithholdingInvoice();
        $withholding    = $withholdings
        ->select([
            'withholding_invoices.percent',
            'accounting_account.name',
            'withholding_invoices.id'
        ])
        ->join('invoices', 'invoices.id = withholding_invoices.invoice_id')
        ->join('accounting_account', 'accounting_account.id = withholding_invoices.accounting_account_id')
        ->where(['withholding_invoices.invoice_id' => $id])
        ->get()
        ->getResult();


        return $this->respond([
            'status'    => 200,  
            'data'      => $withholding 
            ]);
    }

     /**
     * @author wilson andres bachiller ortiz
     * @date 16/03/2021
     * @description crear of withholdings
     * @return json_encode
     */

    public function  withholdingCreate() 
    {
        $data           = $this->request->getJSON();
        $withholdings   = new WithholdingInvoice();
        $withholdings->save($data);

        return $this->respond([
            'status' => 201, 
            'data' => ['message' => 'success created']
        ]);
        
    }

     /**
     * @author wilson andres bachiller ortiz
     * @date 16/03/2021
     * @description delete withholding
     * @param id = number 
     * @return json_encode
     */

    public function   withholdingDelete($id = null) 
    {
        $withholdings  = new WithholdingInvoice();
        $withholding = $withholdings->join('invoices', 'invoices.id = withholding_invoices.invoice_id')
        ->where(['invoices.companies_id' => Auth::querys()->companies_id, 'withholding_invoices.id' => $id])
        ->asObject()
        ->find();

        if(count($withholding) == 0) {
            return $this->respond([
                'status' => 401, 
                'data' => ['message' => 'Not found']
            ]);
        }
        

        $withholdings  = new WithholdingInvoice();
        $withholdings->delete($id);
        return $this->respond([
            'status' => 200, 
            'data' => ['message' => 'success delete']
        ]);

    }

    public function withholdingUpdate($id = null)
    {
        $data           = $this->request->getJSON();
        $withholdings   = new WithholdingInvoice();
        $withholdings->update($id, $data);
        return $this->respond([
            'status' => 200, 
            'data' => ['message' => 'success update']
        ]);
    }

    /**
     * Método POST encargado de guardar la información del documento soporte.
     * @return \CodeIgniter\HTTP\Response|mixed
     * @throws \ReflectionException
     */
    public function create()
    {
        $data = $this->request->getJSON();
        $model = new Invoice();
        $invoiceId = $model->insert([
            'type_documents_id'         => 11,
            'invoice_status_id'         => 8,
            'companies_id'              => Auth::querys()->companies_id,
            'customers_id'              => $data->customer_id,
            'payment_forms_id'          => $data->payment_form->payment_form_id,
            'payment_methods_id'        => $data->payment_form->payment_method_id,
            'payment_due_date'          => $data->payment_form->payment_due_date,
            'duration_measure'          => $data->payment_form->duration_measure,
            'line_extesion_amount'      => $data->legal_monetary_totals->line_extension_amount,
            'tax_exclusive_amount'      => $data->legal_monetary_totals->tax_exclusive_amount,
            'tax_inclusive_amount'      => $data->legal_monetary_totals->tax_inclusive_amount,
            'allowance_total_amount'    => $data->legal_monetary_totals->allowance_total_amount,
            'charge_total_amount'       => $data->legal_monetary_totals->charge_total_amount,
            'notes'                     => $data->notes,
            'pre_paid_amount'           => 0,
            'payable_amount'            => $data->legal_monetary_totals->payable_amount,
        ]);


        foreach($data->invoice_lines as $line) {
            $model = new LineInvoice();
            $lineInvoiceId = $model->insert([
                'invoices_id'                       => $invoiceId,
                'discounts_id'                      => $line->allowance_charges[0]->discount_id,
                'products_id'                       => $line->product_id,
                'discount_amount'                   => (double) $line->allowance_charges[0]->amount,
                'quantity'                          => $line->invoiced_quantity,
                'line_extension_amount'             => (double) $line->line_extension_amount,
                'price_amount'                      => (double) $line->price_amount,
                'description'                       => $line->description,
                'type_generation_transmition_id'    => $line->type_generation_transmition_id,
                'start_date'                        => $line->start_date
            ]);

            foreach ($line->tax_totals as $tax) {
                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id'      => $lineInvoiceId,
                    'taxes_id'              => $tax->tax_id,
                    'tax_amount'            => $tax->tax_amount,
                    'taxable_amount'        => $tax->taxable_amount,
                    'percent'               => $tax->percent
                ]);
            }
        }

        $this->createDocument($invoiceId, null , true);

        return $this->messageCreate($data);

    }

    /**
     * Método GET encargado de traer la información de por medio del id del documento soporte.
     * @param null $id id del documento soporte
     * @return \CodeIgniter\HTTP\Response|mixed
     */
    public function edit($id = null)
    {
        $model = new Invoice();
        $invoice = $model->select([
            'invoices.payment_methods_id',
            'invoices.payment_forms_id',
            'invoices.payment_due_date',
            'invoices.duration_measure',
            'invoices.customers_id',
            'invoices.notes'
        ])
            ->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

        $data = [];
        $data['payment_form']['payment_method_id']  = $invoice->payment_methods_id;
        $data['payment_form']['payment_form_id']    = $invoice->payment_forms_id;
        $data['payment_form']['duration_measure']   = $invoice->duration_measure;
        $data['payment_form']['payment_due_date']   = $invoice->payment_due_date;
        $data['customer_id']                        = $invoice->customers_id;
        $data['note']                               = $invoice->notes;


        $model = new LineInvoice();
        $lineInvoice = $model->where(['invoices_id' => $id])
            ->asObject()
            ->get()
            ->getResult();

        $i = 0;
        foreach ($lineInvoice as $line) {
            $data['invoice_lines'][$i]['unit_measure_id']                        = 70;
            $data['invoice_lines'][$i]['product_id']                             = (int) $line->products_id;
            $data['invoice_lines'][$i]['price_amount']                           = (double) $line->price_amount;
            $data['invoice_lines'][$i]['invoiced_quantity']                      = (double) $line->quantity;
            $data['invoice_lines'][$i]['line_extension_amount']                  = (double) $line->line_extension_amount;
            $data['invoice_lines'][$i]['description']                            = $line->description;
            $data['invoice_lines'][$i]['allowance_charges'][0]['amount']         = (double) $line->discount_amount;
            $data['invoice_lines'][$i]['allowance_charges'][0]['base_amount']    = (double) $line->line_extension_amount + $line->discount_amount;
            $data['invoice_lines'][$i]['type_generation_transmition_id']         = $line->type_generation_transmition_id;
            $data['invoice_lines'][$i]['start_date']                             = $line->start_date;
            $model = new LineInvoiceTax();
            $taxs = $model->where(['line_invoices_id' => $line->id])->get()->getResult();
            $l = 0;
            foreach ($taxs as $tax) {
                $data['invoice_lines'][$i]['tax_totals'][$l]['tax_id']              = $tax->taxes_id;
                $data['invoice_lines'][$i]['tax_totals'][$l]['percent']             = $tax->percent;
                $data['invoice_lines'][$i]['tax_totals'][$l]['taxable_amount']      = $tax->taxable_amount;
                $data['invoice_lines'][$i]['tax_totals'][$l]['tax_amount']          = $tax->tax_amount;
                $l++;
            }
            $i++;
        }

        return $this->messageSuccess($data);
    }


    /**
     * Método PUT encaragado de actualizar los datos de documento soporte.
     * @param string|null $id
     * @return \CodeIgniter\HTTP\Response|mixed
     */
    public function update($id =null )
    {
        $data = $this->request->getJSON();
        $model = new Invoice();
        $invoiceId = $model
            ->set('notes', $data->notes)
            ->set('type_documents_id', 11)
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
            ->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id])
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
                'discount_amount'                   => $line->allowance_charges[0]->amount,
                'quantity'                          => $line->invoiced_quantity,
                'line_extension_amount'             => $line->line_extension_amount,
                'price_amount'                      => $line->price_amount,
                'description'                       => $line->description,
                'type_generation_transmition_id'    => $line->type_generation_transmition_id,
                'start_date'                        => $line->start_date
            ]);

            foreach ($line->tax_totals as $tax) {
                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id'      => $lineInvoiceId,
                    'taxes_id'              => $tax->tax_id,
                    'tax_amount'            => $tax->tax_amount,
                    'taxable_amount'        => $tax->taxable_amount,
                    'percent'               => $tax->percent
                ]);
            }
        }
        return $this->messageSuccess($data);
    }


}