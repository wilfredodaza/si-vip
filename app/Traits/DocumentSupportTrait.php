<?php

namespace App\Traits;

use App\Controllers\Api\Auth;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Resolution;

trait DocumentSupportTrait
{
    public $_model;
    private $_lineInvoice;
    public $_withHoldingTaxTotals = [];
    public $_withHoldingTaxTotalsGroup = [];
    public $_taxTotals = [];
    public $_taxTotalsGroup = [];
    public $_data = [];
    public $_positionTaxes = 0;
    public $_positionWithHoldingTax = 0;
    public $_token = '';
    public $i = 0;


    /**
     * Query of invoice, Line invoices and taxes
     * @param int $id Id Invoice
     */
    public function request(int $id)
    {
        $model = new Invoice();
        $this->_invoice = $model
            ->select([
                'invoices.resolution',
                'invoices.resolution_id',
                'invoices.prefix',
                'invoices.type_documents_id',
                'invoices.payment_forms_id',
                'invoices.payment_methods_id',
                'invoices.duration_measure',
                'invoices.payment_due_date',
                'invoices.resolution_credit',
                'invoices.uuid',
                'invoices.issue_date',
                'invoices.line_extesion_amount',
                'invoices.tax_exclusive_amount',
                'invoices.tax_inclusive_amount',
                'invoices.allowance_total_amount',
                'invoices.charge_total_amount',
                'invoices.payable_amount',
                'customers.identification_number as customer_identification_number',
                'customers.dv as customer_dv',
                'customers.name as customer_name',
                'customers.phone as customer_phone',
                'customers.address as customer_address',
                'customers.email as customer_email',
                'customers.merchant_registration as customer_merchant_registration',
                'customers.type_document_identifications_id as customer_type_document_identification_id',
                'customers.type_organization_id as customer_type_organizations_id',
                'customers.municipality_id as customer_municipality_id',
                'customers.type_regime_id as customer_type_regime_id',
                'invoices.id',
                'companies.token',
                'customers.postal_code',
                'invoices.issue_date',
                'invoices.discrepancy_response_id',
                'invoices.notes',
                'invoices.created_at'
            ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();


        $model = new LineInvoice();
        $this->_lineInvoice = $model->select([
            'line_invoices.id',
            'line_invoices.quantity',
            'line_invoices.line_extension_amount',
            'line_invoices.description',
            'products.code as product_code',
            'line_invoices.price_amount',
            'line_invoices.provider_id',
            'line_invoices.discount_amount',
            'line_invoices.start_date',
            'line_invoices.type_generation_transmition_id'
        ])
            ->join('products', 'products.id = line_invoices.products_id')
            ->where(['invoices_id' => $id])
            ->get()
            ->getResult();


    }


    public function createDocument($id, $resolution, $previsualization = false)
    {
        $this->request($id);
        if ($this->_invoice->type_documents_id == 11) {
            $this->resolution($resolution, 11, $previsualization);
            $this->_invoice();
            $this->_seller();
            $this->_paymentForm();
            $this->_monetaryTotals('legal_monetary_totals');
            $this->_lineInvoice('invoice_lines');
            $this->withHoldingTaxTotal();
            $this->taxTotal();
            return $this->_data;
        } else if ($this->_invoice->type_documents_id == 13) {
            $this->resolution($resolution, 13, $previsualization);
            $this->billing_reference();
            $this->_invoice();
            $this->_seller();
            $this->_paymentForm();
            $this->_monetaryTotals('legal_monetary_totals');
            $this->_lineInvoice('credit_note_lines');
            $this->withHoldingTaxTotal();
            $this->taxTotal();
            return $this->_data;
        }

    }


    /**
     * Datos del encabezados de la factura
     * @return void
     */
    private function _invoice()
    {
        $dateCreation                    = explode(' ', $this->_invoice->created_at);
        $this->_data['type_document_id'] = $this->_invoice->type_documents_id;
        $this->_data['date']             = $dateCreation[0];
        $this->_data['time']             = $dateCreation[1];
        $this->_data['sendmail']         = false;
        $this->_data['notes']            = $this->_invoice->notes;
        $this->_token                    = $this->_invoice->token;
    }

    private function _seller()
    {
        // colocar cunsimidor final
        $this->_data['seller']['identification_number']             = $this->_invoice->customer_identification_number;
        $this->_data['seller']['dv']                                = $this->_invoice->customer_dv;
        $this->_data['seller']['name']                              = $this->_invoice->customer_name;
        $this->_data['seller']['phone']                             = $this->_invoice->customer_phone;
        $this->_data['seller']['address']                           = $this->_invoice->customer_address;
        $this->_data['seller']['email']                             = $this->_invoice->customer_email;
        $this->_data['seller']['merchant_registration']             = $this->_invoice->customer_merchant_registration;
        $this->_data['seller']['type_document_identification_id']   = $this->_invoice->customer_type_document_identification_id;
        $this->_data['seller']['type_organization_id']              = $this->_invoice->customer_type_organizations_id;
        $this->_data['seller']['municipality_id']                   = $this->_invoice->customer_municipality_id;
        $this->_data['seller']['type_regime_id']                    = $this->_invoice->customer_type_regime_id;
        $this->_data['seller']['postal_zone_code']                  = $this->_invoice->postal_code;
    }

    public function _paymentForm()
    {
        $this->_data['payment_form']['payment_form_id'] = $this->_invoice->payment_forms_id;
        $this->_data['payment_form']['payment_method_id'] = $this->_invoice->payment_methods_id;
        $this->_data['payment_form']['payment_due_date'] = $this->_invoice->payment_due_date;
        $this->_data['payment_form']['duration_measure'] = $this->_invoice->duration_measure;
    }

    public function _monetaryTotals(string $type)
    {
        $this->_data[$type]['line_extension_amount'] = $this->_invoice->line_extesion_amount;
        $this->_data[$type]['tax_exclusive_amount'] = $this->_invoice->tax_exclusive_amount;
        $this->_data[$type]['tax_inclusive_amount'] = $this->_invoice->tax_inclusive_amount;
        $this->_data[$type]['allowance_total_amount'] = $this->_invoice->allowance_total_amount;
        $this->_data[$type]['charge_total_amount'] = $this->_invoice->charge_total_amount;
        $this->_data[$type]['payable_amount'] = $this->_invoice->payable_amount;
    }

    public function _lineInvoice(string $type)
    {
        $i = 0;
        foreach ($this->_lineInvoice as $item) {
            $this->_data[$type][$i]['unit_measure_id'] = 70;
            $this->_data[$type][$i]['invoiced_quantity'] = $item->quantity;
            $this->_data[$type][$i]['line_extension_amount'] = $item->line_extension_amount;
            $this->_data[$type][$i]['free_of_charge_indicator'] = false;
            $this->_data[$type][$i]['description'] = $item->description;
            $this->_data[$type][$i]['code'] = $item->product_code;
            $this->_data[$type][$i]['type_item_identification_id'] = 4;
            $this->_data[$type][$i]['price_amount'] = $item->price_amount;
            $this->_data[$type][$i]['base_quantity'] = $item->quantity;
            $this->_data[$type][$i]['allowance_charges'][0]['discount_id'] = 1;
            $this->_data[$type][$i]['allowance_charges'][0]['charge_indicator'] = false;
            $this->_data[$type][$i]['allowance_charges'][0]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
            $this->_data[$type][$i]['allowance_charges'][0]['amount'] = $item->discount_amount;
            $this->_data[$type][$i]['allowance_charges'][0]['base_amount'] = $item->line_extension_amount;
            $this->_data[$type][$i]['tax_totals'] = $this->_taxes($item->id);
            $this->_data[$type][$i]['start_date'] = $item->start_date;
            $this->_data[$type][$i]['type_generation_transmition_id'] = $item->type_generation_transmition_id;
            $i++;
        }

    }

    public function resolution($resolutions, $typeDocument, $previsualization = false)
    {
        if ($previsualization) {
            $this->_data['number'] = $this->_invoice->id;
            $this->_data['resolution_number'] = 0;
            $this->_data['prefix'] = 'PREV';
            if($typeDocument == 13) {
                $this->_data['prefix'] = 'PRNA';
            }
        } else {
            if ($resolutions != 0 && $this->_invoice->resolution == null) {
                $model = new Resolution();
                $resolutionModel = $model
                    ->where(['id' => $resolutions])
                    ->asObject()
                    ->first();

                $this->_data['resolution_number'] = $resolutionModel->resolution;


                $invoice = new Invoice();
                $model = $invoice->select(['resolution'])
                    ->whereIn('type_documents_id', [$typeDocument])
                    ->where([
                        'resolution_id' => $this->_data['resolution_number'],
                        'companies_id' => Auth::querys()->companies_id,
                        'resolution !=' => null
                    ])
                    ->orderBy('CAST(resolution as UNSIGNED)', 'DESC')
                    ->asObject()
                    ->first();


                $this->_data['prefix'] = $resolutionModel->prefix;
                if (!$model) {

                    $model = new Resolution();
                    $resolutionInit = $model
                        ->whereIn('type_documents_id', [$typeDocument])
                        ->where([
                            'resolution' => $this->_data['resolution_number'],
                            'companies_id' => Auth::querys()->companies_id
                        ])
                        ->asObject()
                        ->first();

                    $this->_data['number'] = (int)$resolutionInit->from;
                } else {
                    $this->_data['number'] = $model->resolution + 1;
                }
            }
            else {
                $this->_data['number']                  = $this->_invoice->resolution;
                $this->_data['prefix']                  = $this->_invoice->prefix;
                $this->_data['resolution_number']       = $this->_invoice->resolution_id;
            }
        }
    }

    private function _taxes(int $lineInvoiceId)
    {
        $model = new LineInvoiceTax();
        $lineInvoiceTaxes = $model->where(['line_invoices_id' => $lineInvoiceId])->asObject()->get()->getResult();

        $tax = [];
        $i = 0;

        foreach ($lineInvoiceTaxes as $item) {

            if ($item->taxes_id == 1) {
                $tax[$i]['tax_id'] = $item->taxes_id;
                $tax[$i]['tax_amount'] = $item->tax_amount;
                $tax[$i]['taxable_amount'] = $item->taxable_amount;
                $tax[$i]['percent'] = $item->percent;
                $this->_groupTaxes($tax[$i], 'tax_totals');
            }

            if ($item->taxes_id == 6) {
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['tax_id'] = $item->taxes_id;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['tax_amount'] = $item->tax_amount;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['taxable_amount'] = $item->taxable_amount;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['percent'] = $item->percent;
                $this->_groupTaxes($this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]);
                $this->_positionWithHoldingTax++;
            }

            if ($item->taxes_id == 7) {
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['tax_id'] = $item->taxes_id;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['tax_amount'] = $item->tax_amount;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['taxable_amount'] = $item->taxable_amount;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['percent'] = $item->percent;
                $this->_groupTaxes($this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]);
                $this->_positionWithHoldingTax++;
            }
            $i++;
        }
        return $tax;
    }

    private function _groupTaxes($data, string $type = 'with_holding_tax_total')
    {
        if ($type == 'with_holding_tax_total') {

            $numbers = array_map(function ($value) use ($data) {
                return $value['percent'] == $data['percent'] && $value['tax_id'] == $data['tax_id'];
            }, $this->_withHoldingTaxTotalsGroup);

            if (count($numbers) == 0) {
                $values = [
                    'tax_id'            => $data['tax_id'],
                    'percent'           => (double)$data['percent'],
                    'tax_amount'        => (double)$data['tax_amount'],
                    'taxable_amount'    => (double)$data['taxable_amount']
                ];
                array_push($this->_withHoldingTaxTotalsGroup, $values);
            }elseif(count($numbers) > 0 && in_array(true, $numbers) == false) {
                $values = [
                    'tax_id'            => $data['tax_id'],
                    'percent'           => (double)$data['percent'],
                    'tax_amount'        => (double)$data['tax_amount'],
                    'taxable_amount'    => (double)$data['taxable_amount']
                ];
                array_push($this->_withHoldingTaxTotalsGroup, $values);
            }
            else if($position = in_array(true, $numbers) != false){
                $this->_withHoldingTaxTotalsGroup[array_search(true, $numbers)]['tax_amount'] += (double)$data['tax_amount'];
                $this->_withHoldingTaxTotalsGroup[array_search(true, $numbers)]['taxable_amount'] += (double)$data['taxable_amount'];
            }
        }else {
            $numbers = array_map(function ($value) use ($data) {
                return $value['percent'] == $data['percent'] && $value['tax_id'] == $data['tax_id'];
            }, $this->_taxTotalsGroup);

            if (count($numbers) == 0) {
                $values = [
                    'tax_id'            => $data['tax_id'],
                    'percent'           => (double)$data['percent'],
                    'tax_amount'        => (double)$data['tax_amount'],
                    'taxable_amount'    => (double)$data['taxable_amount']
                ];
                array_push($this->_taxTotalsGroup, $values);
            }elseif(count($numbers) > 0 && in_array(true, $numbers) == false) {
                $values = [
                    'tax_id'            => $data['tax_id'],
                    'percent'           => (double)$data['percent'],
                    'tax_amount'        => (double)$data['tax_amount'],
                    'taxable_amount'    => (double)$data['taxable_amount']
                ];
                array_push($this->_taxTotalsGroup, $values);
            }
            else if($position = in_array(true, $numbers) != false){
                $this->_taxTotalsGroup[array_search(true, $numbers)]['tax_amount'] += (double)$data['tax_amount'];
                $this->_taxTotalsGroup[array_search(true, $numbers)]['taxable_amount'] += (double)$data['taxable_amount'];
            }

        }

    }

    public function taxTotal()
    {
        $this->_data['tax_totals'] = $this->_taxTotalsGroup;
    }

    public function withHoldingTaxTotal()
    {
        $this->_data['with_holding_tax_total'] =  $this->_withHoldingTaxTotalsGroup;
    }

    public function billing_reference()
    {
        $this->_data['billing_reference']['number']         = $this->_invoice->resolution_credit;
        $this->_data['billing_reference']['uuid']           = $this->_invoice->uuid;
        $this->_data['billing_reference']['issue_date']     = $this->_invoice->issue_date;
        $this->_data['discrepancyresponsecode']             = $this->_invoice->discrepancy_response_id;
        $this->_data['discrepancyresponsedescription']      = "Anulación de documento soporte";
    }

}