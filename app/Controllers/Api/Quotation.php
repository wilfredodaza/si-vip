<?php


namespace App\Controllers\Api;

use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\TrackingCustomer;
use App\Traits\DocumentTrait;
use App\Traits\ResponseApiTrait;
use App\Traits\ValidationsTrait2;
use App\Controllers\ApiController;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Config as ConfigModel;



class Quotation extends ResourceController
{
    use DocumentTrait, ValidationsTrait2, ResponseApiTrait;

    protected $format = 'json';

    public function store()
    {
        $json = $this->request->getJSON();
        $model = new \App\Models\Invoice();
        $invoice = $model->insert([
            'resolution'                => $this->quatation(),
            'resolution_id'             => $json->type_document_id,
            'payment_forms_id'          => $json->payment_form->payment_form_id,
            'payment_methods_id'        => $json->payment_form->payment_method_id,
            'payment_due_date'          => ($json->payment_form->duration_measure == 0) ? date('Y-m-d') : $json->payment_form->payment_due_date,
            'duration_measure'          => $json->payment_form->duration_measure,
            'type_documents_id'         => 100,
            'line_extesion_amount'      => $json->legal_monetary_totals->line_extension_amount,
            'tax_exclusive_amount'      => $json->legal_monetary_totals->tax_exclusive_amount,
            'tax_inclusive_amount'      => $json->legal_monetary_totals->tax_inclusive_amount,
            'allowance_total_amount'    => $json->legal_monetary_totals->allowance_total_amount,
            'charge_total_amount'       => $json->legal_monetary_totals->charge_total_amount,
            'payable_amount'            => $json->legal_monetary_totals->payable_amount,
            'customers_id'              => $json->customer_id,
            'created_at'                => $json->created_at.' '.date('H:i:s'),
            'issue_date'                => date('Y-m-d'),
            'invoice_status_id'         => 5,
            'notes'                     => $json->notes,
            'companies_id'              => Auth::querys()->companies_id,
            'idcurrency'                => isset($json->currency_id) ? $json->currency_id : 35,
            'calculationrate'           => isset($json->currency_rate) ? (float) $json->currency_rate : 1,
            'calculationratedate'       => isset($json->currency_rate_date) ? $json->currency_rate_date: date('Y-m-d'),
            'status_wallet'             => 'Pendiente',
            'user_id'                   => Auth::querys()->id,
            'seller_id'                 => isset($json->seller_id) ? $json->seller_id : null,
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
                'provider_id'           => isset($value->provider_id) ? $value->provider_id : null,
                'cost_center_id'        => $json->cost_center_id == 0 ? null :$json->cost_center_id
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
                $lineInvoiceTax = new LineInvoiceTax();
                $lineInvoiceTax->insert($tax);
            }
        }

        $json->id = $id;
        if ($id) {
            $api = new ApiController();
            $api->preview2(Auth::querys()->companies_id, $id);
            return $this->respond(['status' => 201, 'code' => 201, 'data' => $json]);
        }
    }

    public function quatation()
    {
        $id = 0;
        $invoice = new Invoice();
        $data = $invoice->select('resolution')
            ->where(['type_documents_id' => 100, 'companies_id' => Auth::querys()->companies_id])
            ->orderBy('id', 'desc')
            ->get()
            ->getResult();

        if(count($data) > 0) {
            $id = $data[0]->resolution;
        }
        return $id + 1;
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON();
        $model = new \App\Models\Invoice();
        $invoiceId = $model
            ->set('notes', $data->notes)
            ->set('resolution_id', $data->type_document_id)
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
                'discount_amount'                   => (float) $line->allowance_charges[0]->amount,
                'quantity'                          => $line->invoiced_quantity,
                'line_extension_amount'             => (float) $line->line_extension_amount,
                'provider_id'                       => isset($line->provider_id) ? $line->provider_id : null,
                'price_amount'                      => (float) $line->price_amount,
                'description'                       => $line->description,
                'type_generation_transmition_id'    => null,
                'start_date'                        => null,
                // 'cost_center_id'                    => ($line->cost_center_id == 0 ? null : $line->cost_center_id)
            ]);


            foreach ($line->tax_totals as $tax) {
                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id'      => $lineInvoiceId,
                    'taxes_id'              => (int) $tax->tax_id,
                    'tax_amount'            => (float) $tax->tax_amount,
                    'taxable_amount'        => (float) $tax->taxable_amount,
                    'percent'               => (float) $tax->percent
                ]);
            }

        }

        $api = new ApiController();
        $api->preview2(Auth::querys()->companies_id, $id);
        $this->respond([
            'status' => 201,
            'messge' => 'Create.',
            'data' => $data
        ], 201);
    }
}