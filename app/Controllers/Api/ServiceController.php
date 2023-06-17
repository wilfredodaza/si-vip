<?php


namespace App\Controllers\Api;


use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Product;
use App\Models\ProductsDetails;
use App\Models\Resolution;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\I18n\Time;

class ServiceController extends ResourceController
{

    protected $format = 'json';

    public function __construct()
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept,  Authorization");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        header("Allow: GET, POST, OPTIONS, PUT, DELETE");
        header('content-type: application/json; charset=utf-8');
    }
    protected function _resolution($typeDocument, $id = null ) {
        
       
       $resolution = new Resolution();
       $resolution = $resolution->where([ 'companies_id' => Auth::querys()->companies_id, $typeDocument]);


        if($id) {
            $resolution->where(['resolution' => $id]);
            $consulta =['type_documents_id' => 1];
            $resolution->where($consulta);
        }else{
            $resolution->where(['type_documents_id' => $typeDocument ]);
        }
        
        $resolution = $resolution
            ->orderBy('id', 'DESC')
            ->asObject()
            ->first();
            
        

        $invoices = new Invoice();
        $invoices->select('invoices.resolution');
        if($id) {
            $invoices->where([ 'companies_id'=> Auth::querys()->companies_id, 'resolution_id =' => $id]);
        } else {
            $invoices->where(['companies_id'=> Auth::querys()->companies_id, 'type_documents_id' => $typeDocument ]);
        }
        
      
        $invoices = $invoices->orderBy('id', 'DESC')
            ->asObject()
            ->first();



        if(!$invoices){
            http_response_code(200);
            echo json_encode(['resolution' => $resolution->from]);

        }else {
            http_response_code(200);
            echo json_encode(['resolution' => ( $invoices->resolution + 1 )]);
        }
        die();
    }


    protected function _lineInvoice($invoiceLines, $invoiceId)
    {
        foreach ($invoiceLines as $value) {
            $line = [
                'invoices_id'           => $invoiceId,
                'discount_amount'       => $value->allowance_charges[0]->amount,
                'discounts_id'          => isset($value->discounts_id) ? 1 : null,
                'quantity'              => $value->invoiced_quantity,
                'line_extension_amount' => $value->line_extension_amount,
                'price_amount'          => $value->price_amount,
                'cost_amount'           => $this->_costProduct($value->product_id),
                'products_id'           => $value->product_id,
                'description'           => $value->description,
                'provider_id'           => isset($value->providerId) ? $value->providerId : null
            ];
            $lineInvoice = new LineInvoice();
            $lineInvoiceId = $lineInvoice->insert($line);
            $this->_taxLineInvoice($value,  $lineInvoiceId,  $line['line_extension_amount']);
        }
    }

    private  function _taxLineInvoice($taxTotal, $lineInvoiceId, $value)
    {
        foreach ($taxTotal->tax_totals as $taxe) {
            $tax = [
                'taxes_id'          => $taxe->tax_id,
                'tax_amount'        => $taxe->tax_amount,
                'percent'           => $taxe->percent,
                'taxable_amount'    => $taxe->taxable_amount,
                'line_invoices_id'  => $lineInvoiceId
            ];
            $lineInvoiceTax = new LineInvoiceTax();
            $lineInvoiceTax->insert($tax);
        }

        if(!isset($taxTotal->tax_totals) and count($taxTotal->tax_totals)) {
            $tax = [
                'taxes_id'          => 1,
                'tax_amount'        => 0,
                'percent'           => 0,
                'taxable_amount'    => $value,
                'line_invoices_id'  => $lineInvoiceId
            ];
            $lineInvoiceTax = new LineInvoiceTax();
            $lineInvoiceTax->insert($tax);
        }
        if (isset($taxTotal->with_holding_tax_total)) {
            foreach ($taxTotal->with_holding_tax_total as $retention) {
                $tax = [
                    'taxes_id'          => $retention->tax_id,
                    'tax_amount'        => $retention->tax_amount,
                    'percent'           => $retention->percent,
                    'taxable_amount'    => $retention->taxable_amount,
                    'line_invoices_id'  => $lineInvoiceId
                ];

                $lineInvoiceTax = new LineInvoiceTax();
                $lineInvoiceTax->insert($tax);
            }
        }
    }

    protected function _getInvoice($id)
    {
        $invoice = new Invoice();
        $invoice = $invoice
            ->select('*, invoices.id as id_invoice, invoices.notes as notes, invoices.created_at as created_at')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();
        $time = Time::parse($invoice->payment_due_date, 'America/Bogota');
        $date = $time->difference(Time::now());
        $data = [];

        $data['type_document_id']                   = $invoice->type_documents_id;
        $data['resolution_number']                  = $invoice->resolution_id;
        $data['payable_amount']                     = $invoice->payable_amount;
        $data['payment_form_id']                    = $invoice->payment_forms_id;
        $data['payment_method_id']                  = $invoice->payment_methods_id;
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
        $data['billing_reference']["issue_date"]    = explode(' ', $invoice->created_at)[0];
        $data['billing_reference']['date']          = date('Y-m-d');
        $data['notes']                              = $invoice->notes;
        $data['duration_measure']                   = $invoice->duration_measure;
        $data['idcurrency']                         = $invoice->idcurrency;
        $data['rate']                               = $invoice->calculationrate;
        $data['calculationratedate']                = $invoice->calculationratedate;
        $data['delevery_term_id']                   = isset($invoice->delevery_term_id) ? $invoice->delevery_term_id : '';
        $data['seller_id']                          = $invoice->seller_id ? $invoice->seller_id : '';
        $created                                    = explode(' ', $invoice->created_at);
        $data['created_at']                         = $created[0];
        http_response_code(200);
        echo json_encode($data);
        die();
    }

    protected function  _getLineInvoice($id, $type = 1)
    {

        $invoice = new LineInvoice();
        $products = $invoice->select('*, products.id as products_id, line_invoices.id as id, line_invoices.description as line_invoice_description')
            ->join('products', 'line_invoices.products_id = products.id')
            ->where(['invoices_id' => $id])
            ->get()
            ->getResult();

        $i = 0;
        foreach ($products as $key) {
            $data[$i]['id'] = $key->id;
            $data[$i]['product_id'] = $key->products_id;
            $data[$i]['code'] = $key->code;
            $data[$i]['name'] = $key->name;
            $data[$i]['price_amount'] = $key->line_extension_amount;
            $data[$i]['description'] = $key->line_invoice_description;
            $data[$i]['unit_measure_id'] = $key->unit_measures_id;
            $data[$i]['type_item_identification_id'] = $key->type_item_identifications_id;
            $data[$i]['base_quantity'] = 1;
            $data[$i]['free_of_charge_indicator'] = $key->free_of_charge_indicator;
            $data[$i]['reference_price_id'] = $key->reference_prices_id;
            $data[$i]['value'] = (double) $key->price_amount;
            $data[$i]['invoiced_quantity'] = (double) $key->quantity;
            $data[$i]['providerId'] = $key->provider_id;
            $data[$i]['allowance_charges'][0]['valor'] = (double) $key->discount_amount / $key->quantity;
            $data[$i]['allowance_charges'][0]['charge_indicator'] = false;
            $data[$i]['allowance_charges'][0]['amount'] = (int)$data[$i]['allowance_charges'][0]['valor'];
            $data[$i]['allowance_charges'][0]['base_amount'] = (int)$key->valor;
            $data[$i]['allowance_charges'][0]['discount_id'] = 1;//key->discounts_id;
            $data[$i]['allowance_charges'][0]['allowance_charge_reason'] = 'DESCUENTO GENERAL';
            $l = 0;

            $taxLineInvoices = new LineInvoiceTax();
            $taxLineInvoice = $taxLineInvoices->where(['line_invoices_id' => $key->id])->get()->getResult();
            foreach ($taxLineInvoice as $value) {

                if ($value->taxes_id == 1) {
                    $data[$i]['tax_totals'][0]['id'] = (int)$value->id;
                    $data[$i]['tax_totals'][0]['tax_amount'] = (double) $value->tax_amount;
                    $data[$i]['tax_totals'][0]['taxable_amount'] = (double) $value->taxable_amount;
                    $data[$i]['tax_totals'][0]['percent'] = (double)$value->percent;
                    $data[$i]['tax_totals'][0]['tax_id'] = (int)$value->taxes_id;
                } else {
                    if($type == 1) {
                        $data[$i]['with_holding_tax_total'][$l]['id'] = (int)$value->id;
                        $data[$i]['with_holding_tax_total'][$l]['tax_amount'] = (double) $value->tax_amount;
                        $data[$i]['with_holding_tax_total'][$l]['taxable_amount'] = (double) $value->taxable_amount;
                        $data[$i]['with_holding_tax_total'][$l]['percent'] = (double) $value->percent;
                        $data[$i]['with_holding_tax_total'][$l]['tax_id'] = (int)$value->taxes_id;
                        $l++;
                    }
                }
            }


            $i++;
        }
        http_response_code(200);
        echo json_encode($data);
        die();
    }


    public  function resolutionData($typeDocument, $resolutionNumber)  
    {

        $data = [
            'companies_id'          => Auth::querys()->companies_id
        ];
        if($typeDocument[0] == 1 || $typeDocument[0] == 2) {
            $data['resolution_id'] =   $resolutionNumber;
            $typeDocument[0]= 1;
            $typeDocument[1]= 2;
        }

        $invoice = new Invoice();
        $model = $invoice->select(['CONVERT(resolution,UNSIGNED INTEGER) as resolution'])
            ->where($data)
            ->whereIn('type_documents_id', $typeDocument)
            ->orderBy('resolution', 'DESC')
            ->asObject()
            ->first();



        if(!$model) {
            $data = [
                'companies_id'          => Auth::querys()->companies_id
            ];
            if($typeDocument[0] == 1 || $typeDocument[0] == 2) {
                $data['resolution']	=  $resolutionNumber;
                $typeDocument[0]    = 1;
                 $typeDocument[1]    = 2;
            }
            $resolution = new Resolution();
            $resolutions = $resolution->select(['from'])
                ->where($data)
                ->whereIn('type_documents_id', $typeDocument)
                ->asObject()
                ->first();

            return  (int) $resolutions->from;
        }else {
            return  $model->resolution + 1;
        }
    }






    protected function _resolutionOdoo($typeDocument,$company, $id = null) {



        $resolution = new Resolution();
        $resolution = $resolution->where([ 'companies_id' => $company]);

        if($id) {
            $resolution->where(['resolution' => $id]);
            $consulta =['type_documents_id' => 1];
            $resolution->where($consulta);
        }else{
            $resolution->where(['type_documents_id' => $typeDocument ]);
        }

        $resolution = $resolution
            ->orderBy('id', 'DESC')
            ->asObject()
            ->first();



        $invoices = new Invoice();
        $invoices->select('invoices.resolution');
        if($id) {
            $invoices->where([ 'companies_id'=> $company, 'resolution_id =' => $id]);

        } else {
            $invoices->where(['companies_id'=> $company, 'type_documents_id' => $typeDocument ]);
        }


        $invoices = $invoices->orderBy('id', 'DESC')
            ->asObject()
            ->first();



        if(!$invoices){
            return $this->respond(['resolution' => $resolution->from], 200);
        }else {
            return $this->respond(['resolution' => ( $invoices->resolution + 1 )], 200);
        }

    }

    /**
     * Esta función permite obtener el valor de costo del producto que tiene actualmente y lo retorna para ser guardado en line_invoices
     * @param $productId
     * @return mixed
     */

    protected function _costProduct($productId){
        $productsDetails = new ProductsDetails();
        $products = new Product();
        $product = $products->where(['id' => $productId])->asObject()->first();
        $detail= $productsDetails
            ->where(['id_product' => $productId, 'status' => 'active' ])
            ->asObject()->first();
        if(is_null($detail)){
            $cost_amount = $product->cost;
        }else{
            $cost_amount = $detail-> cost_value;
        }
        return $cost_amount;
    }

}
