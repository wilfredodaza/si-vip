<?php


namespace App\Controllers;


use App\Controllers\Api\Auth;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Product_transfer;
use App\Models\TypeDocument;
use App\Models\Product;
use App\Models\Company;
use App\Models\AccountingAcount;
use App\Models\Resolution;

class InventoryController extends BaseController
{
    public $customer;
    public $type_document;
    public $line_invoice;
    public $query;
    public $tableInvoices;
    public $line_invoice_tax;
    public $productTransfers;
    public $companies;
    public $controllerHeadquarters;
    public $products;

    public $manager;
    public $idsCompanies;

    public function __construct()
    {
        $this->customer = new Customer();
        $this->type_document = new TypeDocument();
        $this->invoices = new Invoice();
        $this->tableInvoices = new Invoice();
        $this->line_invoice = new LineInvoice();
        $this->line_invoice_tax = new LineInvoiceTax();
        $this->productTransfers = new Product_transfer();
        $this->controllerHeadquarters = new HeadquartersController();
        $this->companies = new Company();
        $this->products = new Product();
        $this->query = $this->invoices
            ->select('*,customers.name as customer_name, type_documents.name as type_documents_name, invoices.id as invoices_id')
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id');
    }

    public function index()
    {
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $data = [
            'customers.name as customer_name',
            'documents.id',
            'documents.created_at',
            'documents.companies_id',
            'invoices.uuid',
            'invoices.resolution',
            'invoices.prefix',
            'invoices.companies_id as companies_id',
            'document_status.name as status',
            'document_status.description as status_description',
            'document_status.color as color_status',
            'document_status.id as status_id',
            'companies.company as company_name',
            'documents.provider',
            'documents.uuid as status_uuid',
            'customers.id as customer_id',
            'companies.identification_number',
            'documents.zip',
            'associate_document.new_name',
            'associate_document.name',
            'invoices.id as invoices_id',
            'invoices.created_at as created_at_invoice',
            'invoices.type_documents_id as type_documents_id_invoices',
            'invoices.invoice_status_id as invoice_status_id_invoices',
            'type_documents.name as type_documents_name',
            'invoices.payable_amount'
        ];
        $model = new Invoice();
        $invoices = $model
            ->select($data)
            ->join('documents', 'invoices.id = documents.invoice_id', 'left')
            ->join('document_status', 'document_status.id = documents.document_status_id', 'left')
            ->join('associate_document', 'associate_document.documents_id = documents.id', 'left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('companies', 'companies.id = invoices.companies_id', 'left');
            if($manager){
                $invoices->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
            }else{
                $invoices->where(['invoices.companies_id' => Auth::querys()->companies_id]);
            }
            // ['101', '102', '103', '104', '107', '108', '115', '116']
            $invoices->whereIn('invoices.type_documents_id', ['107', '108', '115', '116'])
            ->orderBy('invoices.id', 'desc')
            ->asObject();
        $model = new Company();
        $company = $model->asObject()->find(Auth::querys()->companies_id);

        $data = [
            'company' => $company,
            'documents' => $invoices->paginate(10),
            'pager' => $invoices->pager,
            'manager' => $manager
        ];
        return view('inventory/index', $data);
    }

    public function tableIndex()
    {
        $model = new Company();
        $company = $model->asObject()->find(Auth::querys()->companies_id);
        $data = [
            'customers.name as customer_name',
            'documents.id',
            'documents.created_at',
            'documents.companies_id',
            'invoices.uuid',
            'invoices.resolution',
            'invoices.prefix',
            'invoices.companies_id as companies_id',
            'document_status.name as status',
            'document_status.description as status_description',
            'document_status.color as color_status',
            'document_status.id as status_id',
            'companies.company as company_name',
            'documents.provider',
            'documents.uuid as status_uuid',
            'customers.id as customer_id',
            'companies.identification_number',
            'documents.zip',
            'associate_document.new_name',
            'associate_document.name',
            'invoices.id as invoices_id',
            'invoices.created_at as created_at_invoice',
            'invoices.type_documents_id as type_documents_id_invoices',
            'invoices.invoice_status_id as invoice_status_id_invoices',
            'type_documents.name as type_documents_name'
        ];
        $invoices = $this->tableInvoices
            ->select($data)
            ->join('documents', 'invoices.id = documents.invoice_id', 'left')
            ->join('document_status', 'document_status.id = documents.document_status_id', 'left')
            ->join('associate_document', 'associate_document.documents_id = documents.id', 'left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('companies', 'companies.id = documents.companies_id', 'left')
            ->where(['invoices.companies_id' => Auth::querys()->companies_id])
            ->whereIn('invoices.type_documents_id', ['101', '102', '103', '104', '107', '108', '115', '116'])
            ->orderBy('invoices.id', 'desc')
            ->asObject()->get()->getResult();

        $invoicesInput = [];
        $inventories = array_merge($invoices, $invoicesInput);
        foreach ($inventories as $key => $inventory) {
            if ($inventory->type_documents_id_invoices == 107 || $inventory->type_documents_id_invoices == 108 || $inventory->type_documents_id_invoices == 115 || $inventory->type_documents_id_invoices == 116) {
                $date = strtotime($inventory->created_at_invoice);
                $inventory->tableDate = date('Y-m-d', $date);
                $inventory->tableStatus = '<span class="new badge tooltipped purple" data-position="top" data-badge-caption="" data-tooltip="Guardado">Guardado</span>';
                $inventory->tableActions = '<div class="group">
                                                <a href="' . base_url() . '/inventory/edit/' . $inventory->invoices_id . '" class="btn btn-small yellow darken-2 send tooltipped step-4 next-tour" style="padding:0px 10px;" data-position="top" data-tooltip="Editar Remisión"><i class="material-icons">create</i></a></div>';
                if ($inventory->type_documents_id_invoices == 115 || $inventory->type_documents_id_invoices == 116) {
                    $inventory->tableActions = '<div class="group">
                                                <a href="' . base_url() . '/inventory/edit_out_transfer/' . $inventory->invoices_id . '" class="btn btn-small yellow darken-2 send tooltipped step-4 next-tour" style="padding:0px 10px;" data-position="top" data-tooltip="Editar transferencia"><i class="material-icons">create</i></a></div>';
                    if ($inventory->invoice_status_id_invoices == 22) {
                        $inventory->tableStatus = '<span class="new badge tooltipped yellow darken-2" data-position="top" data-badge-caption="" data-tooltip="Pendiente">Pendiente</span>';
                    } elseif ($inventory->invoice_status_id_invoices == 20) {
                        $inventory->tableStatus = '<span class="new badge tooltipped red" data-position="top" data-badge-caption="" data-tooltip="Rechazado">Rechazadodo</span>';
                    } elseif ($inventory->invoice_status_id_invoices == 21) {
                        $inventory->tableStatus = '<span class="new badge tooltipped green" data-position="top" data-badge-caption="" data-tooltip="Finalizado">Finalizado</span>';
                        //$inventory->tableActions = '<a href="" class="btn btn-small btn-light-blue-grey tooltipped step-8" download="" style="padding:0px 10px;" data-position="top" data-tooltip="Descargar documento"><i class="material-icons">cloud_download</i></a>';
                    }
                }
                $inventory->tableClient = $company->company;
                $inventory->tableProvider = $inventory->customer_name;
                $inventory->tableUuid = 'Sin Cufe';

            } else {
                $date = strtotime($inventory->created_at);
                $inventory->tableDate = date('Y-m-d', $date);
                $inventory->tableStatus = '<span class="new badge tooltipped ' . $inventory->color_status . '" data-position="top" data-badge-caption="" data-tooltip="' . $inventory->status_description . '">' . $inventory->status . '</span>';
                if ($inventory->status_id != 1) {
                    if (empty($inventory->provider)) {
                        $inventory->tableClient = '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="El proveedor no concuerda."><i class="material-icons small text-red red-text breadcrumbs-title" >brightness_1</i></span>' . $inventory->provider;
                    } else {
                        $inventory->tableClient = '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="Proveedor Ok"><i class="material-icons small text-green green-text" >brightness_1</i></span>' . $inventory->company_name;
                    }
                }
                if (isset($inventory->customer_id)) {
                    $errors = validationRowsNull($inventory->customer_id);
                    if (count($errors) > 0) {
                        $text = '';
                        foreach ($errors as $error) {
                            $text .= $error . '<br>';
                        }
                        $inventory->tableProvider = '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="' . $text . '"><i class="material-icons small text-yellow yellow-text"  >brightness_1</i></span> ' . $inventory->customer_name;
                    } else {
                        $inventory->tableProvider = '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="Cliente Ok"><i class="material-icons small text-green green-text" >brightness_1</i></span> ' . $inventory->customer_name;
                    }
                }
                if (!empty($inventory->uuid)) {
                    if ($inventory->status_uuid == 'false') {
                        $inventory->tableUuid = '<span class="tooltipped"  data-position="top"   data-tooltip="Factura Invalida"><i class="material-icons small text-red red-text" >brightness_1</i></span> ' . $inventory->uuid;
                    } else {
                        $inventory->tableUuid = '<span class="tooltipped"  data-position="top" data-badge-caption=""  data-tooltip="CUFE o CUDE Ok"><i class="material-icons small text-green green-text">brightness_1</i></span> ' . $inventory->uuid;
                    }
                }
                if (isset($document->new_name) && !empty($document->new_name)) {
                    $url = getenv("API") . "/download/" . $inventory->identification_number . "/" . $inventory->new_name;
                } else {
                    $url = "https://catalogo-vpfe.dian.gov.co/document/searchqr?documentKey=" . $inventory->uuid;
                }
                $inventory->tableActions = '<div class="btn-group z-depth-1">';
                if ($inventory->status_id == 1) {
                    $inventory->tableActions .= ' <a href="' . base_url("/documents/validations/" . $inventory->id) . '" class="btn btn-small yellow darken-2 send tooltipped step-4 next-tour" style="padding:0px 10px;" data-position="top"
                                                data-tooltip="Validar factura"><i class="material-icons">file_upload</i></a>';
                }
                if ($inventory->status_id == 2) {
                    $inventory->tableActions .= ' <a href="' . base_url("/documents/products/" . $inventory->id) . '" class="btn btn-small green tooltipped up-inventory step-5 next-tour" style="padding:0px 10px;" data-position="top"
                                                data-tooltip="Subir al inventario"><i class="material-icons">assignment</i></a>';
                }
                if ($inventory->status_id != 1) {
                    $inventory->tableActions .= '<a href="' . $url . '" class="btn btn-small btn-light-blue-grey tooltipped step-8" download="' . $inventory->name . '" style="padding:0px 10px;" data-position="top" data-tooltip="Descargar Factura"><i class="material-icons">cloud_download</i></a>';
                }
                if ($inventory->status_id != 1) {
                    $inventory->tableActions .= '<a href="' . base_url('documents/payment/' . $inventory->invoices_id) . '" class="btn btn-small purple darken-2 tooltipped step-8 payment_upload" data-document_id="' . $inventory->invoices_id . '" style="padding:0px 10px;" data-position="top" data-tooltip="Subir Pago" data-target="modal2"><i class="material-icons">receipt</i></a>';
                }
                $inventory->tableActions .= '<a href="' . base_url('/documents/delete/' . $inventory->id) . '" class="btn btn-small  red darken-2 tooltipped step-8" style="padding:0px 10px;" data-position="top" data-tooltip="Eliminar factura"><i class="material-icons">delete</i></a>';

                $inventory->tableActions .= '</div>';
            }
            $inventory->tableDocumentName = $inventory->type_documents_name ?? '';
            $inventory->tableDocument = $inventory->prefix . $inventory->resolution;
        }
        return json_encode($inventories);
    }

    public function create()
    {
        return view('inventory/create');
    }

    public function out_create()
    {
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        return view('inventory/create_out', ['manager' => $manager]);
    }

    public function edit($id = null)
    {
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        return view('inventory/edit', ['id' => $id, 'manager' => $manager]);
    }

    public function reports()
    {
        $providers = $this->customer
            ->where(['companies_id' => Auth::querys()->companies_id, 'type_customer_id' => 2])->get()
            ->getResult();
        $types_documents = $this->type_document->whereIn('id', [1, 2, 3, 4, 5, 102, 101, 103, 104, 107, 108])->get()->getResult();

        echo view('inventory/inventory_report', ['providers' => $providers, 'type_documents' => $types_documents]);
    }

    public function result()
    {
        $date_init = ($_POST['date_init'] ?? '');
        $date_end = ($_POST['date_end'] ?? '');
        $provider = ($_POST['providers'] ?? '');
        $operation = ($_POST['operation'] ?? '');

        $where = [
            'invoices.companies_id' => Auth::querys()->companies_id
        ];
        if (!empty($date_init)) {
            $where['invoices.issue_date >='] = $date_init;
        }
        if (!empty($date_end)) {
            $where['invoices.issue_date <='] = $date_end;
        }
        if (!empty($provider)) {
            $where['invoices.customers_id'] = $provider;
        }
        if (!empty($operation)) {
            $where['invoices.type_documents_id'] = $operation;
        }
        if (empty($operation)) {
            $data = $this->query->where($where)->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5, 102, 101, 104, 103, 107, 108]);
        } else {
            $data = $this->query->where($where);
        }

        $data_send = [
            'vista' => 'result',
            'data' => $data->paginate(10),
            'pager' => $data->pager
        ];
        $data_send['quantity'] = $this->quantities($data_send['data']);
        //echo json_encode($data_send);die();
        echo view('inventory/result_report', $data_send);
    }

    public function details($id)
    {
        $descuentos = 0;
        $invoice = $this->query->where(['invoices.id' => $id])->get()->getResult();
        $data = $this->line_invoice
            ->select('*,line_invoices.id as id_line')
            ->join('products', 'products.id = line_invoices.products_id')
            ->where(['line_invoices.invoices_id' => $id])->get()->getResult();

        foreach ($data as $line_invoice) {
            $retencion = $this->line_invoice_tax->selectSum('tax_amount')
                ->where(['line_invoices_id' => $line_invoice->id_line, 'taxes_id >=' => 5])->get()->getResult();
            $line_invoice->retenciones = $retencion[0]->tax_amount;
            $iva = $this->line_invoice_tax->selectSum('tax_amount')
                ->where(['line_invoices_id' => $line_invoice->id_line, 'taxes_id' => 1])->get()->getResult();
            $line_invoice->iva_product = $iva[0]->tax_amount;
        }
        $data_send = [
            'invoice' => $invoice,
            'data' => $data,
            'vista' => 'details',
        ];
        //echo json_encode($data);die();
        echo view('inventory/result_report', $data_send);
    }

    public function quantities($data)
    {
        $quantities = [];
        $quantity = 0;
        foreach ($data as $total) {
            $quantity = $this->line_invoice->selectSum('quantity')->where(['invoices_id' => $total['invoices_id']])->get()->getResult();
            array_push($quantities, $quantity[0]->quantity);
        }
        return $quantities;
    }


    /**
     * Table availability
     * method GET
     * @return string view
     */
    public function availability()
    {
        $product = new Product();
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $countIds = count($this->controllerHeadquarters->idsCompaniesHeadquarters());
        $idsCompanies = '';
        foreach ($this->controllerHeadquarters->idsCompaniesHeadquarters() as $id => $item) {
            if ($id == 0) {
                $idsCompanies = $item;
            } else {
                $idsCompanies = $idsCompanies . ',' . $item;
            }
        }
        if(!$manager){
            $idsCompanies = Auth::querys()->companies_id;
        }
        if(isset($_GET['headquarter'])){
            $manager = false;
            $idsCompanies = $_GET['headquarter'];
        }
        $indicadores = [];
        $indicators = $this->totalIndicatorsInventory(($manager)?$this->controllerHeadquarters->idsCompaniesHeadquarters():[$idsCompanies]);
        array_push($indicadores, (object)[
            'id' => 'costo',
            'color' => 'green',
            'icon' => 'verified_user',
            'name' => 'Valor Costo',
            'observaciones' => '',
            'total' => $indicators->input
        ]);
        array_push($indicadores, (object)[
            'id' => 'venta',
            'color' => 'red',
            'icon' => 'trending_down',
            'name' => 'Valor Venta',
            'observaciones' => '',
            'total' => $indicators->output
        ]);
        $products = $product->select([
            'products.id',
            'products.name',
            'products.tax_iva',
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = products.id and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (101, 102, 4, 104,  107)
            GROUP BY  prod2.id), 0) AS input',
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = products.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (116)
            GROUP BY  prod2.id), 0) AS inputTransfer',
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id
            WHERE prod2.id = products.id and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (1, 2, 5, 103, 108)
            GROUP BY  prod2.id), 0) AS output',
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id
            WHERE prod2.id = products.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (115)
            GROUP BY  prod2.id), 0) AS outputTransfer',
            //     'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            // LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            // LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            // WHERE prod2.id = products.id and invoices.companies_id IN (' . $idsCompanies . ')  and  invoices.type_documents_id IN (101, 102, 4, 104, 107)
            // GROUP BY  prod2.id), 0) AS availability_input',
            //     'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            // LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            // LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            // WHERE prod2.id = products.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and  invoices.type_documents_id IN (116)
            // GROUP BY  prod2.id), 0) AS availability_input_transfer',
            //     'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            // LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            // LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            // WHERE prod2.id = products.id and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (1, 2, 5, 103, 108)
            // GROUP BY  prod2.id), 0) AS availability_output',
            //     'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            // LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            // LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            // WHERE prod2.id = products.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (115)
            // GROUP BY  prod2.id), 0) AS availability_output_transfer'
        ])
            ->join('line_invoices', 'line_invoices.products_id = products.id')
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5, 102, 101, 103, 104, 107, 108, 115, 116])
            ->where('products.tax_iva !=', null);
        if ($this->request->getGet('code')) {
            $products->like('products.code', $this->request->getGet('code'), 'both');
        }
        if ($this->request->getGet('name')) {
            $products->like('products.name', $this->request->getGet('name'),'both');
        }
        if ($manager) {
            $products->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
            //$products->whereIn('prod.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $products->where(['invoices.companies_id' => $idsCompanies]);
            //$products->where(['prod.companies_id' => Auth::querys()->companies_id]);
        }
        
        if ($this->request->getGet('orderBy')) {
            switch ($this->request->getGet('orderBy')) {
                case 'entry':
                    $products->orderBy('(input + inputTransfer)', $this->request->getGet('DESC'));
                    break;
                case 'sales':
                    $products->orderBy('(output + outputTransfer)', $this->request->getGet('DESC'));
                    break;
                default:
                    $products->orderBy('((input + inputTransfer) - (output + outputTransfer))', $this->request->getGet('DESC'));
                    break;
            }
        }
        
        $products->groupBy('products.id') 
            ->asObject();

        //echo json_encode($product->get()->getResult());die();

        $headquarters = $this->companies
            ->select('companies.id, companies.company')
            ->whereIn('id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->where(['id !=' => 1])
            ->asObject()->get()->getResult();
        $productsTransfer = $this->products
            ->where(['companies_id' => Auth::querys()->companies_id])
            ->asObject()->get()->getResult();
        $productos = $products->paginate();

        // Función de comparación personalizada

        // var_dump($productos); die();
        return view('inventory/availability', [
            'products' => $productos,
            'pager' => $products->pager,
            'headquarters' => $headquarters,
            'productsTransfer' => $productsTransfer,
            'manager' => $this->controllerHeadquarters->permissionManager(session('user')->role_id),
            'indicadores' => $indicadores
        ]);
    }

    public function kardex($id = null)
    {
        $model = new Product();
        $product = $model->asObject()->find($id);
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        return view('inventory/kardex', ['product' => $product, 'manager' => $manager]);
    }

    public function kardexTable($id = null)
    {
        $manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $idCompany = Auth::querys()->companies_id;
        if(isset($_GET['headquarter']) && $_GET['headquarter'] != 0){
            $manager = false;
            $idCompany = $_GET['headquarter'];
        }
        // other documents
        $dataProducts = $this->products->select('
            invoices.resolution, 
            customers.name as customer_name,
            line_invoices.quantity, 
            invoices.created_at,
            invoices.type_documents_id,
            type_documents.name as type_document_name,
            products.name,
            companies.company as company_name,
            documents.provider,
            users.name as userName
            ')
            ->join('line_invoices', 'line_invoices.products_id = products.id')
            ->join('invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('documents', 'documents.invoice_id = invoices.id', 'left')
            ->join('companies', 'companies.id = invoices.companies_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('users', 'invoices.user_id = users.id', 'left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
            ->whereIn('invoices.type_documents_id', [1, 2, 4, 5, 102, 101, 103, 104, 107, 108]);
        if ($manager) {
            $dataProducts->where(['products.id' => $id])->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $dataProducts->where(['invoices.companies_id' => $idCompany, 'products.id' => $id]);
        }
        $dataProducts->orderBy('invoices.created_at', 'ASC')->asObject();
        $products = $dataProducts->get()->getResult();
        //echo json_encode($_GET['headquarter']);die();
        // transfer documents
        $dataTransfer = $this->products->select('
            invoices.resolution, 
            customers.name as customer_name,
            line_invoices.quantity, 
            invoices.created_at,
            invoices.type_documents_id,
            type_documents.name as type_document_name,
            products.name,
            companies.company as company_name,
            documents.provider,
            users.name as userName
            ')
            ->join('line_invoices', 'line_invoices.products_id = products.id')
            ->join('invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('documents', 'documents.invoice_id = invoices.id', 'left')
            ->join('companies', 'companies.id = invoices.companies_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('users', 'invoices.user_id = users.id', 'left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
            ->whereIn('invoices.type_documents_id', [115, 116]);
        if ($manager) {
            $dataTransfer->where(['products.id' => $id, 'invoices.invoice_status_id' => 21])->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $dataTransfer->where(['invoices.companies_id' => $idCompany, 'products.id' => $id, 'invoices.invoice_status_id' => 21]);
        }
        $dataTransfer->orderBy('invoices.created_at', 'ASC')->asObject();
        $transfer = $dataTransfer->get()->getResult();
        $aux = [];
        $kardex = array_merge($products, $transfer);
        foreach ($kardex as $key => $row) {
            $aux[$key] = $row->created_at;
        }
        array_multisort($aux, SORT_ASC, $kardex);
        $balance = 0;
        $date = [];
        foreach ($kardex as $item) {
            $item->input = 0;
            $item->out = 0;
            if ($item->type_documents_id == 107 || $item->type_documents_id == 108 || $item->type_documents_id == 115 || $item->type_documents_id == 116) {
                $item->source = $item->company_name;
            } else {
                if (empty($item->provider) &&  !is_null($item->provider)) {
                    $item->source = $item->provider;
                } else {
                    $item->source = $item->company_name;
                }
            }
            $item->destination = $item->customer_name;
            if($item->type_documents_id == 101 || $item->type_documents_id == 102 || $item->type_documents_id == 4 || $item->type_documents_id == 104 || $item->type_documents_id == 107){
                $item->destination = $item->company_name;
                $item->source = 'Sede Proveedores';
            }else{
                $item->source = $item->company_name;
                $item->destination = 'Sede Clientes';
            }
            if($item->type_documents_id == 115){
                $item->destination = $item->customer_name;
                $item->source = $item->company_name;
            }else if($item->type_documents_id == 116){
                $item->source = $item->customer_name;
                $item->destination = $item->company_name;
            }
            if ($item->type_documents_id == 101 || $item->type_documents_id == 102 || $item->type_documents_id == 4 || $item->type_documents_id == 104 || $item->type_documents_id == 107 || $item->type_documents_id == 116) {
                $balance += $item->quantity;
                $item->input = $item->quantity;
            } else {
                $balance -= $item->quantity;
                $item->out = $item->quantity;
            }
            if($item->type_documents_id == 115 || $item->type_documents_id == 116){
                $item->customerOrProvider = $item->userName;
            }else if($item->type_documents_id == 101 || $item->type_documents_id == 102 || $item->type_documents_id == 4 || $item->type_documents_id == 104 || $item->type_documents_id == 107){
                $item->customerOrProvider = $item->customer_name;
            }else{
                $item->customerOrProvider = $item->customer_name;
            }
            array_push($date, $balance);
            $item->balance = $balance;
        }
        //echo json_encode($kardex);die();
        return json_encode(array_reverse($kardex));
    }

    public function out_transfer()
    {
        return view('inventory/create_transfer');
    }

    public function edit_out_transfer($id = null)
    {
        //echo json_encode($id);die();
        return view('inventory/edit_transfer', ['id' => $id]);
    }

    public function transferProduct()
    {
        try {
            $productTranfer = $this->products->where(['id' => $_POST['productId'], 'companies_id' => Auth::querys()->companies_id])->asObject()->first();
            $productReceive = $this->products->where(['companies_id' => $_POST['headquarterId'], 'code' => $productTranfer->code])->asObject()->first();
            $transfer = [
                'companies_id' => Auth::querys()->companies_id,
                'product_id' => $_POST['productId'],
                'quantity' => $_POST['quantity'],
                'destination_headquarters' => $_POST['headquarterId'],
                'user_id' => Auth::querys()->id,
                'type_document_id' => 115
            ];
            //echo json_encode($productTranfer);die();
            if (!is_null($productReceive)) {
                $transfer['destination_product_id'] = $productReceive->id;
            } else {
                $product = [
                    'name' => $productTranfer->name,
                    'code' => $productTranfer->code,
                    'valor' => $productTranfer->valor,
                    'cost' => $productTranfer->cost,
                    'description' => $productTranfer->description,
                    'unit_measures_id' => $productTranfer->unit_measures_id,
                    'type_item_identifications_id' => $productTranfer->type_item_identifications_id,
                    'reference_prices_id' => 1,
                    'free_of_charge_indicator' => $productTranfer->free_of_charge_indicator,
                    'companies_id' => $_POST['headquarterId'],
                    'entry_credit' => $productTranfer->entry_credit,
                    'entry_debit' => $productTranfer->entry_debit,
                    'iva' => $productTranfer->iva,
                    'retefuente' => $productTranfer->retefuente,
                    'reteica' => $productTranfer->reteica,
                    'reteiva' => $productTranfer->reteiva,
                    'account_pay' => $productTranfer->account_pay,
                    'brandname' => $productTranfer->brandname,
                    'modelname' => $productTranfer->modelname,
                    'foto' => $productTranfer->foto,
                    'category_id' => $productTranfer->category_id,
                ];
                $saveProductNew = $this->products->insert($product);
                if (isset($saveProductNew)) {
                    $transfer['destination_product_id'] = $saveProductNew;
                } else {
                    throw  new \Exception('No se puedo crear nuevo producto para realizar la transferencia');
                }
            }
            if ($this->productTransfers->save($transfer)) {
                return redirect()->to(base_url() . route_to('inventory-availability'))->with('success', 'Se realizo con exíto la transferencia de cantidades');
            } else {
                throw  new \Exception('No se pudo realizar la transferencia de cantidades');
            }
        } catch (\Exception $e) {
            return redirect()->to(base_url() . route_to('inventory-availability'))->with('error', $e->getMessage());
        }
    }

    public function availabilityProduct($idProduct, $idCompany)
    {
        $product = new Product();
        $manager = $this->controllerHeadquarters->permissionManager(Auth::querys()->role_id);
        $countIds = count($this->controllerHeadquarters->idsCompaniesHeadquarters($idCompany));
        $idsCompanies = '';
        foreach ($this->controllerHeadquarters->idsCompaniesHeadquarters($idCompany) as $id => $item) {
            if ($id < ($countIds--)) {
                $idsCompanies = $idsCompanies . '' . $item . ',';
            } else {
                $idsCompanies = $idsCompanies . '' . $item;
            }
        }
        $products = $product->select([
            'prod.id',
            'prod.name', ($manager) ?
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (101, 102, 4, 104,  107)
            GROUP BY  prod2.id), 0) AS input' :
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.companies_id = ' . Auth::querys()->companies_id . '  and invoices.type_documents_id IN (101, 102, 4, 104,  107)
            GROUP BY  prod2.id), 0) AS input', ($manager) ?
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (116)
            GROUP BY  prod2.id), 0) AS inputTransfer' :
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id = ' . Auth::querys()->companies_id . '  and invoices.type_documents_id IN (116)
            GROUP BY  prod2.id), 0) AS inputTransfer', ($manager) ?
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id
            WHERE prod2.id = prod.id and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (1, 2, 5, 103, 108)
            GROUP BY  prod2.id), 0) AS output' :
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id
            WHERE prod2.id = prod.id and invoices.companies_id = ' . Auth::querys()->companies_id . '  and invoices.type_documents_id IN (1, 2, 5, 103, 108)
            GROUP BY  prod2.id), 0) AS output', ($manager) ?
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (115)
            GROUP BY  prod2.id), 0) AS outputTransfer' :
                'IFNULL((SELECT SUM(line2.quantity) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id = ' . Auth::querys()->companies_id . '  and invoices.type_documents_id IN (115)
            GROUP BY  prod2.id), 0) AS outputTransfer', ($manager) ?
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.companies_id IN (' . $idsCompanies . ')  and  invoices.type_documents_id IN (101, 102, 4, 104, 107)
            GROUP BY  prod2.id), 0) AS availability_input' :
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.companies_id = ' . Auth::querys()->companies_id . '  and  invoices.type_documents_id IN (101, 102, 4, 104, 107)
            GROUP BY  prod2.id), 0) AS availability_input', ($manager) ?
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and  invoices.type_documents_id IN (116)
            GROUP BY  prod2.id), 0) AS availability_input_transfer' :
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id = ' . Auth::querys()->companies_id . '  and  invoices.type_documents_id IN (116)
            GROUP BY  prod2.id), 0) AS availability_input_transfer', ($manager) ?
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (1, 2, 5, 103, 108)
            GROUP BY  prod2.id), 0) AS availability_output' :
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.companies_id = ' . Auth::querys()->companies_id . '  and invoices.type_documents_id IN (1, 2, 5, 103, 108)
            GROUP BY  prod2.id), 0) AS availability_output', ($manager) ?
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id IN (' . $idsCompanies . ')  and invoices.type_documents_id IN (115)
            GROUP BY  prod2.id), 0) AS availability_output_transfer' :
                'IFNULL((SELECT SUM(line2.price_amount) FROM products AS prod2
            LEFT JOIN line_invoices AS line2 ON prod2.id = line2.products_id  
            LEFT JOIN invoices ON invoices.id = line2.invoices_id  
            WHERE prod2.id = prod.id and invoices.invoice_status_id = 21 and invoices.companies_id = ' . Auth::querys()->companies_id . '  and invoices.type_documents_id IN (115)
            GROUP BY  prod2.id), 0) AS availability_output_transfer'
        ])->from('products AS prod')
            ->join('line_invoices', 'line_invoices.products_id = products.id')
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->where(['prod.id' => $idProduct])
            ->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5, 102, 101, 103, 104, 107, 108, 115, 116]);
        if ($manager) {
            $products->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters($idCompany));
        } else {
            $products->where(['invoices.companies_id' => Auth::querys()->companies_id]);
        }
        $products->groupBy('prod.id')
            ->asObject();
        $total = $products->first();
        if(is_null($total)){
            return 0;
        }else{
            return ($total->input + $total->inputTransfer) - ($total->output + $total->outputTransfer);
        }
    }

    private function totalIndicatorsInventory($idsCompanies){
        $input = 0;
        $output = 0;
        $invoice = new Invoice();
        $invoices = $invoice->select([
            'line_invoices.quantity',
            'products.valor',
            'products.cost',
            'invoices.type_documents_id'
        ])->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'line_invoices.products_id = products.id')
            ->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5, 102, 101, 103, 104, 107, 108, 115, 116]);

        $invoices->whereIn('invoices.companies_id', $idsCompanies);
        $invoices->asObject();
        $totals = $invoices->get()->getResult();
        $idInputs = [101, 102, 4, 104,  107, 116];
        $idOutPuts = [1, 2, 5, 103, 108,115];
        foreach($totals as $total){
            if(in_array($total->type_documents_id, $idInputs)){
                $input = $input + ($total->quantity * $total->cost);
                $output = $output + ($total->quantity * $total->valor);
            }else{
                $input = $input - ($total->quantity * $total->cost);
                $output = $output - ($total->quantity * $total->valor);
            }
        }
        return (object)[
            'input' => $input,
            'output' => $output
        ];
    }

    public function cartera(){
        var_dump(session('module')); die();
        $this->activeUser();
        $indicadores = [];
        $paymentMethodCompanys = new AccountingAcount();
        $paymentMethod = $paymentMethodCompanys
            ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->where(['type_accounting_account_id' => 5])
            ->asObject()
            ->get()
            ->getResult();

        $model = new Resolution();
        $resolutions = $model
            ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->where(['type_documents_id' => 1])
            ->get()
            ->getResult();
        $customer = new Customer();

            $customers = $customer
                ->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
                ->where(['type_customer_id' => 1])
                ->orderBy('name', 'asc')
                ->get()
                ->getResult();

        //sedes
        $companies = new Company();
        $headquarters = $companies->whereIn('id',$this->controllerHeadquarters->idsCompaniesHeadquarters())->where(['id !=' => 1])->asObject()->get()->getResult();


        $wallet = new Invoice();
        $total = $wallet->select([
            'SUM(invoices.payable_amount) as payable_amount',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id  GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) GROUP BY line_invoices.invoices_id) AS withholdings'
        ]);
        if ($this->manager) {
            $total->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $total->where('invoices.companies_id', Auth::querys()->companies_id);
        }

        $total->whereIn('invoices.type_documents_id', [1, 2, 5, 108])
            ->whereIn('invoices.invoice_status_id', [2, 3, 4])
            ->where('invoices.deleted_at', null);

        $this->extracted($total);

        $total->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id')
            ->asObject();

        //indicadores
        array_push($indicadores, (object)[
            'id' => 'adeudado',
            'color' => 'red',
            'icon' => 'trending_down',
            'name' => 'Adeudado',
            'total' => 0
        ]);
        array_push($indicadores, (object)[
            'id' => 'recaudado',
            'color' => 'green',
            'icon' => 'verified_user',
            'name' => 'Recaudado',
            'total' => 0
        ]);

        $model = new Invoice();
        $select = $this->dataSearch($this->manager, $this->idsCompanies);
        array_push($select, 'companies.company as nameCompany');
        $data = $model->select($select)
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [107, 108]);
            // ->whereIn('invoices.invoice_status_id', [2, 3, 4]);
        if ($this->manager) {
            $data->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $data->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        $data->where('invoices.deleted_at', null)
            ->orderBy('invoices.created_at', 'DESC');


        $this->extracted($data);
        //echo json_encode($data->get()->getResult());die();
        return view('wallet/index', [
            'resolutions' => $resolutions,
            'wallets' => $data->asObject()->paginate(10),
            'pager' => $data->pager,
            'total' => $total->get()->getResult(),
            'paymentMethod' => $paymentMethod,
            'customers' => $customers,
            'headquarters' => $headquarters,
            'indicadores' => $indicadores
        ]);
    }

    public function dataSearch($manager, $idsCompanies): array
    {
        $dataSearch = [
            'invoices.companies_id',
            'invoices.payment_methods_id',
            'invoices.resolution',
            'invoices.created_at',
            'invoices.payable_amount',
            'invoices.status_wallet',
            'invoices.id',
            'customers.name',
            'invoices.type_documents_id',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id AND wallet.deleted_at IS  NULL   GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7)  AND invoices.deleted_at IS  NULL GROUP BY line_invoices.invoices_id) AS withholdings'
        ];
        if ($manager) {
            $dataNew = [
                '(SELECT IFNULL(SUM(line_invoice_taxs.tax_amount), 0) FROM invoices as inv2 
                INNER JOIN line_invoices ON line_invoices.invoices_id = inv2.id 
                INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id =  line_invoices.id
                WHERE inv2.type_documents_id = 4 
                AND inv2.companies_id IN (' . $idsCompanies . ')
                AND  inv2.resolution_credit =  invoices.resolution
                AND line_invoice_taxs.taxes_id IN (5,6,7) ) as credit_note_withholdings',
                '(SELECT IFNULL(SUM(inv2.payable_amount), 0) FROM invoices as inv2 WHERE inv2.companies_id IN(' . $idsCompanies . ') AND inv2.type_documents_id = 4 AND  inv2.resolution_credit =  invoices.resolution AND inv2.deleted_at IS  NULL LIMIT 1 ) as credit_note',

            ];
        } else {
            $dataNew = ['(SELECT IFNULL(SUM(line_invoice_taxs.tax_amount), 0) FROM invoices as inv2 
                INNER JOIN line_invoices ON line_invoices.invoices_id = inv2.id 
                INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id =  line_invoices.id
                WHERE inv2.type_documents_id = 4 
                AND  inv2.resolution_credit =  invoices.resolution
                AND inv2.companies_id = ' . $idsCompanies . ' 
                AND line_invoice_taxs.taxes_id IN (5,6,7) ) as credit_note_withholdings',
                '(SELECT IFNULL(SUM(inv2.payable_amount), 0) FROM invoices as inv2 WHERE inv2.type_documents_id = 4 AND  inv2.resolution_credit =  invoices.resolution and inv2.companies_id = ' . $idsCompanies . ' AND inv2.deleted_at IS  NULL ) as credit_note',
            ];
        }

        return array_merge($dataSearch, $dataNew);
    }

    protected function activeUser()
    {
        $this->manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $this->idsCompanies = $this->controllerHeadquarters->idsCompaniesText();
        if (!$this->manager) {
            $this->idsCompanies = Auth::querys()->companies_id;
        }
    }

    protected function extracted(Invoice $data): void
    {
        $this->request->getGet('start_date') ? $data->where('invoices.created_at >=', $this->request->getGet('start_date') . ' 00:00:00') : '';
        $this->request->getGet('end_date') ? $data->where('invoices.created_at <=', $this->request->getGet('end_date') . ' 00:00:00') : '';
        $this->request->getGet('resolution') ? $data->where('invoices.resolution', $this->request->getGet('resolution')) : '';
        $this->request->getGet('customers_id') ? $data->where('invoices.customers_id', $this->request->getGet('customers_id')) : '';
        $this->request->getGet('status') && $this->request->getGet('status') != 'Todos' ? $data->where('invoices.status_wallet', $this->request->getGet('status')) : ($this->request->getGet('status') == 'Todos' ? '' : $data->where('invoices.status_wallet', 'Pendiente'));
        $this->request->getGet('headquarters') ? $data->where('invoices.companies_id', $this->request->getGet('headquarters')) : '';
    }

}

