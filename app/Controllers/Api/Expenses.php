<?php

namespace App\Controllers\Api;

use App\Controllers\HeadquartersController;
use App\Models\Expense_type;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Traits\ResponseApiTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Customer;

class Expenses extends ResourceController
{
    use ResponseApiTrait;

    protected $format = 'json';
    protected $controllerHeadquarters;
    protected $invoicesTable;
    protected $companiesTable;
    protected $customersTable;

    public function __construct()
    {
        $this->controllerHeadquarters = new HeadquartersController();
        $this->invoicesTable = new Invoice();
        $this->companiesTable = new Company();
        $this->customersTable = new Customer();
    }

    public function headquarters()
    {
        $customers = $this->customersTable
            ->select([
                'id',
                'name',
                'type_document_identifications_id as type_document_identification_id',
                'identification_number',
                'dv',
                'phone',
                'address',
                'email',
                'email2',
                'email3',
                'merchant_registration',
                'type_customer_id',
                'type_regime_id',
                'municipality_id',
                'type_organization_id',
                'status',
                'created_at',
                'updated_at',
                'user_id'
            ])
            ->where([
                'companies_id'          => Auth::querys()->companies_id,
                'type_customer_id'    => 2,
                'status'            => 'Activo'
            ])
            ->findAll();

        return $this->respond([
            'status' =>  200 ,
            'data' => $customers
        ], 200);
    }


    /**
     * Método POST encargado de guardar la información del pago.
     * @return \CodeIgniter\HTTP\Response|mixed
     * @throws \ReflectionException
     */
    public function create()
    {
        $data = $this->request->getJSON();
        $model = new Invoice();
        $invoiceId = $model->insert([
            'type_documents_id'         => 118,
            'invoice_status_id'         => 8,
            'companies_id'              => Auth::querys()->companies_id,
            'customers_id'              => $data->customer_id,
            'seller_id'                 => $data->seller_id,
            'payment_forms_id'          => 1,
            'payment_methods_id'        => 10,
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

        //$this->createDocument($invoiceId, null , true);

        return $this->messageCreate($data);

    }

    /**
     * Método GET encargado de traer la información de por medio del id del pago.
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
            'invoices.notes',
            'invoices.seller_id'
        ])
            ->where(['id' => $id])
            ->asObject()
            ->first();

        $data = [];
        $data['payment_form']['payment_method_id']  = $invoice->payment_methods_id;
        $data['payment_form']['payment_form_id']    = $invoice->payment_forms_id;
        $data['payment_form']['duration_measure']   = $invoice->duration_measure;
        $data['payment_form']['payment_due_date']   = $invoice->payment_due_date;
        $data['customer_id']                        = $invoice->customers_id;
        $data['note']                               = $invoice->notes;
        $data['seller_id']                        = $invoice->seller_id;


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
            ->set('type_documents_id', 118)
            ->set('customers_id', $data->customer_id)
            ->set('seller_id', (empty($data->seller_id) || $data->seller_id == 0)? null :$data->seller_id)
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