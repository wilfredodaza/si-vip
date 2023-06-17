<?php


namespace App\Controllers\Api\V2;

use App\Controllers\Api\Auth;
use App\Controllers\Api\PurchaseOrder;
use App\Controllers\ApiController;
use App\Controllers\HeadquartersController;
use App\Models\Company;
use App\Models\Customer;
use App\Models\ProductsDetails;
use App\Models\TrackingCustomer;
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
            if ($json->type_document_id == 115) {
                $customer = $this->tableCustomer->where(['id' => $json->customer_id])->asObject()->first();
                if (!is_null($customer)) {
                    $headquarters = $customer->headquarters_id;
                }
            }
            if ($json->type_document_id == 108) {
                $outNew = $this->tableInvoices->where(['type_documents_id' => 108])->orderBy('id', 'DESC')->asObject()->get()->getResult();
                if (count($outNew) > 0) {
                    $number = $outNew[0]->resolution + 1;
                } else {
                    $number = 1;
                }
            }
            $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
            if ($manager) {
                $idCompany = $this->controllerHeadquarters->idSearchBodega();
            } else {
                $idCompany = Auth::querys()->companies_id;
            }
            $invoice = $this->tableInvoices->insert([
                'resolution' => ($json->type_document_id == 108) ? $number : $json->number,
                'resolution_id' => ($json->type_document_id == 108) ? 1 : ($json->type_document_id == 114)? null :$json->resolution,
                'payment_forms_id' => $json->payment_form->payment_form_id,
                'payment_methods_id' => $json->payment_form->payment_method_id,
                'payment_due_date' => ($json->payment_form->duration_measure == 0) ? date('Y-m-d') : $json->payment_form->payment_due_date,
                'duration_measure' => $json->payment_form->duration_measure,
                'type_documents_id' => ($json->type_document_id != 114) ? $json->type_document_id : 107,
                'line_extesion_amount' => $json->legal_monetary_totals->line_extension_amount,
                'tax_exclusive_amount' => $json->legal_monetary_totals->tax_exclusive_amount,
                'tax_inclusive_amount' => $json->legal_monetary_totals->tax_inclusive_amount,
                'allowance_total_amount' => $json->legal_monetary_totals->allowance_total_amount,
                'charge_total_amount' => $json->legal_monetary_totals->charge_total_amount,
                'payable_amount' => $json->legal_monetary_totals->payable_amount,
                'customers_id' => $json->customer_id,
                'created_at' => date('Y-m-d H:i:s'),
                'invoice_status_id' => ($json->type_document_id != 115) ? 2 : 22,
                'notes' => ($json->type_document_id == 114)?"Remision generada con orden de compra # {$json->resolution} <br>".$json->notes:$json->notes,
                'companies_id' => $idCompany,
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
                    if ($manager) {
                        $idCompany = $this->controllerHeadquarters->idSearchBodega();
                    } else {
                        $idCompany = Auth::querys()->companies_id;
                    }
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
                $idInput = $this->createTransfer($id, $headquarters);
                $this->tableInvoices->set(['resolution_credit' => $idInput])->where(['id' => $id])->update();
            }
            if($json->type_document_id == 108 &&  $json->payment_form->payment_form_id == 1){
                $wallet = [
                    'value' => $json->legal_monetary_totals->payable_amount - $this->walletDiscount,
                    'description' => "Se realiza pago de Contado",
                    'payment_method_id' => 7,
                    'invoices_id' => $id,
                    'created_at' => date("Y-m-d H:i:s"),
                    'user_id' => Auth::querys()->id
                ];
                $tableWallet = new Wallet();
                $tableWallet->save($wallet);
            }
            $json->id = $id;
            if ($id) {
                $api = new ApiController();
                // $api->preview(Auth::querys()->companies_id, $id);
                return $this->respond(['status' => 201, 'code' => 201, 'data' => $json]);
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
            $data['invoice_lines'][$i]['base_quantity'] = ($isCo)?$this->productsOc[$item->products_id]:((int)$item->quantity);
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
                'type_documents_id >' => 100
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
            $line = [
                'invoices_id' => $invoice,
                'discount_amount' => $value->allowance_charges[0]->amount,
                'discounts_id' => 1,
                'quantity' => $value->invoiced_quantity,
                'line_extension_amount' => $value->line_extension_amount,
                'price_amount' => $value->price_amount,
                'products_id' => $value->product_id,
                'description' => $value->description,
                'provider_id' => $value->providerId ?? null
            ];
            $lineInvoiceId = $this->tableLineInvoices->insert($line);
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
                $lineInvoicesHeadquartes = $this->tableLineInvoices
                    ->where(['products_id' => $lineInvoices->products_id, 'invoices_id' => $transfer->resolution_credit])
                    ->asObject()->first();
                $this->tableLineInvoices->where(['id' => $lineInvoicesHeadquartes->id])->delete();
                $this->tableLineInvoicesTax->where(['line_invoices_id' => $lineInvoicesHeadquartes->id])->delete();
            }
            $this->tableLineInvoices->where(['id' => $item])->delete();
            $this->tableLineInvoicesTax->where(['line_invoices_id' => $item])->delete();
        }
        foreach ($invoiceLines as $value) {
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