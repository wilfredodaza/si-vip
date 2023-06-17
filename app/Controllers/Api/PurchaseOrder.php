<?php


namespace App\Controllers\Api;


use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\TrackingCustomer;
use App\Traits\DocumentTrait;
use App\Controllers\ApiController;
use App\Traits\ResponseApiTrait;
use CodeIgniter\RESTful\ResourceController;
use App\Models\Config as ConfigModel;


class PurchaseOrder extends ResourceController
{
    use ResponseApiTrait, DocumentTrait;

    protected $format = 'json';
    protected $tableInvoices;
    protected $tableLineInvoices;
    protected $api;
    protected $tableTracking;

    public function __construct()
    {
        $this->tableInvoices = new Invoice();
        $this->tableLineInvoices = new LineInvoice();
        $this->api = new ApiController();
        $this->tableTracking = new TrackingCustomer();
    }

    public function create()
    {
        $data = $this->request->getJSON();
        $model = new Invoice();
        $invoiceId = $model->insert([
            'type_documents_id' => 114,
            'invoice_status_id' => 5,
            'resolution' => $data->number,
            'companies_id' => Auth::querys()->companies_id,
            'customers_id' => $data->customer_id,
            'payment_forms_id' => $data->payment_form->payment_form_id,
            'payment_methods_id' => $data->payment_form->payment_method_id,
            'payment_due_date' => $data->payment_form->payment_due_date,
            'duration_measure' => $data->payment_form->duration_measure,
            'line_extesion_amount' => $data->legal_monetary_totals->line_extension_amount,
            'tax_exclusive_amount' => $data->legal_monetary_totals->tax_exclusive_amount,
            'tax_inclusive_amount' => $data->legal_monetary_totals->tax_inclusive_amount,
            'allowance_total_amount' => $data->legal_monetary_totals->allowance_total_amount,
            'charge_total_amount' => $data->legal_monetary_totals->charge_total_amount,
            'pre_paid_amount' => 0,
            'payable_amount' => $data->legal_monetary_totals->payable_amount,
            'user_id' => Auth::querys()->id,
            'created_at' => date('Y-m-d H:i:s')
        ]);


        foreach ($data->invoice_lines as $line) {
            $model = new LineInvoice();
            $lineInvoiceId = $model->insert([
                'invoices_id' => $invoiceId,
                'discounts_id' => $line->allowance_charges[0]->discount_id,
                'products_id' => $line->product_id,
                'discount_amount' => (double)$line->allowance_charges[0]->amount,
                'quantity' => $line->invoiced_quantity,
                'line_extension_amount' => (double)$line->line_extension_amount,
                'price_amount' => (double)$line->price_amount,
                'description' => $line->description,
                'type_generation_transmition_id' => null,
                'start_date' => null
            ]);

            foreach ($line->tax_totals as $tax) {
                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id' => $lineInvoiceId,
                    'taxes_id' => $tax->tax_id,
                    'tax_amount' => $tax->tax_amount,
                    'taxable_amount' => $tax->taxable_amount,
                    'percent' => $tax->percent
                ]);
            }
        }

        // $this->api->preview(Auth::querys()->companies_id, $invoiceId);

        return $this->messageCreate($data);

    }

    public function edit($id = null)
    {
        return view('PurchaseOrder/edit', ['id' => $id]);
    }

    public function update($id = null)
    {
        $data = $this->request->getJSON();
        $invoiceId = $this->tableInvoices
            ->set('type_documents_id', 114)
            ->set('customers_id', $data->customer_id)
            ->set('payment_forms_id', $data->payment_form->payment_form_id)
            ->set('payment_methods_id', $data->payment_form->payment_method_id)
            ->set('payment_due_date', $data->payment_form->payment_due_date)
            ->set('duration_measure', $data->payment_form->duration_measure)
            ->set('line_extesion_amount', $data->legal_monetary_totals->line_extension_amount)
            ->set('tax_exclusive_amount', $data->legal_monetary_totals->tax_exclusive_amount)
            ->set('tax_inclusive_amount', $data->legal_monetary_totals->tax_inclusive_amount)
            ->set('allowance_total_amount', $data->legal_monetary_totals->allowance_total_amount)
            ->set('charge_total_amount', $data->legal_monetary_totals->charge_total_amount)
            ->set('pre_paid_amount', 0)
            ->set('payable_amount', $data->legal_monetary_totals->payable_amount)
            ->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id])
            ->update();

        $lineInvoice = new LineInvoice();
        $lineInvoices = $this->tableLineInvoices->where(['invoices_id' => $id])
            ->get()
            ->getResult();

        foreach ($lineInvoices as $lines) {
            $model = new LineInvoiceTax();
            $model->where(['line_invoices_id' => $lines->id])->delete();
            $lineInvoice->delete($lines->id);
        }


        foreach ($data->invoice_lines as $line) {
            $lineInvoiceId = $this->tableLineInvoices->insert([
                'invoices_id' => $id,
                'discounts_id' => 1,
                'products_id' => $line->product_id,
                'discount_amount' => $line->allowance_charges[0]->amount,
                'quantity' => $line->invoiced_quantity,
                'line_extension_amount' => $line->line_extension_amount,
                'price_amount' => $line->price_amount,
                'description' => $line->description,
                'type_generation_transmition_id' => null,
                'start_date' => null
            ]);

            foreach ($line->tax_totals as $tax) {
                $model = new LineInvoiceTax();
                $model->insert([
                    'line_invoices_id' => $lineInvoiceId,
                    'taxes_id' => $tax->tax_id,
                    'tax_amount' => $tax->tax_amount,
                    'taxable_amount' => $tax->taxable_amount,
                    'percent' => $tax->percent
                ]);
            }
        }

        //$this->api->preview(Auth::querys()->companies_id, $id);

        return $this->messageSuccess($data);
    }

    public function generateTracking($id, $process = null, $messages = null)
    {
        if($process == 'create'){
            $message = 'El usuario ' . session('user')->username . ' Genero la remisión con Id # ' . $id . ' el dia ' . date('Y-m-d H:i:s') . '.';
            $message .= $messages;
            $data = [
                'message' => $message,
                'username' => session('user')->username,
                'created_at' => date('Y-m-d H:i:s'),
                'table_id' => $id,
                'companies_id' => session('user')->companies_id,
                'type_tracking_id' => 4,
            ];
            $this->tableTracking->save($data);
        }

        if($process == 'tracking'){
            $data = [
                'message' => $messages,
                'username' => session('user')->username,
                'created_at' => date('Y-m-d H:i:s'),
                'table_id' => $id,
                'companies_id' => session('user')->companies_id,
                'type_tracking_id' => 4,
            ];
            $this->tableTracking->save($data);
        }

        if ($process == 'close') {
            $message = 'El usuario ' . session('user')->username . ' hace el cierre de la orden de compra el dia ' . date('Y-m-d H:i:s') . '.';
            $message .= $messages;
            $this->tableInvoices->update(['id' => $id], ['invoice_status_id' => 6]);
            $data = [
                'message' => $message,
                'username' => session('user')->username,
                'created_at' => date('Y-m-d H:i:s'),
                'table_id' => $id,
                'companies_id' => session('user')->companies_id,
                'type_tracking_id' => 4,
            ];
            $this->tableTracking->save($data);
        }
    }

    public function close($id = null)
    {
        $invoice = new Invoice();
        $invoice->update(['id' => $id], ['invoice_status_id' => 6]);


        $tracking = new TrackingCustomer();
        $data = [
            'message' => 'El usuario ' . session('user')->username . ' hace el cierre de la cotizacion el dia ' . date('Y-m-d H:i:s') . '.',
            'username' => session('user')->username,
            'created_at' => date('Y-m-d H:i:s'),
            'table_id' => $id,
            'companies_id' => session('user')->companies_id,
            'type_tracking_id' => 1,
        ];
        $tracking->save($data);

        return $this->respond(['status' => 200]);
    }

    public function invoice($id = null)
    {
        $model = new \App\Models\Invoice();
        $invoice = $model->where(['id' => $id])
            ->asObject()
            ->first();

        if (is_null($invoice)) {
            return $this->respond(['status' => 404, 'code' => 404, 'data' => 'Not Found']);
        }

        $data = [];
        $data['number'] = $invoice->resolution;
        $data['resolution'] = $invoice->resolution_id;
        $data['delevery_term_id'] = $invoice->delevery_term_id;
        $data['currency_id'] = $invoice->idcurrency;
        $data['currency_rate'] = (int)$invoice->calculationrate;
        $data['currency_rate_date'] = $invoice->calculationratedate;
        $data['notes'] = $invoice->notes;
        $data['type_document_id'] = (int)$invoice->type_documents_id;
        $data['customer_id'] = $invoice->customers_id;
        $data['payment_form']['payment_form_id'] = $invoice->payment_forms_id;
        $data['payment_form']['payment_method_id'] = $invoice->payment_methods_id;
        $data['payment_form']['payment_due_date'] = $invoice->payment_due_date;
        $data['payment_form']['duration_measure'] = $invoice->duration_measure;

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
                'line_invoices.discount_amount',
            ])
            ->join('products', 'products.id = line_invoices.products_id')
            ->where(['invoices_id' => $id])
            ->asObject()
            ->findAll();


        $i = 0;
        foreach ($lineInvoice as $item) {
            $data['invoice_lines'][$i]['product_id'] = $item->products_id;
            $data['invoice_lines'][$i]['invoice_line_id'] = $item->id;
            $data['invoice_lines'][$i]['unit_measure_id'] = 70;
            $data['invoice_lines'][$i]['invoiced_quantity'] = (int)$item->quantity;
            $data['invoice_lines'][$i]['line_extension_amount'] = (int)$item->line_extension_amount;
            $data['invoice_lines'][$i]['free_of_charge_indicator'] = $item->free_of_charge_indicator;
            $data['invoice_lines'][$i]['description'] = $item->description;
            $data['invoice_lines'][$i]['code'] = $item->code;
            $data['invoice_lines'][$i]['type_item_identification_id'] = 4;
            $data['invoice_lines'][$i]['base_quantity'] = (int)$item->quantity;
            $data['invoice_lines'][$i]['name'] = $item->name;
            $data['invoice_lines'][$i]['price_amount'] = (int)$item->price_amount;
            $data['invoice_lines'][$i]['provider_id'] = $item->provider_id;
            $data['invoice_lines'][$i]['allowance_charges'][0]['id'] = 0;
            $data['invoice_lines'][$i]['allowance_charges'][0]['discount_id'] = 12;
            $data['invoice_lines'][$i]['allowance_charges'][0]['charge_indicator'] = false;
            $data['invoice_lines'][$i]['allowance_charges'][0]['allowance_charge_reason'] = 'Descuento General';
            $data['invoice_lines'][$i]['allowance_charges'][0]['amount'] = (int)$item->discount_amount;
            $data['invoice_lines'][$i]['allowance_charges'][0]['base_amount'] = $item->price_amount * $item->quantity;
            $data['invoice_lines'][$i]['allowance_charges'][0]['type'] = 0;
            $data['invoice_lines'][$i]['allowance_charges'][0]['percentage'] = (100 * $item->discount_amount) / (($item->price_amount * $item->quantity) / $item->quantity);
            $data['invoice_lines'][$i]['allowance_charges'][0]['value_total'] = (int)$item->discount_amount / $item->quantity;
            $l = 0;
            $model = new LineInvoiceTax();
            $lineInvoiceTax = $model->where(['line_invoices_id' => $item->id])
                ->asObject()
                ->findAll();
            foreach ($lineInvoiceTax as $item2) {
                $data['invoice_lines'][$i]['tax_totals'][$l]['tax_id'] = (int)$item2->taxes_id;
                $data['invoice_lines'][$i]['tax_totals'][$l]['tax_amount'] = (int)$item2->tax_amount;
                $data['invoice_lines'][$i]['tax_totals'][$l]['percent'] = (int)$item2->percent;
                $data['invoice_lines'][$i]['tax_totals'][$l]['taxable_amount'] = (int)$item2->taxable_amount;
                $l++;
            }

            $i++;
        }

        $data['legal_monetary_totals']['line_extension_amount'] = $invoice->line_extesion_amount;
        $data['legal_monetary_totals']['tax_exclusive_amount'] = $invoice->tax_exclusive_amount;
        $data['legal_monetary_totals']['tax_inclusive_amount'] = $invoice->tax_inclusive_amount;
        $data['legal_monetary_totals']['allowance_total_amount'] = $invoice->allowance_total_amount;
        $data['legal_monetary_totals']['charge_total_amount'] = $invoice->charge_total_amount;
        $data['legal_monetary_totals']['payable_amount'] = $invoice->payable_amount;

        return $this->respond(['status' => 201, 'code' => 201, 'data' => $data]);
    }

}
