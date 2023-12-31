<?php


namespace App\Controllers\Api\V2;

use App\Controllers\Api\Auth;
use App\Controllers\Api\PurchaseOrder;
use App\Controllers\ApiController;
use App\Controllers\HeadquartersController;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ProductsDetails;
use App\Models\ProductsSerial;
use App\Models\ProductsSerialDetail;
use App\Models\TrackingCustomer;
use App\Models\AccountingAcount;
use App\Models\Wallet;
use App\Traits\ValidationsTrait2;
use CodeIgniter\RESTful\ResourceController;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use ReflectionException;
use App\Models\Invoice;
use App\Models\Product;


class Inventory extends ResourceController
{
    use ValidationsTrait2;

    protected $apiPurchase;
    protected $tableProductsDetails;
    protected $tableCustomer;
    protected $controllerHeadquarters;
    protected $tableLineInvoices;
    protected $tableInvoices;
    protected $tableLineInvoicesTax;
    protected $tableProducts;
    protected $message;
    protected $quantityTotal;
    protected $productsOc;
    protected $idsProductsOc;
    protected $walletDiscount;

    public function __construct()
    {
        $this->apiPurchase = new PurchaseOrder();
        $this->tableProductsDetails = new ProductsDetails();
        $this->tableCustomer = new Customer();
        $this->controllerHeadquarters = new HeadquartersController();
        $this->tableLineInvoices = new LineInvoice();
        $this->tableInvoices = new Invoice();
        $this->tableLineInvoicesTax = new LineInvoiceTax();
        $this->tableProducts = new Product();
        $this->message = '';
        $this->quantityTotal = 0;
        $this->productsOc = [];
        $this->idsProductsOc = [];
        $this->walletDiscount = 0;
    }

    public function index()
    {

    }

    public function create()
    {
        try {
            $json = $this->request->getJSON();
            $close = true;
            if(isset($json->resolution)){
                $isCo = $this->isCo($json->resolution);
            }
            if($json->type_document_id == 114){
                if ($isCo) {
                    $this->validateCloseOc($json->resolution, $json->resolution);
                }
            }
            
            $headquarters = null;
            // if ($json->type_document_id == 115) {
            //     $customer = $this->tableCustomer->where(['id' => $json->customer_id])->asObject()->first();
            //     if (!is_null($customer)) {
            //         $headquarters = $customer->headquarters_id;
            //     }
            // }
            if ($json->type_document_id == 108 || $json->type_document_id == 115) {
                $outNew = $this->tableInvoices->where(['type_documents_id' => $json->type_document_id])->orderBy('id', 'DESC')->asObject()->first();
                $number = $outNew ? $outNew->resolution + 1 : 1;
                // if (count($outNew) > 0) {
                //     $number = $outNew[0]->resolution + 1;
                // } else {
                //     $number = 1;
                // }
            }

            if ($json->type_document_id == 107) {
                $serials = [];
                foreach ($json->invoice_lines  as $key => $detail) {
                    $serials_value = [];
                    foreach ($detail->serials as $key => $serial) {
                        array_push($serials_value, $serial->value.'-'.$serial->type_serial);
                    }
                    if(count($detail->serials) > 0) {
                        $pSerialM = new ProductsSerial();
                        $data = $pSerialM->whereIn('CONCAT(serial,"-", serial_type_id)', $serials_value)->get()->getResult();
                        if(count($data) > 0) {
                            foreach ($data as $key => $value) {
                                array_push($serials, $value->serial);
                            }
                        }
                    }
                }
                if(count($serials) > 0) {
                    $serial = implode(',', $serials);
                    return $this->respond(['status' => 500, 'code' => 500, 'title' => 'Seriales repetidos',  'data' => $serial]);
                }
            }
            // echo json_encode(session('user'));

            // Si el rol actual es gerente (15) toma la sede bodega (69)
            $idCompany = Auth::querys()->role_id == 15 ? 69 : Auth::querys()->companies_id;
                // return $this->respond(['status' => 500, 'code' => 500, 'data' =>  $json->type_document_id == 107 ? $idCompany : $json->companies_destination_id]);
            if($json->type_document_id == 108){
                $account = new AccountingAcount();
                $account = $account->where(['id' => $json->payment_form->payment_method_id])->asObject()->first();
                // return $this->respond(['status' => 500, 'code' => 500, 'data' => $account], 500);
            }

            if($json->type_document_id == 115){
                $notes = '<span>Se adjuntaron: </span><ul>';
                foreach ($json->invoice_lines as $key => $line) {
                    foreach ($line->serials as $key => $serial) {
                        $notes .= "<li>{$line->name} - {$serial->serial}</li>";
                    }
                }
                $notes .= '</ul>';
                $json->notes = $notes;
            }
            // var_dump($json); die();

            // return $this->respond(['status' => 500, 'code' => 500, 'title' => 'Seriales repetidos',  'data' => $notes], 500);

            $invoice = $this->tableInvoices->insert([
                'resolution' => ($json->type_document_id == 108) ? $number : $json->number,
                'resolution_id' => ($json->type_document_id == 108) ? 1 : (($json->type_document_id == 114)? null :$json->resolution),
                'payment_forms_id' => $json->payment_form->payment_form_id,
                'payment_methods_id' => ($json->type_document_id == 108) ? ( $json->payment_form->payment_form_id == 1 ? ($account->type_entry == 1 ? 47 : 10) : 1) : $json->payment_form->payment_method_id,//$json->payment_form->payment_method_id
                'payment_due_date' => ($json->payment_form->duration_measure == 0) ? date('Y-m-d') : $json->payment_form->payment_due_date,
                'duration_measure' => $json->payment_form->duration_measure,
                'type_documents_id' => ($json->type_document_id != 114) ? $json->type_document_id : 107,
                'line_extesion_amount' => $json->legal_monetary_totals->line_extension_amount,
                'tax_exclusive_amount' => $json->legal_monetary_totals->tax_exclusive_amount,
                'tax_inclusive_amount' => $json->legal_monetary_totals->tax_inclusive_amount,
                'allowance_total_amount' => $json->legal_monetary_totals->allowance_total_amount,
                'charge_total_amount' => $json->legal_monetary_totals->charge_total_amount,
                'payable_amount' => $json->legal_monetary_totals->payable_amount,
                'customers_id' => $json->type_document_id == 115 ?  null : $json->customer_id, // 
                'created_at' => date('Y-m-d H:i:s'),
                'invoice_status_id' => ($json->type_document_id != 115) ? 2 : 22,
                'notes' => ($json->type_document_id == 114)?"Remision generada con orden de compra # {$json->resolution} <br>".$json->notes:$json->notes,
                'companies_id' => $json->type_document_id == 107 ? 2 : ($json->type_document_id == 114 ? 2 : $idCompany ),
                'company_destination_id' => $json->type_document_id == 107 ? $idCompany : ($json->type_document_id == 115 ? $json->customer_id : ($json->type_document_id == 114 ? $idCompany : 3 )),
                'idcurrency' => $json->idcurrency ?? 35,
                'calculationrate' => $json->calculationrate ?? 1,
                'calculationratedate' => $json->calculationratedate ?? date('Y-m-d'),
                'status_wallet' => ($json->type_document_id == 108 &&  $json->payment_form->payment_form_id == 1)?'Paga':'Pendiente',
                'user_id' => Auth::querys()->id,
                'seller_id' => $json->seller_id ?? null,
                'delevery_term_id' => $json->type_document_id == 2 ? $json->delevery_term_id : NULL,
                'issue_date' => $json->date ?? null,
                'resolution_credit' => ($json->type_document_id == 114)? $json->resolution : null,
                'headquarters_id' => $headquarters
            ]);

            $id = $invoice;

            $this->lineInvoices($json, $id);
            if($json->type_document_id == 114){
                if ($isCo) {
                    foreach ($this->idsProductsOc as $ids) {
                        if ($this->productsOc[$ids->id] > 0) {
                            $close = false;
                        }
                    }
                    $oc = $this->tableInvoices->where('id', $json->resolution)->asObject()->first();
                    //$manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
                    // if ($manager) {
                    //     $idCompany = $this->controllerHeadquarters->idSearchBodega();
                    // } else {
                    //     $idCompany = Auth::querys()->companies_id;
                    // }
                    $company = new Company();
                    $companyName = $company->select('company')->where('id', $idCompany)->asObject()->first();
                    $messages = "<br> Entrada por remisión # {$json->number} - sede {$companyName->company} - Cantidad de productos  {$this->quantityTotal} - Valor $ {$json->legal_monetary_totals->payable_amount} ";
                    $messages .= $this->message;

                    $tracking = new TrackingCustomer();
                    $count = $tracking->where('table_id', $oc->id)->get()->getResult();
                    if (count($count) > 0) {
                        $this->apiPurchase->generateTracking($oc->id, 'tracking', $messages);
                    } else {
                        $this->apiPurchase->generateTracking($oc->id, 'create', $messages);
                    }
                    if ($close) {
                        $messages = '';
                        foreach ($this->idsProductsOc as $ids) {
                            if ($this->productsOc[$ids->id] < 0) {
                                $messages .= "<br> El producto {$ids->name} supera la cantidad la cantidad de productos que se adquirio";
                            }
                        }
                        $this->apiPurchase->generateTracking($oc->id, 'close', $messages);
                    }

                    // $this->apiPurchase->generateTracking($json->idOc, 'close');
                }
            }
            if ($json->type_document_id == 115) {
                // $idInput = $this->createTransfer($id, $headquarters);
                // $this->tableInvoices->set(['resolution_credit' => $idInput])->where(['id' => $id])->update();
            }
            if($json->type_document_id == 108 &&  $json->payment_form->payment_form_id == 1){
                $wallet = [
                    'value' => $json->legal_monetary_totals->payable_amount - $this->walletDiscount,
                    'description' => "Se realiza pago de Contado",
                    'payment_method_id' => $json->payment_form->payment_method_id,
                    'invoices_id' => $id,
                    'created_at' => date("Y-m-d H:i:s"),
                    'user_id' => Auth::querys()->id,
                    'companies_id' => Auth::querys()->companies_id,
                ];
                $tableWallet = new Wallet();
                $tableWallet->save($wallet);
            }

            if($json->type_document_id == 100){
                $this->tableInvoices->save(['id' => $json->id, 'invoice_status_id' => 6]);
            }
            $json->id = $id;
            if ($id) {
                $api = new ApiController();
                // $api->preview(Auth::querys()->companies_id, $id);
                return $this->respond(['status' => 201, 'code' => 201, 'data' => $json, 'message' => 'Guardado Correctamente.']);
            }
        } catch (\Exception $e) {
            return $this->respond(['status' => 500, 'code' => 500, 'data' => $e->getMessage()]);
        }
    }

    public function edit($id = null)
    {
        
        // $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        /** @autor john vergara
         * se realiza ajuste para que desde gerencia se pueda editar los datos de inventario
         */
        // $invoice = $this->tableInvoices->where(['id' => $id, 'companies_id' => Auth::querys()->companies_id])->asObject()->first();
        $invoice = $this->tableInvoices->where(['id' => $id])->asObject()->first();
        if (is_null($invoice)) {
            return $this->respond(['status' => 404, 'code' => 404, 'data' => 'Not Found']);
        }

        $data = [];
        $data['number'] = $invoice->resolution;
        $data['companies_id'] = $invoice->companies_id;
        $data['company_destination_id'] = $invoice->company_destination_id;
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
        $data['issue_date'] = $invoice->issue_date;
        $data['created_at'] = date('Y-m-d', strtotime($invoice->created_at));
        $data['headquarters_id'] = false;
        if ($invoice->headquarters_id == Auth::querys()->companies_id) {
            $data['headquarters_id'] = true;
        }
        $isCo = $this->isCo($id);
        $entryRemision = $this->tableInvoices->select('resolution_credit')->where('id', $id)->asObject()->first();
        if ($isCo) {
            $this->validateCloseOc($id, $id);
        }
        $lineInvoice = $this->tableLineInvoices
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
                'line_invoices.cost_amount',
                'line_invoices.provider_id',
                'line_invoices.discount_amount',
            ])
            ->join('products', 'products.id = line_invoices.products_id')
            ->where(['invoices_id' => $id])
            ->asObject()
            ->findAll();


        $i = 0;
        foreach ($lineInvoice as $item) {
            if($invoice->type_documents_id == 107){
                $serials = $this->getSerial($item->products_id, $invoice->id);
                $data['invoice_lines'][$i]['serials'] = $serials;
                $data['invoice_lines'][$i]['serials_sales'] = [];
            }else if($invoice->type_documents_id == 108){
                $serials = $this->getSerial($item->products_id, $invoice->id);
                $serialsSales = $this->getSerialsSales($item->products_id, $invoice->id);
                $data['invoice_lines'][$i]['serials'] = $serials;
                $data['invoice_lines'][$i]['serials_sales'] = $serialsSales;
            }
            $data['invoice_lines'][$i]['product_id'] = $item->products_id;
            $data['invoice_lines'][$i]['invoice_line_id'] = $item->id;
            $data['invoice_lines'][$i]['unit_measure_id'] = 70;
            $data['invoice_lines'][$i]['invoiced_quantity'] = (int)$item->quantity;
            $data['invoice_lines'][$i]['line_extension_amount'] = (int)$item->line_extension_amount;
            $data['invoice_lines'][$i]['free_of_charge_indicator'] = $item->free_of_charge_indicator;
            $data['invoice_lines'][$i]['description'] = $item->description;
            $data['invoice_lines'][$i]['code'] = $item->code;
            $data['invoice_lines'][$i]['type_item_identification_id'] = 4;
            $data['invoice_lines'][$i]['base_quantity'] = ($isCo)?$this->productsOc[$item->products_id]:((int)$item->quantity);
            $data['invoice_lines'][$i]['name'] = $item->name;
            $data['invoice_lines'][$i]['price_amount'] = (int)$item->price_amount;
            $data['invoice_lines'][$i]['price_cost'] = (int)$item->cost_amount;
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
            $lineInvoiceTax = $this->tableLineInvoicesTax->where(['line_invoices_id' => $item->id])
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

    public function getSerial($id_product, $id_invoice){
        
        $pSerialM = new ProductsSerial();
        $serials = $pSerialM
            ->select([
                'products_serial.*',
                'products_serial.serial',
                'serial_type.name as serial_type_name',
                'serial_type.id as type_serial',
                'products_serial_detail.invoices_id',
                'products_serial_detail.id as pro_serial_det_id',
                '(SELECT COUNT(id) FROM products_serial_detail WHERE products_serial_id = products_serial.id) as total_details', // Contador de detalles
                '0 as isDeleted',
            ])
            ->where([
                'products_serial.products_id' => $id_product,
                'products_serial_detail.invoices_id' => $id_invoice,
            ])
            ->join('products_serial_detail', 'products_serial_detail.products_serial_id = products_serial.id', 'left')
            ->join('serial_type', 'serial_type.id = products_serial.serial_type_id', 'left')
            ->groupBy('products_serial.id') // Agrupa por products_serial para contar detalles por cada uno
            ->get()->getResult();

        return $serials;
    }

    public function getSerialsSales($id_product, $id_invoice){
        $pSerialM = new ProductsSerial();
        $serials = $pSerialM
            ->select([
                'products_serial.*',
                'products_serial.serial as name',
                'serial_type.name as serial_type_name',
                'serial_type.id as type_serial',
                'products_serial_detail.invoices_id',
                'products_serial_detail.id as pro_serial_det_id',
                'invoices.company_destination_id'
            ])
            ->where([
                'products_serial.products_id' => $id_product,
                'invoices.company_destination_id' => Auth::querys()->companies_id,
                'invoices.type_documents_id !=' => 115
            ])
            ->orWhere('(invoices.type_documents_id = 115 and invoices.invoice_status_id = 21 and invoices.company_destination_id = '.Auth::querys()->companies_id.')')
            ->join('products_serial_detail', 'products_serial_detail.products_serial_id = products_serial.id and products_serial_detail.invoices_id = (select max(invoices_id) from products_serial_detail where products_serial_id = products_serial.id and invoices_id != '.$id_invoice.')', 'left')
            ->join('invoices', 'invoices.id = products_serial_detail.invoices_id', 'left')
            ->join('serial_type', 'serial_type.id = products_serial.serial_type_id', 'left')
            ->get()->getResult();
        return $serials;
    }

    public function update($id = null)
    {
        try {
            //$close = true;
            //$isCo = $this->isCo($id);
            //$entryRemision = $this->tableInvoices->select('resolution_credit')->where('id', $id)->asObject()->first();
            //if ($isCo) {
           //     $this->validateCloseOc($entryRemision->resolution_credit, $id);
            //}
            // echo json_encode($this->productsOc);
            $invoice = new \App\Models\Invoice();
            $invoices = $invoice->where([
                'id' => $id,
                // 'type_documents_id >' => 100,
                // 'invoice_status_id' => 1
            ])->countAllResults();

            if ($invoices == 0) {
                $invoices = $invoice->where([
                    'id' => $id,
                    'type_documents_id >' => 114,
                    'invoice_status_id' => 22
                ])->countAllResults();
                if ($invoices == 0) {
                    $invoices = $invoice->where([
                        'id' => $id,
                        'type_documents_id >' => 114,
                        'invoice_status_id' => 20
                    ])->countAllResults();
                    if ($invoices == 0) {
                        return $this->respond(['status' => 404, 'code' => 404, 'data' => 'Not Found']);
                    }
                }
            }

            $json = $this->request->getJSON();
            $invoiceLines = $json->invoice_lines;
            $data = [
                'resolution' => $json->number,
                'payment_forms_id' => $json->payment_form->payment_form_id,
                'payment_methods_id' => $json->payment_form->payment_method_id,
                'payment_due_date' => ($json->payment_form->duration_measure == 0) ? date('Y-m-d') : $json->payment_form->payment_due_date,
                'duration_measure' => $json->payment_form->duration_measure,
                'line_extesion_amount' => $json->legal_monetary_totals->line_extension_amount,
                'tax_exclusive_amount' => $json->legal_monetary_totals->tax_exclusive_amount,
                'tax_inclusive_amount' => $json->legal_monetary_totals->tax_inclusive_amount,
                'allowance_total_amount' => $json->legal_monetary_totals->allowance_total_amount,
                'charge_total_amount' => $json->legal_monetary_totals->charge_total_amount,
                'payable_amount' => $json->legal_monetary_totals->payable_amount,
                'type_documents_id' => $json->type_document_id,
                'customers_id' => $json->customer_id,
                'notes' => $json->notes,
                'idcurrency' => $json->currency_id ?? 35,
                'calculationrate' => $json->currency_rate ?? 1,
                'calculationratedate' => $json->currency_rate_date ?? date('Y-m-d'),
                'delevery_term_id' => $json->type_document_id == 2 ? $json->delevery_term_id : NULL,
                'issue_date' => $json->date
            ];
            if ($json->type_document_id != 115 && $json->type_document_id != 116) {
                $data['invoice_status_id'] = 1;
            }

            if (isset($json->update_date) && $json->update_date == true) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            $invoice = new \App\Models\Invoice();
            $invoice->set($data)
                ->where(['id' => $id])
                ->update();
            $invoiceOrigin = $this->tableInvoices->where(['id' => $id])->asObject()->first();
            // return $this->respond(['status' => 500, 'code' => 500, 'data' => 'hola']);
            $this->editLineInvoiceTransfer($json, $invoiceLines, $id, $invoiceOrigin);
            if ($invoice) {
                $api = new ApiController();
                //$api->preview(Auth::querys()->companies_id, $id);
                http_response_code(201);
                echo json_encode(['status' => 'ok', 'code' => 201, 'message' => 'Guardado Correctamente.']);
                die();
            }
        } catch (\Exception $e) {
            return $this->respond(['status' => 500, 'code' => 500, 'data' => $e->getMessage()]);
        }
    }

    public function delete($id = null){
        try {
            $invoiceModel = new Invoice();
            $request = $this->request->getJSON();
            $motivo = ($request->motivo ? $request->motivo : 'Sin motivo');
            $invoice = $invoiceModel->where(['id' => $id])->asObject()->first();
            $invoice = $invoiceModel->save([
                'id'                    => $id,
                'notes'                 => $motivo,
                'invoice_status_id'     => 28,
                'line_extesion_amount'  => 0,
                'payable_amount'        => 0,
                'tax_exclusive_amount'  => 0,
                'tax_inclusive_amount'  => 0,
                'user_id'               => Auth::querys()->id
            ]);
            $invoiceLineModel = new LineInvoice();
            $lineInvoices = $invoiceLineModel->where(['invoices_id' => $id])->asObject()->get()->getResult();
            $invoiceLineModel = new LineInvoice();
            foreach ($lineInvoices as $key => $lineInvoice) {
                $invoiceLineModel->save([
                    'id' => $lineInvoice->id,
                    'line_extension_amount' => 0,
                    'price_amount' => 0,
                    'quantity' => 0
                ]);
            }
            return $this->respond(['status' => 201, 'code' => 201, 'data' => $lineInvoices, 'request' => $request, 'message' => 'Guardado Correctamente.']);
        } catch (\Exception $th) {
            return $this->respond(['status' => 500, 'code' => 500, 'data' => $e->getMessage()]);
        }
    }

    /**
     * Funcion que permite crear la entrada por transferencia a una sede
     * @param $idInvoice
     * @param $idHeadquarters
     */
    public function createTransfer($idInvoice, $idHeadquarters)
    {
        try {
            $customer = $this->tableCustomer->where(['companies_id' => $idHeadquarters, 'headquarters_id' => Auth::querys()->companies_id])->asObject()->first();
            $json = $this->request->getJSON();
            $invoice = $this->tableInvoices->insert([
                'resolution' => $json->number,
                'resolution_id' => $json->resolution,
                'payment_forms_id' => $json->payment_form->payment_form_id,
                'payment_methods_id' => $json->payment_form->payment_method_id,
                'payment_due_date' => ($json->payment_form->duration_measure == 0) ? date('Y-m-d') : $json->payment_form->payment_due_date,
                'duration_measure' => $json->payment_form->duration_measure,
                'type_documents_id' => 116,
                'line_extesion_amount' => $json->legal_monetary_totals->line_extension_amount,
                'tax_exclusive_amount' => $json->legal_monetary_totals->tax_exclusive_amount,
                'tax_inclusive_amount' => $json->legal_monetary_totals->tax_inclusive_amount,
                'allowance_total_amount' => $json->legal_monetary_totals->allowance_total_amount,
                'charge_total_amount' => $json->legal_monetary_totals->charge_total_amount,
                'payable_amount' => $json->legal_monetary_totals->payable_amount,
                'customers_id' => $customer->id,
                'created_at' => date('Y-m-d H:i:s'),
                'invoice_status_id' => 22,
                'notes' => $json->notes,
                'companies_id' => $idHeadquarters,
                'idcurrency' => $json->idcurrency ?? 35,
                'calculationrate' => $json->calculationrate ?? 1,
                'calculationratedate' => $json->calculationratedate ?? date('Y-m-d'),
                'status_wallet' => 'Pendiente',
                'user_id' => Auth::querys()->id,
                'seller_id' => $json->seller_id ?? null,
                'delevery_term_id' => $json->type_document_id == 2 ? $json->delevery_term_id : NULL,
                'issue_date' => $json->date ?? null,
                'resolution_credit' => $idInvoice,
                'headquarters_id' => Auth::querys()->companies_id
            ]);

            $this->lineInvoices($json, $invoice);
            $api = new ApiController();
            // $api->preview($idHeadquarters, $invoice);
            return $invoice;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Funcion que permite realizar el cargue de la line invoices y taxes invoices para los procesos de crete y createTransfer
     * @param $json
     * @param $invoice
     * @throws ReflectionException
     */
    private function lineInvoices($json, $invoice): void
    {
        if($json->type_document_id == 114){
            $isCo = $this->isCo($invoice);
        }
        foreach ($json->invoice_lines as $value) {
            if($json->type_document_id == 114){
                $this->validateCreateRemision($isCo, $json, $value, $invoice);
                $this->quantityTotal = $this->quantityTotal + $value->invoiced_quantity;
                $this->productsOc[ $value->product_id] = $this->productsOc[ $value->product_id] - $value->invoiced_quantity;
            }
            $productM = new Product();
            $productAux = $productM
                        ->select(['products.cost', 'products_details.cost_value'])
                        ->where(['products.id' => $value->product_id,])
                        ->join('products_details', 'products_details.id_product = products.id and products_details.status = "active"', 'left')
                        ->asObject()->first();
            $line = [
                'invoices_id' => $invoice,
                'discount_amount' => $value->allowance_charges[0]->amount,
                'discounts_id' => 1,
                'quantity' => $value->invoiced_quantity,
                'line_extension_amount' => $value->line_extension_amount,
                'price_amount' => $value->price_amount,
                'cost_amount' => $productAux->cost_value ? $productAux->cost_value : $productAux->cost,
                'products_id' => $value->product_id,
                'description' => $value->description,
                'provider_id' => $value->providerId ?? null
            ];
            $lineInvoiceId = $this->tableLineInvoices->insert($line);
            if($json->type_document_id != 108){
                $this->tableProductsDetails
                    ->set(['status' => 'inactive'])
                    ->where(['id_product' => $value->product_id])
                    ->update();
                $productDetail = [
                    'id_product' => $value->product_id,
                    'id_invoices' => $invoice,
                    'created_at' => date('Y-m-d'),
                    'policy_type' => 'general',
                    'cost_value' => $value->price_amount,
                ];
                $this->tableProductsDetails->insert($productDetail);
                foreach ($value->serials as $key => $serial) {
                    if($json->type_document_id == 115){
                        $id = $serial->id;
                    }else{
                        $pSerialM = new ProductsSerial();
                        $id = $pSerialM->insert([
                            'products_id' => $value->product_id,
                            'serial' => $serial->value,
                            'serial_type_id' => $serial->type_serial
                        ]);
                    }
                    $pSerialDetailM = new ProductsSerialDetail();
                    $pSerialDetailM->save([
                        'products_serial_id' => $id,
                        'invoices_id' => $invoice
                    ]);
                }
            }else{
                foreach ($value->serials as $key => $serial) {
                    $pSerialM = new ProductsSerial();
                    $pSerialM->save([
                        'id' => $serial->id,
                        'status' => 0
                    ]);
                    $pSerialDetailM = new ProductsSerialDetail();
                    $pSerialDetailM->save([
                        'products_serial_id' => $serial->id,
                        'invoices_id' => $invoice
                    ]);
                }
            }
            
            foreach ($value->tax_totals as $taxe) {
                $tax = [
                    'taxes_id' => $taxe->tax_id,
                    'tax_amount' => $taxe->tax_amount,
                    'percent' => $taxe->percent,
                    'taxable_amount' => $taxe->taxable_amount,
                    'line_invoices_id' => $lineInvoiceId
                ];
                if($taxe->tax_id == 6 || $taxe->tax_id == 7){
                    $this->walletDiscount +=  $taxe->tax_amount;
                }
                $this->tableLineInvoicesTax->insert($tax);
            }
        }
    }

    /**
     * @param $json
     * @param $invoiceLines
     * @param $idInvoice
     * @throws ReflectionException
     */
    private function editLineInvoiceTransfer($json, $invoiceLines, $idInvoice, $transfer): void
    {
        //$isCo = $this->isCo($idInvoice);
        foreach ($json->idDelete as $item) {
            if ($json->type_document_id == 115 || $json->type_document_id == 116) {
                $lineInvoices = $this->tableLineInvoices->where(['id' => $item])->asObject()->first();
                if(!empty($lineInvoices)){
                    $lineInvoicesHeadquartes = $this->tableLineInvoices
                        ->where(['products_id' => $lineInvoices->products_id, 'invoices_id' => $transfer->resolution_credit])
                        ->asObject()->first();
                    $this->tableLineInvoices->where(['id' => $lineInvoicesHeadquartes->id])->delete();
                    $this->tableLineInvoicesTax->where(['line_invoices_id' => $lineInvoicesHeadquartes->id])->delete();
                }
            }

            if($json->type_document_id == 107){
                $lineInvoices = $this->tableLineInvoices->where(['id' => $item])->asObject()->first();
                if(!empty($lineInvoices)){
                    $serials = $this->getSerial($lineInvoices->products_id, $idInvoice);
                    foreach ($serials as $key => $serial) {
                        $proSerialDet = new ProductsSerialDetail();
                        $proSerial = new ProductsSerial();
                        $proSerialDet->where(['id' => $serial->pro_serial_det_id])->delete();
                        $proSerial->where(['id' => $serial->id])->delete();
                    }
                    $this->tableLineInvoices->where(['id' => $item])->delete();
                    $this->tableLineInvoicesTax->where(['line_invoices_id' => $item])->delete();
                }
            }

            if($json->type_document_id == 108){
                $lineInvoices = $this->tableLineInvoices->where(['id' => $item])->asObject()->first();
                if(!empty($lineInvoices)){
                    $serials = $this->getSerial($lineInvoices->products_id, $idInvoice);
                    foreach ($serials as $key => $serial) {
                        $proSerialDet = new ProductsSerialDetail();
                        $proSerial = new ProductsSerial();
                        $proSerialDet->where(['id' => $serial->pro_serial_det_id])->delete();
                        $proSerial->set(['status' => 1])->where(['id' => $serial->id])->update();
                    }
                    $this->tableLineInvoices->where(['id' => $item])->delete();
                    $this->tableLineInvoicesTax->where(['line_invoices_id' => $item])->delete();
                }
            }


        }
        foreach ($invoiceLines as $value) {
            if($json->type_document_id == 107){
                $serials = $this->getSerial($value->product_id, $idInvoice);
                foreach ($serials as $key => $serial) {
                    if($serial->total_details == 1){
                        $proSerialDet = new ProductsSerialDetail();
                        $proSerial = new ProductsSerial();
                        $proSerialDet->where(['id' => $serial->pro_serial_det_id])->delete();
                        $proSerial->where(['id' => $serial->id])->delete();
                    }
                }
            }else if ($json->type_document_id == 108){
                $serials = $this->getSerial($value->product_id, $idInvoice);
                foreach ($serials as $key => $serial) {
                    $proSerialDet = new ProductsSerialDetail();
                    $proSerial = new ProductsSerial();
                    $proSerialDet->where(['id' => $serial->pro_serial_det_id])->delete();
                    $proSerial->set(['status' => 1])->where(['id' => $serial->id])->update();
                }
            }
            if (isset($value->invoice_line_id)) {
                $idProduct = $value->product_id;
                //$this->validateCreateRemision($isCo, $json, $value, $idInvoice);
                //$this->quantityTotal = $this->quantityTotal + $value->invoiced_quantity;
                $line = [
                    'discount_amount' => $value->allowance_charges[0]->amount,
                    'discounts_id' => 1,
                    'quantity' => $value->invoiced_quantity,
                    'line_extension_amount' => $value->line_extension_amount,
                    'price_amount' => $value->price_amount,
                    'products_id' => $value->product_id,
                    'description' => $value->description,
                    'provider_id' => $value->providerId ?? null
                ];

                $lineInvoice = new LineInvoice();
                $lineInvoice->set($line)
                    ->where(['id' => $value->invoice_line_id])
                    ->update();
                if ($json->type_document_id == 115 || $json->type_document_id == 116) {
                    $lineInvoice->set($line)
                        ->where(['invoices_id' => $transfer->resolution_credit, 'products_id' => $idProduct])
                        ->update();
                }
                $this->tableProductsDetails
                    ->set(['cost_value' => $value->price_amount])
                    ->where(['id_product' => $value->product_id, 'id_invoices' => $idInvoice])
                    ->update();
                    
                foreach ($value->tax_totals as $taxe) {
                    $tax = [
                        "taxes_id" => $taxe->tax_id,
                        "tax_amount" => $taxe->tax_amount,
                        "percent" => $taxe->percent,
                        "taxable_amount" => $taxe->taxable_amount
                    ];
                    
                    $lineInvoiceTax = new LineInvoiceTax();
                    if ($json->type_document_id == 115 || $json->type_document_id == 116) {
                        // echo json_encode($transfer); 
                        $lineInvoiceTranferTax = $this->tableLineInvoices->where(['products_id' => $value->product_id, 'invoices_id' => $transfer->resolution_credit])->asObject()->first();
                        $lineInvoiceTax->set($tax)
                            ->where(['taxes_id' => $taxe->tax_id, 'line_invoices_id' => $lineInvoiceTranferTax->id])
                            ->update();
                            
                    }
                    $lineInvoiceTax->set($tax)
                        ->where(['taxes_id' => $taxe->tax_id, 'line_invoices_id' => $value->invoice_line_id])
                        ->update();

                }
                
            } else {
                //$this->validateCreateRemision($isCo, $json, $value, $idInvoice);
                //$this->quantityTotal = $this->quantityTotal + $value->invoiced_quantity;
                $lineInvoice = new LineInvoice();
                $productDetail = [
                    'id_product' => $value->product_id,
                    'id_invoices' => $idInvoice,
                    'created_at' => date('Y-m-d'),
                    'policy_type' => 'general',
                    'cost_value' => $value->price_amount,
                ];
                //edicion normal de productos
                $line = [
                    'discount_amount' => $value->allowance_charges[0]->amount,
                    'discounts_id' => 1,
                    'quantity' => $value->invoiced_quantity,
                    'line_extension_amount' => $value->line_extension_amount,
                    'price_amount' => $value->price_amount,
                    'products_id' => $value->product_id,
                    'description' => $value->name,
                    'invoices_id' => $idInvoice
                ];
                $this->tableProductsDetails
                    ->set(['status' => 'inactive'])
                    ->where(['id_product' => $value->product_id])
                    ->update();

                $lineId = $lineInvoice->insert($line);
                $this->tableProductsDetails->insert($productDetail);

                foreach ($value->tax_totals as $taxe) {
                    $tax = [
                        "taxes_id" => $taxe->tax_id,
                        "tax_amount" => $taxe->tax_amount,
                        "percent" => $taxe->percent,
                        "taxable_amount" => $taxe->taxable_amount,
                        "line_invoices_id" => $lineId
                    ];
                    $lineInvoiceTax = new LineInvoiceTax();
                    $lineInvoiceTax->insert($tax);
                }
                // para agragar productos de transferencia en la otra sede en edicion
                if ($json->type_document_id == 115 || $json->type_document_id == 116) {
                    $productDetail['id_product'] = $value->product_id;
                    $line['invoices_id'] = $transfer->resolution_credit;
                    $this->tableProductsDetails
                        ->set(['status' => 'inactive'])
                        ->where(['id_product' => $value->product_id])
                        ->update();
                    $lineIdTransfer = $lineInvoice->insert($line);
                    foreach ($value->tax_totals as $taxe) {
                        $tax = [
                            "taxes_id" => $taxe->tax_id,
                            "tax_amount" => $taxe->tax_amount,
                            "percent" => $taxe->percent,
                            "taxable_amount" => $taxe->taxable_amount,
                            "line_invoices_id" => $lineIdTransfer
                        ];
                        $lineInvoiceTax = new LineInvoiceTax();
                        $lineInvoiceTax->insert($tax);
                    }
                }
            }
            foreach ($value->serials as $key => $serial) {
                if($json->type_document_id == 107 && $serial->total_details == 1){
                    $pSerialM = new ProductsSerial();
                    $id = $pSerialM->insert([
                        'products_id' => $value->product_id,
                        'serial' => $serial->serial,
                        'serial_type_id' => $serial->type_serial
                    ]);
                    $pSerialDetailM = new ProductsSerialDetail();
                    $pSerialDetailM->save([
                        'products_serial_id' => $id,
                        'invoices_id' => $idInvoice
                    ]);
                }else if($json->type_document_id == 108){
                    $pSerialM = new ProductsSerial();
                    $pSerialM->save([
                        'id' => $serial->id,
                        'status' => 0
                    ]);
                    $pSerialDetailM = new ProductsSerialDetail();
                    $pSerialDetailM->save([
                        'products_serial_id' => $serial->id,
                        'invoices_id' => $idInvoice
                    ]);
                }
            }
        }
    }

    private function validateOcRemision($id, $idProduct)
    {
        $data = (object)[
            'quantity' => 0,
            'price_amount' => 0,
            'product' => false
        ];
        $oC = $this->tableInvoices
            ->select([
                'line_invoices.quantity',
                'line_invoices.price_amount'
            ])
            ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->where(['invoices.id' => $id, 'line_invoices.products_id' => $idProduct])
            ->asObject()->first();

        if (!is_null($oC)) {
            $data->quantity = $oC->quantity;
            $data->price_amount = $oC->price_amount;
            $data->product = true;
        }

        return $data;
    }

    private function isCo($id): bool
    {
        $result = false;
        $entryRemision = $this->tableInvoices->select('resolution_credit')->where('id', $id)->asObject()->first();
        $oc = $this->tableInvoices->where('id', $id)->asObject()->first();
        if (!is_null($oc)) {
            if ($oc->type_documents_id == 114) {
                $result = true;
            }
        }
        return $result;
    }

    /**
     * @param bool $isCo
     * @param $json
     * @param $value
     */
    private function validateCreateRemision(bool $isCo, $json, $value, $id)
    {
        if ($isCo) {
            $entryRemision = $this->tableInvoices->select('resolution_credit')->where('id', $id)->asObject()->first();
            $oc = $this->tableInvoices->where('id', $id)->asObject()->first();
            $dataOriginal = $this->validateOcRemision($oc->id, $value->product_id);
            $this->productsOc[$value->product_id] = $this->productsOc[$value->product_id] - $value->invoiced_quantity;
            if ($dataOriginal->product) {
                if ($value->invoiced_quantity > $dataOriginal->quantity) {
                    $this->message .= "<br> El producto {$value->description} supera las cantidades adquiridas";
                }
                if ($value->price_amount > $dataOriginal->price_amount) {
                    $this->message .= "<br> El producto {$value->description} supera el valor de compra al adquirirlo";
                }
            } else {
                $this->message .= "<br> El producto {$value->description} No existe en la orden de compra";
            }
        }
    }

    private function validateCloseOc($id, $idInvoice)
    {
        $oC = $this->tableInvoices
            ->select([
                'line_invoices.quantity',
                'line_invoices.products_id',
                'products.name'
            ])
            ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'line_invoices.products_id = products.id')
            ->where(['invoices.id' => $id])
            ->asObject()->get()->getResult();

        foreach ($oC as $item) {
            array_push($this->idsProductsOc, (object)['id' => $item->products_id, 'name' => $item->name]);
            $this->productsOc[$item->products_id] = $item->quantity;
        }
        $orders = $this->tableInvoices->select([
            'line_invoices.quantity',
            'line_invoices.products_id'
        ])
            ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->where(['invoices.resolution_credit' => $id])
            ->asObject()->get()->getResult();
        foreach ($orders as $order) {
            $this->productsOc[$order->products_id] = $this->productsOc[$order->products_id] - $order->quantity;
        }
    }
}