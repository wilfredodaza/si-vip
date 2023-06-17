<?php


namespace App\Controllers;


use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;

class NoteDebitController extends BaseController
{
    public function index($id)
    {
        echo view('angular/note_debit', ['id' => $id]);
    }

    public function getInvoice($id)
    {
        $invoice = new Invoice();
        $invoice = $invoice
            ->select('*, invoices.id as id_invoice')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();
        $data = [];
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
        http_response_code(200);
        echo json_encode($data);
        die();
    }

    public function getLineInvoice($id)
    {
        $products = new LineInvoice();
        $lineInvoices = $products
            ->select('*, products.id as products_id')
            ->join('products', 'line_invoices.products_id = products.id')
            ->where(['line_invoices.invoices_id' => $id])
            ->get()
            ->getResult();
        $i = 0;

        foreach ($lineInvoices as  $key) {
            $data[$i]['product_id'] = $key->products_id;
            $data[$i]['code'] = $key->code;
            $data[$i]['name'] = $key->description;
            $data[$i]['price_amount'] = $key->price_amount;
            $data[$i]['description'] = $key->name;
            $data[$i]['unit_measure_id'] = $key->unit_measures_id;
            $data[$i]['type_item_identification_id'] = $key->type_item_identifications_id;
            $data[$i]['base_quantity'] = 1;
            $data[$i]['free_of_charge_indicator'] = false;
            $data[$i]['reference_price_id'] = $key->reference_prices_id;
            $data[$i]['value'] = (int)$key->line_extension_amount;
            $data[$i]['invoiced_quantity'] = (int)$key->quantity;
            $data[$i]['allowance_charges'][0]['valor'] = (int)$key->discount_amount;
            $data[$i]['allowance_charges'][0]['charge_indicator'] = false;
            $data[$i]['allowance_charges'][0]['amount'] = (int)$data[$i]['allowance_charges'][0]['valor'];
            $data[$i]['allowance_charges'][0]['base_amount'] = (int)$key->valor;
            $data[$i]['allowance_charges'][0]['discount_id'] = 1;//key->discounts_id;
            $data[$i]['allowance_charges'][0]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
            $lineInvoicesTax = new LineInvoiceTax();
            $lineInvoiceTax = $lineInvoicesTax->where(['line_invoices_id' => $key->id])
                ->get()
                ->getResult();
            foreach ($lineInvoiceTax as $value) {
                if ($value->taxes_id == 1) {
                    $data[$i]['tax_totals'][0]['tax_amount'] = (int)$value->tax_amount;
                    $data[$i]['tax_totals'][0]['taxable_amount'] = (int)$value->taxable_amount;
                    $data[$i]['tax_totals'][0]['percent'] = (int)$value->percent;
                    $data[$i]['tax_totals'][0]['tax_id'] = (int)$value->taxes_id;
                }
            }

            $i++;
        }
        http_response_code(200);
        echo json_encode($data);
        die();
    }

}