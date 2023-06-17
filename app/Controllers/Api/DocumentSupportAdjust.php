<?php

namespace App\Controllers\Api;


use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Traits\DocumentSupportTrait;
use App\Traits\ResponseApiTrait;
use CodeIgniter\RESTful\ResourceController;


class DocumentSupportAdjust extends ResourceController
{
    use ResponseApiTrait, DocumentSupportTrait;

    public function edit($id = null)
    {
        $model = new Invoice();
        $invoice = $model->select([
            'invoices.payment_methods_id',
            'invoices.payment_forms_id',
            'invoices.payment_due_date',
            'invoices.duration_measure',
            'invoices.customers_id',
            'invoices.prefix',
            'invoices.resolution',
            'invoices.uuid',
            'invoices.notes',
            'invoices.type_documents_id',
            'invoices.created_at'
        ])
            ->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

        $data = [];
        $data['billing_reference']['prefix']        = $invoice->prefix;
        $data['billing_reference']['number']        = $invoice->resolution;
        $data['billing_reference']['uuid']          = $invoice->uuid;
        $data['billing_reference']['issue_date']    = explode(' ', $invoice->created_at)[0];

        $data['type_document_id']                   = $invoice->type_documents_id;
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


    public function create()
    {
        $data = $this->request->getJSON();
        $model = new Invoice();
        $invoiceId = $model->insert([
            'type_documents_id'         => 13,
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
            'pre_paid_amount'           => 0,
            'payable_amount'            => $data->legal_monetary_totals->payable_amount,
            'resolution_credit'         => $data->billing_reference->prefix.$data->billing_reference->number,
            'uuid'                      => $data->billing_reference->uuid,
            'issue_date'                => $data->billing_reference->issue_date,
            'discrepancy_response_id'   => $data->discrepancyresponsecode
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

}