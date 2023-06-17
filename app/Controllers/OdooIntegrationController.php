<?php

/**
 * @author wilson andres bachiller ortiz
 * @date    12/11/2020
 */



namespace App\Controllers;

require_once '../app/Libraries/ripcord/ripcord.php';

use App\Controllers\Api\Auth;
use App\Models\Invoice;
use App\Models\Resolution;
use App\Models\Integration;
use ripcord;


class OdooIntegrationController extends BaseController
{
    private $url;
    private $db;
    private $username;
    private $password;
    private $models;
    private $uid;
    private $invoice = [];
    private $integration;
    public  $api = 'http://api.test/';

    public function __construct()
    {
        $ingration = new Integration();
        $this->integration = $ingration->join('companies', 'companies.id =Integrations.companies_id')
            ->asObject()->find(1);

        $this->url          = $this->integration->url;
        $this->db           = $this->integration->database;
        $this->username     = $this->integration->username;
        $this->password     = $this->integration->password;

        // Validation version odooo
        $common = ripcord::client("$this->url/xmlrpc/2/common");
        $version = $common->version();

        // Authentication
        $this->uid = $common->authenticate($this->db, $this->username, $this->password, $version);
        $this->models = ripcord::client("$this->url/xmlrpc/2/object");
    }

    public function init($resolucionDian)
    {
        $data = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'sale.order', 'search_read', [], ['fields' => ['partner_id', 'name'], 'limit' => 1]);

        $ordenes = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'mail.message', 'search_read', [[['record_name', '=', $data[0]['name'] ]]],
            [
                'fields'=>[
                    'parent_id',
                    'res_id',
                    'author_id',
                    'create_uid'
                ],'limit' => 1
            ]
        );

        
        $dates = [];

        foreach ($data as $cliente) {
            $this->invoice = [];
            $this->invoice['number']              = $resolucionDian;
            $this->invoice['type_document_id']    = 1;
            $this->invoice['resolution_number']   = '18760000001';
            $this->invoice['date']                = date('Y-m-d');
            $this->invoice['time']                = date('H:i:s');
            $this->customer($cliente["partner_id"][0]);
            $this->paymentForms();
            $this->lineInvoice($cliente['id']);
            $this->_legalMonetaryTotals();
            $this->_taxesTotals();
	

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->api.'api/ubl2.1/invoice/'.$this->integration->testId,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($this->invoice),
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Accept: application/json",
                    "Authorization: Bearer " . $this->integration->token,
                    "Content-Type: application/json"
                ),
            ));

            $response = curl_exec($curl);



            curl_close($curl);
            /***
             * @author john vergara
             * @email jvergara@iplanetcolombia.com
             * @message  create notification on odoo.co
             */

            $id = $this->models->execute_kw($this->db, $this->uid, $this->password,
                'mail.message', 'create',[
                    [
                        'subject'       => 'Re: '.$cliente['name'],
                        'date'          => date('Y-m-d H:i:s'),
                        'body'          => '<p>Factura generada con Resolucion N°'.$this->invoice['number'].' <a target="_blank" href="https://planeta-internet.com/api/api/download/901005608/FES-SETP'.$this->invoice['number'].'.pdf">Ver Factura</a></p>',
                        'parent_id'     => $ordenes[0]['parent_id'][0],
                        'model'         => 'sale.order',
                        'res_id'        => $ordenes[0]['res_id'],
                        'record_name'   => $cliente['name'],
                        'message_type'  => 'comment',
                        'subtype_id'    => 1,
                        'author_id'     => $ordenes[0]['author_id'][0]  ,
                        'add_sign'      => 't',
                        'create_uid'    => 2,
                        'create_date'   => date('Y-m-d H:i:s'),
                        'write_uid'     => 2,
                        'write_date'    =>  date('Y-m-d H:i:s')

                    ]
                ]);


            echo $response;
            die();


        }
    }



    public function customer($partnerId)
    {
        $data = $this->models->execute_kw(
            $this->db,
            $this->uid,
            $this->password,
            'res.partner',
            'read',
            [$partnerId],
            ['fields' => ['id', 'vat', 'name', 'phone', 'mobile', 'street', 'email', 'display_name']]
        );



        $this->invoice['customer']['name']                                  = $data[0]['name'] ? $data[0]['name'] : $data[0]['display_name'];
        $this->invoice['customer']['identification_number']                 = $data[0]['vat']       ? $data[0]['vat'] :     '222222222222';
        $this->invoice['customer']['phone']                                 = $data[0]['phone']     ? str_replace(' ', '',str_replace('+57 ', '',$data[0]['phone'])): $this->integration->phone;
        $this->invoice['customer']['address']                               = $data[0]['street']    ? $data[0]['street']:   $this->integration->address;
        $this->invoice['customer']['email']                                 = $data[0]['email']     ? $data[0]['email']:    $this->integration->email;
        $this->invoice['customer']['merchant_registration']                 = '000000';
        $this->invoice['customer']['type_document_identification_id']       = 3;
        $this->invoice['customer']['type_organization_id']                  = 1;
        $this->invoice['customer']['municipality_id']                       = 149;
        $this->invoice['customer']['type_regime_id']                        = 2;


    }

    public function paymentForms()
    {
        $this->invoice['payment_form']['payment_form_id']       = 1;
        $this->invoice['payment_form']['payment_method_id']     = 10;
        $this->invoice['payment_form']['payment_due_date']      = date('Y-m-d');
        $this->invoice['payment_form']['duration_measure']      = 0;
    }

    public function lineInvoice($orderId)
    {
        $data = $this->models->execute_kw($this->db, $this->uid, $this->password,
            'sale.order.line', 'search_read',
            [[['order_id', '=' ,$orderId]]],
            ['fields' => ['id', 'price_subtotal','product_uom_qty', 'discount', 'name', 'tax_id']]);

        $l = 0;
        foreach ($data as $line) {
            if($data[$l]['price_subtotal'] != 0) {
                $this->invoice['invoice_lines'][$l]['unit_measure_id']              = 70;
                $this->invoice['invoice_lines'][$l]['invoiced_quantity']            = $data[$l]['product_uom_qty'];
                $this->invoice['invoice_lines'][$l]['line_extension_amount']        = ($data[$l]['price_subtotal']) - $data[$l]['discount'];
                $this->invoice['invoice_lines'][$l]['free_of_charge_indicator']     = false;

                $this->invoice['invoice_lines'][$l]['description']                  = $data[$l]['name'];
                $this->invoice['invoice_lines'][$l]['code']                         = $data[$l]['name'];
                $this->invoice['invoice_lines'][$l]['type_item_identification_id']  = 4;
                $this->invoice['invoice_lines'][$l]['price_amount']                 = ($data[$l]['price_subtotal']/ ($data[$l]['product_uom_qty'] == 0 ? 1 : $data[$l]['product_uom_qty']));
                $this->invoice['invoice_lines'][$l]['base_quantity']                = $data[$l]['price_subtotal'];

                $this->invoice['invoice_lines'][$l]['allowance_charges'][0]['charge_indicator']         = false;
                $this->invoice['invoice_lines'][$l]['allowance_charges'][0]['allowance_charge_reason']  = 'DESCUENTO GENERAL';
                $this->invoice['invoice_lines'][$l]['allowance_charges'][0]['amount']                   =  $data[$l]['discount'];
                $this->invoice['invoice_lines'][$l]['allowance_charges'][0]['base_amount']              = $data[$l]['price_subtotal'];




            $accountTax = $this->models->execute_kw($this->db, $this->uid, $this->password,
                'account.tax', 'search_read',
                [[['id', '=' ,$data[$l]['tax_id']]]],
                ['fields' => ['name','amount']]);

            $i = 0;
            foreach ($accountTax as $item) {

                if ($this->_validationTax($item['name']) == 1 ) {
                    $this->invoice['invoice_lines'][$l]['tax_totals'][$i]['tax_id'] = $this->_validationTax($item['name']);
                    $this->invoice['invoice_lines'][$l]['tax_totals'][$i]['tax_amount'] = (double) number_format(((($data[$l]['price_subtotal']) - $data[$l]['discount']) * abs($item['amount'])) / 100, '2', '.', '');
                    $this->invoice['invoice_lines'][$l]['tax_totals'][$i]['taxable_amount'] = ( $data[$l]['price_subtotal']) - $data[$l]['discount'];
                    $this->invoice['invoice_lines'][$l]['tax_totals'][$i]['percent'] = (string)abs($item['amount']);
                } else {
                    $this->invoice['invoice_lines'][$l]['with_holding_tax_total'][$i]['tax_id']         = $this->_validationTax($item['name']);
                    $this->invoice['invoice_lines'][$l]['with_holding_tax_total'][$i]['tax_amount']     = ((($data[$l]['price_subtotal'])- $data[$l]['discount']) * abs($item['amount'])) / 100;
                    $this->invoice['invoice_lines'][$l]['with_holding_tax_total'][$i]['taxable_amount'] = ($data[$l]['price_subtotal']) - $data[$l]['discount'];
                    $this->invoice['invoice_lines'][$l]['with_holding_tax_total'][$i]['percent']        = (string)abs($item['amount']);

                }
                $i++;

            }
            $l++;
            }
        }
	
	if(!isset($this->invoice['invoice_lines'][$l]['with_holding_tax_total'])) {
		$this->invoice['with_holding_tax_total'] = [];
	}





    }

    private function _legalMonetaryTotals() {
        $lineExtensionAmount = 0;
        $taxExclusiveAmount = 0;
        $taxInclusiveAmount = 0;
        foreach ($this->invoice['invoice_lines'] as $item) {
            $lineExtensionAmount +=     $item['line_extension_amount'];
            $taxExclusiveAmount  +=     $item['line_extension_amount'];
        }

        foreach ($this->invoice['invoice_lines'] as $item) {
            if(isset($item['tax_totals'])) {
                foreach ($item['tax_totals'] as $tax) {
                    if($tax['tax_id'] == 1) {
                        $taxInclusiveAmount += $tax['tax_amount'];
                    }
                }
            }

        }

        $this->invoice['legal_monetary_totals']['line_extension_amount']    = (double) number_format($lineExtensionAmount, '2', '.', '');
        $this->invoice['legal_monetary_totals']['tax_exclusive_amount']     = (double) number_format($taxExclusiveAmount, '2', '.', '');
        $this->invoice['legal_monetary_totals']['tax_inclusive_amount']     = (double) number_format($taxInclusiveAmount + $lineExtensionAmount, '2', '.', '');
        $this->invoice['legal_monetary_totals']['allowance_total_amount']   = '0.00';
        $this->invoice['legal_monetary_totals']['charge_total_amount']      = '0.00';
        $this->invoice['legal_monetary_totals']['payable_amount']           = (double) number_format($taxInclusiveAmount + $lineExtensionAmount, '2', '.', '');
    }

    private function _taxesTotals() {

        $iva = [];
        $percent = [];
        foreach ($this->invoice['invoice_lines'] as $value) {
            if(isset($value['tax_totals'])) {;
                foreach ($value['tax_totals'] as $item) {
                    if (!array_key_exists($item['percent'], $percent)) {
                        array_push($iva, $item);
                        $percent["" . $item['percent'] . ""] = $item['percent'];
                    } else {
                        $m = 0;
                        foreach ($iva as $valid) {
                            if ($valid['percent'] == $item['percent']) {
                                $iva[$m]['tax_amount'] += $item['tax_amount'];
                                $iva[$m]['taxable_amount'] += $item['taxable_amount'];
                            }
                            $m++;
                        }

                    }

                }
            }
        }



        $retention = [];
        $l = 0;


        foreach ($this->invoice['invoice_lines'] as $item) {
            if(isset($item['with_holding_tax_total'])) {
                foreach ($item['with_holding_tax_total'] as $taxTotal) {
                    if ($taxTotal['tax_id'] == '5' || $taxTotal['tax_id'] == '6' || $taxTotal['tax_id'] == '7') {
                        if (array_key_exists($taxTotal['tax_id'], $retention)) {
                            if (isset($retention[$taxTotal['tax_id']][$l]['percent']) && $retention[$taxTotal['tax_id']][$l]['percent'] == $taxTotal['percent']) {
                                $k = 0;
                                foreach ($retention[$taxTotal['tax_id']] as $values) {
                                    if ($retention[$taxTotal['tax_id']][$k]['percent'] = $taxTotal['percent']) {
                                        $retention[$taxTotal['tax_id']][$k]['taxable_amount'] += $taxTotal['taxable_amount'];
                                        $retention[$taxTotal['tax_id']][$k]['tax_amount'] += $taxTotal['tax_amount'];
                                    }
                                    $k++;
                                }
                            }
                            $l++;
                            $retention[$taxTotal['tax_id']][$l] = $taxTotal;
                        } else {
                            $retention[$taxTotal['tax_id']][$l] = $taxTotal;
                        }
                    }
                }
            }
        }

        $array = [];
        $info = [];


        if (isset($retention[5])) {
            array_push($info, (array)$retention[5]);
        }

        if( isset($retention[6])) {
            array_push($info, (array)$retention[6]);
        }

        if( isset($retention[7])) {
            array_push($info, (array)$retention[7]);
        }

        foreach ($info as $item) {
            foreach ($item as $item2) {
                if ($item2['percent'] != 0.00) {
                    array_push($array, $item2);
                }
            }
        }
        if (count($array) > 0) {
            $this->invoice['with_holding_tax_total'] = $array;
        }
        if(!empty($iva)){
            $this->invoice['tax_totals'] = $iva;
        }



    }


    private function _validationTax($name)
    {
        if(strpos($name, 'IVA') !== false) {
            return 1;
        }else if(strpos($name, 'RteFte') !== false) {
            return 6;
        }else if(strpos($name, 'RteICA') !== false) {
            return 7;
        }
    }

    private function _resolution($id = null)
    {
        $resolution = new Resolution();
        $resolution = $resolution->where([ 'companies_id' =>  Auth::querys()->companies_id]);

        if($id) {
            $resolution->where(['resolution' => $id]);
            $consulta =['type_documents_id' => 1];
            $resolution->where($consulta);
        }

        $resolution = $resolution
            ->orderBy('id', 'DESC')
            ->asObject()
            ->first();



        $invoices = new Invoice();
        $invoices->select('invoices.resolution');
        if($id) {
            $invoices->where([ 'companies_id'=> Auth::querys()->companies_id, 'resolution_id =' => $id]);
        }
        $invoices = $invoices->orderBy('id', 'DESC')
            ->asObject()
            ->first();



        if(!$invoices){
            return  $resolution->from;

        }else {
            return $invoices->resolution + 1;
        }

    }


}