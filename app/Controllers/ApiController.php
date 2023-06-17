<?php


namespace App\Controllers;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Config;
use App\Models\Currency;
use App\Models\Resolution;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;

use App\Controllers\Api\Auth;

use App\Traits\InvoiceTrait;
use App\Traits\RequestAPITrait;
use App\Traits\ValidateResponseAPITrait;
use Config\Services;


class ApiController extends BaseController
{

    use InvoiceTrait,  RequestAPITrait, ValidateResponseAPITrait;


    public function envioMasivo()
    {
        $invoice = new Invoice();
        $data = $invoice->select(['id', 'companies_id'])
            ->where([
                'invoices.invoice_status_id' => 3,
                'invoices.created_at <= ' => '2021-10-30 00:00:00',
                'invoices.companies_id <>' => 77])
            ->get()
            ->getResult();
        return json_encode($data);
        die();
    }

    public function email($company, $id)
    {
        $invoice = new Invoice();
        $data = $invoice->select('*')
            ->join('companies', 'invoices.companies_id = companies.id')
            ->where(['invoices.companies_id' => $company, 'invoices.id' => $id])
            ->whereIn('invoices.type_documents_id', ['1', '2', '4', '5'])
            ->get()
            ->getResult()[0];

        if ($data->type_documents_id == 1 || $data->type_documents_id == 2) {
            $resolution = new Resolution();
            $resolutions = $resolution->where(['resolution' => $data->resolution_id])
                ->get()
                ->getResult()[0];
        } else if ($data->type_documents_id == 4) {
            $resolution = new Resolution();
            $resolutions = $resolution->where(['type_documents_id' => 4])
                ->get()
                ->getResult()[0];
        } else if ($data->type_documents_id == 5) {
            $resolution = new Resolution();
            $resolutions = $resolution->where(['type_documents_id' => 5])
                ->get()
                ->getResult()[0];
        }

        // return var_dump($data->identification_number);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('API') . "/send-email-customer/Now",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode([
                "company_idnumber" => $data->identification_number,
                "prefix" => empty($resolutions->prefix) ? ' ' : trim($resolutions->prefix),
                "number" => $data->resolution
            ]),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "accept: application/json"
            ),
        ));
        $response = curl_exec($curl);


        curl_close($curl);
        $data = json_decode($response);

        // echo json_encode($data);die();


        if (isset($data->success) && $data->success == 'true') {
            $invoice = new Invoice();
            $invoice->set(['invoice_status_id' => 3])
                ->where(['id' => $id])
                ->update();
        } else {
            return redirect()->to(base_url('/invoice'))->with('errors', 'El correos electrónicos no pudo ser enviado.');
        }
        return redirect()->to(base_url('/invoice'))->with('success', $data->message);
    }

    public function pdf($id)
    {
        $model = new Invoice();
        $invoice = $model
            ->select([
                'companies.identification_number',
                'invoices.resolution',
     		 'invoices.resolution_id',

                'invoices.type_documents_id',
                'invoices.customers_id',
                'resolutions.prefix'
            ])
            ->join('companies', 'invoices.companies_id = companies.id', 'left')
            ->join('resolutions', 'resolutions.resolution = invoices.resolution_id', 'left')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();


        switch ($invoice->type_documents_id){
            case 1:
            case 2:
                $prefix = 'FES-'.$invoice->prefix;
                break;
            case 4:
	
		$resolution = new Resolution();
		$resolutions = $resolution
		->where(['type_documents_id' => 4, 'companies_id' => Auth::querys()->companies_id, 'resolution' => $invoice->resolution_id])
		->orderBy('id', 'desc')
		->asObject()
		->first();
 		 $prefix = 'NCS-'.$resolutions->prefix;	
                break;
            case 5:
                $prefix = 'NDS-ND';
                break;
 	    case 100:
                $prefix = 'COT-';
                break;

        }
   	$name = $prefix.''.$invoice->resolution;    
        header('Content-disposition: attachment; filename='.$name.'.pdf');
        header('Content-type: application/pdf');
        readfile(getenv('API') . "/invoice/".$invoice->identification_number."/".$name.'.pdf');
    }

    public function preview2($company, $id)
    {
        $companies = new Company();
        $info = $companies->find($company);
        $data = [];
        if($info['type_company_id'] ==  2) {
            $model = new Partnership();
            $partnerships = $model->where(['company_id' => $company])->asObject()->get()->getResult();
            $data['partnerships'] = $partnerships;
        }
        $config = new Config();
        $config = $config->where(['companies_id' => $company])->asObject()->first();
        $data['dv'] = $info['dv'];
        if ($config) {
            $data['nombretipodocid'] = $config->name_type_doc_id;
            $data['responsable_iva'] = $config->responsable_iva;
            $data['decimal'] =  isset($config->quantity_decimal) ? $config->quantity_decimal : 2 ;
            if(isset( $config->economic_activity) || !empty( $config->economic_activity) ) {
                $data['actividadeconomica'] = $config->economic_activity;
            }
        }
        $data['template']   = $info['template_pdf_id'];
        $invoice = new Invoice();
        $invoice = $invoice->select(['*', 'customers.id as customers_id', 'invoices.created_at'])
            ->join('customers', 'invoices.customers_id = customers.id')
            ->asObject()
            ->find($id);
        $dateCreation       = explode(' ', $invoice->created_at);
        $data['date']       = $dateCreation[0];
        $data['time']       = $dateCreation[1];
        $data['id'] = $invoice->id;
        $type = '';
        if ($invoice->type_documents_id == 1 ||  $invoice->type_documents_id == 2) {
            $data['type_document_id'] = 1;
            $type = 'invoice_lines';
        } else if ($invoice->type_documents_id == 4) {
            $data['type_document_id']                   = 4;
            $data['billing_reference']['number']        = $invoice->resolution_credit;
            $data['billing_reference']['uuid']          = $invoice->uuid;
            $data['billing_reference']['issue_date']    = $invoice->issue_date;
            $type = 'credit_note_lines';
        } else if ($invoice->type_documents_id == 5) {
            $data['type_document_id'] = 5;
            $data['billing_reference']['number']        = $invoice->resolution_credit;
            $data['billing_reference']['uuid']          = $invoice->uuid;
            $data['billing_reference']['issue_date']    = $invoice->issue_date;
            $type = 'debit_note_lines';
        } else if( $invoice->type_documents_id == 100) {
            $data['type_document_id'] = 100;
            $type = 'invoice_lines';
        }
        $data['notes'] =  $invoice->notes;
        $data['customer']['id']                                 = $invoice->customers_id;
        $data['customer']['name']                               = $invoice->name;
        $data['customer']['identification_number']              = $invoice->identification_number;
        $data['customer']['phone']                              = $invoice->phone;
        $data['customer']['address']                            = $invoice->address;
        $data['customer']['email']                              = $invoice->email;
        $data['customer']['email2']                             = $invoice->email2;
        $data['customer']['email3']                             = $invoice->email3;
        $data['customer']['merchant_registration']              = $invoice->merchant_registration;
        $data['customer']['municipality_id']                    = $invoice->municipality_id;
        $data['customer']['type_document_identification_id']    = $invoice->type_document_identifications_id;
        if ($invoice->type_document_identifications_id == 6) {
            $data['customer']['dv'] = $invoice->dv;
        }
        $data["number"] = $invoice->resolution;
        if (!empty($invoice->resolution_id) && $data['type_document_id'] != 100) {
          $data["resolution_number"] = $invoice->resolution_id;
          $resolution = new Resolution();
          $resolutions = $resolution->where(['type_documents_id' =>   $data['type_document_id'], 'resolution' => $invoice->resolution_id])->asObject()->first();
          $data['prefix'] = $resolutions->prefix;
        }
        
        $data['legal_monetary_totals']['line_extension_amount']     = $invoice->line_extesion_amount;
        $data['legal_monetary_totals']['tax_exclusive_amount']      = $invoice->tax_exclusive_amount;
        $data['legal_monetary_totals']['tax_inclusive_amount']      = $invoice->tax_inclusive_amount;
        $data['legal_monetary_totals']['allowance_total_amount']    = $invoice->allowance_total_amount;
        $data['legal_monetary_totals']['charge_total_amount']       = $invoice->charge_total_amount;
        $data['legal_monetary_totals']['payable_amount']            = $invoice->payable_amount;
        if ($invoice->type_documents_id == 1 || $invoice->type_documents_id == 2) {
            $data['payment_form']['payment_form_id']                = $invoice->payment_forms_id;
            $data['payment_form']['payment_method_id']              = $invoice->payment_methods_id;
            $data['payment_form']['payment_due_date']               = $invoice->duration_measure > 0 ? $invoice->payment_due_date: date('Y-m-d');
            $data['payment_form']['duration_measure']               = $invoice->duration_measure;
            $data['calculationrate']                                = $invoice->calculationrate;
            $data['calculationratedate']                            = $invoice->calculationratedate;
        }
          if ($invoice->type_documents_id == 100) {
            $data['calculationrate']                        = $invoice->calculationrate;
            $data['calculationratedate']                    = $invoice->calculationratedate ;
        }
        $data['idcurrency'] = $invoice->idcurrency;
        $currency = new Currency();
        $currencies = $currency->where(['id' => isset($invoice->idcurrency) ? $invoice->idcurrency : 35])
            ->asObject()
            ->get()
            ->getResult()[0];
        $data['currencyCode'] = $currencies->code;
        $lineInvoice = new LineInvoice();
        $products = $lineInvoice->select('*,line_invoices.id as id_line_invoice, line_invoices.description  as description')
            ->join('products', 'line_invoices.products_id = products.id')
            ->where('invoices_id', $id)
            ->get()
            ->getResult();
        $i = 0;
        foreach ($products as $key) {
            $data[$type][$i]['product_id'] = $key->products_id;
            $data[$type][$i]['code'] = $key->code;
            $data[$type][$i]['name'] = $key->name;
            $data[$type][$i]['price_amount'] = $key->price_amount;
            $data[$type][$i]['line_extension_amount'] = $key->line_extension_amount;
            $data[$type][$i]['description'] = $key->description;
            $data[$type][$i]['unit_measure_id'] = $key->unit_measures_id;
            $data[$type][$i]['type_item_identification_id'] = $key->type_item_identifications_id;
            $data[$type][$i]['base_quantity'] = 1;
            $data[$type][$i]['free_of_charge_indicator'] = ($key->free_of_charge_indicator == 'true') ? true : false;
            $data[$type][$i]['reference_price_id'] = $key->reference_prices_id;
            $data[$type][$i]['value']             = $key->valor;
            $data[$type][$i]['invoiced_quantity'] = (double)$key->quantity;
            $data[$type][$i]['allowance_charges'][0]['valor'] = (double) $key->discount_amount;
            $data[$type][$i]['allowance_charges'][0]['charge_indicator'] = false;
            $data[$type][$i]['allowance_charges'][0]['amount'] = (double)$data[$type][$i]['allowance_charges'][0]['valor'];
            $data[$type][$i]['allowance_charges'][0]['base_amount'] = (double) $key->valor;
            $data[$type][$i]['allowance_charges'][0]['discount_id'] = 1;
            $data[$type][$i]['allowance_charges'][0]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
    /**
            * @author wilson andres bachiller ortiz
            * @email wbachiller@iplanetcolombia.com
            * @method datos de terceros en el  producto
             * @date 18/11/2020
            * */
        	if(!empty($key->provider_id) && is_null($key->provider_id) == false) {
	    	$customer = new Customer();
		    $customers = $customer->asObject()->find($key->provider_id);
                $data[$type][$i]['agentparty'] 		= $customers->identification_number;
                $data[$type][$i]['agentparty_dv'] 	= $customers->dv;
            }
            $taxes = new LineInvoiceTax();
            $taxes = $taxes->select('*')
                ->where(['line_invoices_id' => $key->id_line_invoice])
                ->get()
                ->getResult();
            foreach ($taxes as $value) {
                if ($value->taxes_id == 1) {
                    $data[$type][$i]['tax_totals'][0]['tax_amount'] = (double) $value->tax_amount;
                    $data[$type][$i]['tax_totals'][0]['taxable_amount'] = (double) $value->taxable_amount;
                    $data[$type][$i]['tax_totals'][0]['percent'] = $value->percent;
                    $data[$type][$i]['tax_totals'][0]['tax_id'] = (int)$value->taxes_id;
                } else {
                    if ($value->taxes_id == 5) {
                        $data[$type][$i]['with_holding_tax_total'][0]['tax_id'] = 5;
                        $data[$type][$i]['with_holding_tax_total'][0]['percent'] = $value->percent;
                        $data[$type][$i]['with_holding_tax_total'][0]['tax_amount'] = (double)$value->tax_amount;
                        $data[$type][$i]['with_holding_tax_total'][0]['taxable_amount'] = (double)$value->taxable_amount;
                    } else if ($value->taxes_id == 6) {
                        $data[$type][$i]['with_holding_tax_total'][1]['tax_id'] = 6;
                        $data[$type][$i]['with_holding_tax_total'][1]['percent'] = $value->percent;
                        $data[$type][$i]['with_holding_tax_total'][1]['tax_amount'] = (double)$value->tax_amount;
                        $data[$type][$i]['with_holding_tax_total'][1]['taxable_amount'] = (double)$value->taxable_amount;
                    } else if ($value->taxes_id == 7) {
                        $data[$type][$i]['with_holding_tax_total'][2]['tax_id'] = 7;
                        $data[$type][$i]['with_holding_tax_total'][2]['percent'] = $value->percent;
                        $data[$type][$i]['with_holding_tax_total'][2]['tax_amount'] = (double)$value->tax_amount;
                        $data[$type][$i]['with_holding_tax_total'][2]['taxable_amount'] = (double)$value->taxable_amount;
                    }
    }
            }
        
            if(isset($data[$type][0]['tax_totals']) && is_null($data[$type][0]['tax_totals']))
    {
                $data[$type][$i]['tax_totals'][0]['tax_id'] = 1;
                $data[$type][$i]['tax_totals'][0]['taxable_amount'] = (double) $value->taxable_amount;
                $data[$type][$i]['tax_totals'][0]['tax_amount'] = 0;
                $data[$type][$i]['tax_totals'][0]['percent'] = 0;
            }
            $i++;
        }
        $iva = [];
        $percent = [];
        foreach ($data[$type] as $value) {
            foreach ($value['tax_totals'] as $item) {
                if (!array_key_exists($item['percent'], $percent)) {
                    array_push($iva, $item);
                    $percent["" . $item['percent'] . ""] = $item['percent'];
                } else {
                    $i = 0;
                    foreach ($iva as $valid) {
                        if ($valid['percent'] == $item['percent']) {
                            $iva[$i]['tax_amount'] += $item['tax_amount'];
                            $iva[$i]['taxable_amount'] += $item['taxable_amount'];
                        }
                        $i++;
                    }
                }
            }
        }
   //DURO DE MATAR 
         if ($invoice->type_documents_id == 1 || $invoice->type_documents_id == 2 || $invoice->type_documents_id == 100) {
            $retention = [];
            $l = 0;
            foreach ($data[$type] as $item):
                if(isset($item['with_holding_tax_total'])):
                foreach ($item['with_holding_tax_total'] as $taxTotal):
                    if ($taxTotal['tax_id'] == '5' || $taxTotal['tax_id'] == '6' || $taxTotal['tax_id'] == '7'):
                        if (array_key_exists($taxTotal['tax_id'], $retention)):
                            $newTaxes = false;
                            	$position = 0;
                          	$cordination = 0;
                                foreach($retention[$taxTotal['tax_id']] as $item3) {
                                    if(isset( $item3['percent']) &&  $taxTotal['percent'] == $item3['percent']) {
                                        $newTaxes = true;
                                        $cordination = $position;
					break;
                            }else {
                                        $position++;
        }
                                }
                            if ($newTaxes):
                                $retention[$taxTotal['tax_id']][$cordination]['taxable_amount'] += $taxTotal['taxable_amount'];
                                $retention[$taxTotal['tax_id']][$cordination]['tax_amount'] += $taxTotal['tax_amount'];
                            else:
                                $retention[$taxTotal['tax_id']][ count($retention[$taxTotal['tax_id']]) ] = $taxTotal;
                            endif;
                        else:
                            $retention[$taxTotal['tax_id']][0] = $taxTotal;
			    $l++;
                        endif;
                    endif;
                endforeach;
                endif;
            endforeach;
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
            $data['with_holding_tax_total'] = $array;
         }
        $data['tax_totals'] = $iva;
         
        $url = '';
   
        if ($invoice->type_documents_id == 1  || $invoice->type_documents_id == 2) {
            $url = 'previsualization';
        } else if($invoice->type_documents_id == 100){
            $url = 'quotation';
    }
        /*
        * @author wilson andres bachiller ortiz
        * @email wbachiller@iplanetcolombia.com
        * @method se eliminan validaciones de tipo de documento ya que no son nesesarias
         * @date 18/11/2020
        * */
        $companies = new Company();
        $info = $companies->find($company);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => getenv('API'). "/ubl2.1/".$url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer " . $info['token'],
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $info = json_decode($response);
        
        
    }

    public function preview($id)
    {
       //header('Content-Type: application/json; charset=utf-8');
       //http_response_code(200);

        $resolution   = null;
        $data         = $this->createDocument($id, $resolution, Auth::querys()->companies_id, true);

       // echo json_encode($data);die();

        switch ($data['type_document_id']) {
            case 2:
            case 1:
            case 3:
                $name   = 'FES-PREV' . $id;
                $url    = getenv('API') . '/ubl2.1/preview/invoice';
                break;
            case 4:
                $name   = 'NCS-PREV1';
                $url    = getenv('API') . '/ubl2.1/preview/credit-note';
                break;
            case 5:
                $name   = 'NDS-PREV1';
                $url    = getenv('API') . '/ubl2.1/preview/debit-note';
                break;
        }

        $res = $this->sendRequest($url, $data, 'post', $this->_token);
      //  echo json_encode($res);die();
        $model = new Company();
        $company = $model->where(['id' => Auth::querys()->companies_id ])->asObject()->first();
        return $this->downloadFile(getenv('API') . "/invoice/".$company->identification_number."/".$name.'.pdf', 'application/pdf',$name.'.pdf');
    }

    public function send($id = null)
    {
      //  header('Content-Type: application/json; charset=utf-8');
     //   http_response_code(200);
        $resolution   = $this->request->getPost('resolution_id');
        $data         = $this->createDocument($id,  $resolution, Auth::querys()->companies_id);


        $model = new Invoice();
        $model->set('resolution', $data['number'])
            ->set('resolution_id', $data['resolution_number'])
            ->set('prefix', $data['prefix'])
            ->where(['id' => $id])
            ->update();

        switch ($data['type_document_id']){
            case 1:
                $link = 'invoice';
                break;
            case 2:
                $link = 'invoice-export';
                break;
            case 4:
                $link = 'credit-note';
                break;
            case 5:
                $link = 'debit-note';
                break;
        }
        $res            = $this->sendRequest(getenv('API').'/ubl2.1/'.$link, $data, 'post', $this->_token);
        $documentStatus = $this->validStatusCodeHTTP($id, 1, $res, $data['type_document_id']);
        if(isset($documentStatus->error) && $documentStatus->error == true ) {
            if(isset($res->data->cufe) || isset($res->data->cude)) {
                $model = new Invoice();
                $model->set('uuid', isset($res->data->cufe) ? $res->data->cufe : $res->data->cude)
                   // ->set('send', 'True')
                    ->where(['id' => $id])
                    ->update();
            }

            return redirect()->to(base_url().route_to('invoice.index'))->with('errors', showErrors($documentStatus->errors['data'], $documentStatus->errors['type']));
        }else if(isset($documentStatus->error) && $documentStatus->error == false){
            $model = new Invoice();
            $model->set('invoice_status_id', 2)
                ->set('send', 'True')
                ->where(['id' => $id])
                ->update();
            return redirect()->to(base_url().route_to('invoice.index'))->with('success', $documentStatus->messages);
        }else {
            return redirect()->to(base_url().route_to('invoice.index'))->with('errors', 'HTTP 500 - Falla en el servidor.');
        }
    }

}
