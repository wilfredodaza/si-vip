<?php

namespace App\Traits;

use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;

Class AssemblingInvoiceController 
{
    public  $_invoice;
    private $_lineInvoice;
    public  $_withHoldingTaxTotals              = [];
    public  $_withHoldingTaxTotalsGroup         = [];
    public  $_taxTotals                         = [];
    public  $_taxTotalsGroup                    = [];
    public  $_data                              = [];
    public  $_positionTaxes                     = 0;
    public  $_positionWithHoldingTax            = 0;
    public  $_token                             = '';


    /**
     * Query of invoice, Line invoices and taxes
     * 
     * @param int $id Id Invoice
     * 
     */
    public function request(int $id)
    {
        $model = new Invoice();
        $this->_invoice = $model
        ->select([
            'invoices.resolution', 
            'invoices.resolution_id',
            'invoices.type_documents_id',
            'customers.identification_number as customer_identification_number',
            'customers.dv as customer_dv',
            'customers.name as customer_name',
            'customers.phone as customer_phone',
            'customers.address as customer_address',
            'customers.email as customer_email',
            'customers.merchant_registration as customer_merchant_registration',
            'customers.type_document_identifications_id as customer_type_document_identification_id',
            'customers.type_organization_id as customer_type_organization_id',
            'customers.municipality_id as customer_municipality_id',
            'customers.type_regime_id as customer_type_regime_id',
            'resolutions.prefix',
            'invoices.payment_forms_id',
            'payment_methods_id',
            'duration_measure',
            'payment_due_date',
            'invoices.line_extesion_amount',
            'invoices.tax_exclusive_amount',
            'invoices.tax_inclusive_amount',
            'invoices.allowance_total_amount',
            'invoices.charge_total_amount',
            'invoices.payable_amount',
            'companies.token'
            ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->join('resolutions', 'resolutions.resolution = invoices.resolution_id')
            ->asObject()
            ->find($id);


        $model = new LineInvoice();
        $this->_lineInvoice = $model->select([
            'line_invoices.id',
            'line_invoices.quantity',
            'line_invoices.line_extension_amount',
            'line_invoices.description',
            'products.code as product_code',
            'line_invoices.price_amount',
            'line_invoices.provider_id',
            'line_invoices.discount_amount'
            ])
        ->join('products', 'products.id = line_invoices.products_id')
        ->join('customers', 'customers.id = line_invoices.provider_id', 'left')
        ->where(['invoices_id' => $id])
        ->get()
        ->getResult();

   



    }

    public function configuration()
    {

    }

    

    public function invoice()
    {

        $this->_data['prefix']                  = $this->_invoice->prefix;
        $this->_data['number']                  = $this->_invoice->resolution;
        $this->_data['resolution_number']       = $this->_invoice->resolution_id;
        $this->_data['type_document_id']        = $this->_invoice->type_documents_id;
        $this->_data['date']                    = date('Y-m-d');
        $this->_data['time']                    = date('H:i:s');
        $this->_data['sendmail']                = false;
        
        //$this->_data['establishment_name']      = 0;
        $this->_token                           = $this->_invoice->token;
        
    }

    private function _orderReference()
    {
        $this->_data['order_reference']['id_order'];
        $this->_data['order_reference']['issue_date_order'];
    }


    private function _delivery()
    {
        $this->_data['delivery']['languaje_id'];
        $this->_data['delivery']['country_id'];
        $this->_data['delivery']['municipality_id'];
        $this->_data['delivery']['address'];
        $this->_data['delivery']['actual_delivery_dat'];
    }

    private function _deliveryParty()
    {
        $this->_data['deliveryparty']['identification_number'];
        $this->_data['deliveryparty']['dv'];
        $this->_data['deliveryparty']['name'];
        $this->_data['deliveryparty']['phone'];
        $this->_data['deliveryparty']['address'];
        $this->_data['deliveryparty']['email'];
        $this->_data['deliveryparty']['merchant_registration'];
        $this->_data['deliveryparty']['type_document_identification_id'];
        $this->_data['deliveryparty']['type_organization_id'];
        $this->_data['deliveryparty']['municipality_id'];
        $this->_data['deliveryparty']['type_regime_id'];
    }


    public  function customer()
    {
        // colocar cunsimidor final
        $this->_data['customer']['identification_number']                   = $this->_invoice->customer_identification_number;
        $this->_data['customer']['dv']                                      = $this->_invoice->customer_dv;
        $this->_data['customer']['name']                                    = $this->_invoice->customer_name;
        $this->_data['customer']['phone']                                   = $this->_invoice->customer_phone;
        $this->_data['customer']['address']                                 = $this->_invoice->customer_address;
        $this->_data['customer']['email']                                   = $this->_invoice->customer_email;
        $this->_data['customer']['merchant_registration']                   = $this->_invoice->customer_merchant_registration;
        $this->_data['customer']['type_document_identification_id']         = $this->_invoice->customer_type_document_identification_id;
        $this->_data['customer']['type_organization_id']                    = $this->_invoice->customer_type_organization_id;
        $this->_data['customer']['municipality_id']                         = $this->_invoice->customer_municipality_id;
        $this->_data['customer']['type_regime_id']                          = $this->_invoice->customer_type_regime_id;
    }


    public function paymentForm()
    {
        $this->_data['payment_form']['payment_form_id']                     = $this->_invoice->payment_forms_id;
        $this->_data['payment_form']['payment_method_id']                   = $this->_invoice->payment_methods_id;
        $this->_data['payment_form']['payment_due_date']                    = $this->_invoice->payment_due_date;
        $this->_data['payment_form']['duration_measure']                    = $this->_invoice->duration_measure;
    }


    public function monetaryTotals(string $type)
    {
        $this->_data[$type]['line_extension_amount']      = $this->_invoice->line_extesion_amount;
        $this->_data[$type]['tax_exclusive_amount']       = $this->_invoice->tax_exclusive_amount;
        $this->_data[$type]['tax_inclusive_amount']       = $this->_invoice->tax_inclusive_amount;
        $this->_data[$type]['allowance_total_amount']     = $this->_invoice->allowance_total_amount;
        $this->_data[$type]['charge_total_amount']        = $this->_invoice->charge_total_amount;
        $this->_data[$type]['payable_amount']             = $this->_invoice->payable_amount;
    }

    private function _prepaidPayment()
    {
        $this->_data['prepaid_payment']['idpayment']                = 0;
        $this->_data['prepaid_payment']['paidamount']               = 0;
        $this->_data['prepaid_payment']['receiveddate']             = 0;
        $this->_data['prepaid_payment']['paiddate']                 = 0;
        $this->_data['prepaid_payment']['instructionid']            = 0;
    }

    private function allowanceCharges(int $discountId, bool $chargeIndicator, string $allowanceChargeReason, $amount, $baseAmount)
    {
        $this->_data['allowance_charges'][0]['discount_id']                 = $discountId;
        $this->_data['allowance_charges'][0]['charge_indicator']            = $chargeIndicator;
        $this->_data['allowance_charges'][0]['allowance_charge_reason']     = $allowanceChargeReason;
        $this->_data['allowance_charges'][0]['amount']                      = $amount;
        $this->_data['allowance_charges'][0]['base_amount']                 = $baseAmount;
    }



    public function lineInvoice(string $type)
    {
        $i = 0;
        foreach($this->_lineInvoice as $item) {
            $this->_data[$type][$i]['unit_measure_id']             = 70;
            $this->_data[$type][$i]['invoiced_quantity']           = $item->quantity;
            $this->_data[$type][$i]['line_extension_amount']       = $item->line_extension_amount;
            $this->_data[$type][$i]['free_of_charge_indicator']    = false;
            $this->_data[$type][$i]['description']                 = $item->description;
            $this->_data[$type][$i]['code']                        = $item->product_code;
            $this->_data[$type][$i]['type_item_identification_id'] = 4;
            $this->_data[$type][$i]['price_amount']                = $item->price_amount;
            $this->_data[$type][$i]['base_quantity']               = $item->quantity;
            if($item->provider_id) {
                $this->_data[$type][$i]['agentparty'];
                $this->_data[$type][$i]['agentparty_dv'];
            }
            $this->_data[$type][$i]['allowance_charges'][0]['discount_id']                 = 1;
            $this->_data[$type][$i]['allowance_charges'][0]['charge_indicator']            = false;
            $this->_data[$type][$i]['allowance_charges'][0]['allowance_charge_reason']     = 'DESCUENTO GENERAL';
            $this->_data[$type][$i]['allowance_charges'][0]['amount']                      = $item->discount_amount;
            $this->_data[$type][$i]['allowance_charges'][0]['base_amount']                 = $item->line_extension_amount;
            $this->_data[$type][$i]['tax_totals']                        = $this->_taxes($item->id);
            $i++;
        }
       
    }

    private function _taxes(int  $lineInvoiceId )
    {


        $model = new LineInvoiceTax();
        $lineInvoiceTaxes = $model->where(['line_invoices_id' => $lineInvoiceId])->asObject()->get()->getResult();


        $tax    = [];
        $i      =  0;

     
        foreach($lineInvoiceTaxes as $item) {
            if($item->taxes_id <= 4) {
                $tax[$i]['tax_id']              =  $item->taxes_id;
                $tax[$i]['tax_amount']          =  $item->tax_amount;
                $tax[$i]['taxable_amount']      =  $item->taxable_amount;
                $tax[$i]['percent']              =  $item->percent;
                $this->_groupTaxes($tax[$i], 'tax_totals');
            }

            if($item->taxes_id  == 10) {
                $tax[$i]['tax_id']              =  $item->taxes_id;
                $tax[$i]['tax_amount']          =  $item->tax_amount;
                $tax[$i]['taxable_amount']      =  $item->taxable_amount;
                $tax[$i]['percent']              =  $item->percent;     
                $tax[$i]['unit_measure_id']     =  70;
                $tax[$i]['per_unit_amount']     =  $item->per_unit_amount;
                $tax[$i]['base_unit_measure']   =  $item->ase_unit_measure;
                $this->_groupTaxes($tax[$i], 'tax_totals');

            }
            if($item->taxes_id <= 4) {
                $this->_taxTotals[$this->_positionTaxes]['tax_id']              =  $item->taxes_id;
                $this->_taxTotals[$this->_positionTaxes]['tax_amount']          =  $item->tax_amount;
                $this->_taxTotals[$this->_positionTaxes]['taxable_amount']      =  $item->taxable_amount;
                $this->_taxTotals[$this->_positionTaxes]['percent']             =  $item->percent;
                $this->_positionTaxes++;
            }

            if($item->taxes_id >= 5 && $item->taxes_id <= 7) {
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['tax_id']              =  $item->taxes_id;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['tax_amount']          =  $item->tax_amount;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['taxable_amount']      =  $item->taxable_amount;
                $this->_withHoldingTaxTotals[$this->_positionWithHoldingTax]['percent']              =  $item->percent;
                $this->_groupTaxes($this->_withHoldingTaxTotals[$this->_positionWithHoldingTax], 'with_holding_tax_total');
                $this->_positionWithHoldingTax++;
            }
            $i++;
        }
            
        return $tax;
    }

    private function  _groupTaxes($data, string $type = 'with_holding_tax_total')
    {
  
        $i = 0;
        $validate = true;
        foreach(($type == 'tax_totals' ? $this->_taxTotalsGroup : $this->_withHoldingTaxTotalsGroup) as $tax) {
      
            if($tax->tax_id == $data['tax_id'] && $tax->percent == $data['percent']){   
                $validate = false;
                if($type == 'with_holding_tax_total') {
                    $this->_withHoldingTaxTotalsGroup[$i]->tax_amount         += (double) $data['tax_amount'];
                    $this->_withHoldingTaxTotalsGroup[$i]->taxable_amount     += (double) $data['taxable_amount'];
                    $i++;
                    break;
                }else {
                    $this->_taxTotalsGroup[$i]->tax_amount                    += (double) $data['tax_amount'];
                    $this->_taxTotalsGroup[$i]->taxable_amount                += (double) $data['taxable_amount'];
                    $i++;
                    break;
                }
               
            }
        }

        if($validate) {
            if($data['tax_id'] != 5) {
                $values = (Object) [
                    'tax_id'            => $data['tax_id'],
                    'percent'           => (double) $data['percent'],
                    'tax_amount'        => (double) $data['tax_amount'],
                    'taxable_amount'    => (double) $data['taxable_amount']
                ];
                if($type == 'tax_totals') {
                    array_push($this->_taxTotalsGroup, $values);
                }else {
                    array_push($this->_withHoldingTaxTotalsGroup, $values);
                }
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
   
   

    
}