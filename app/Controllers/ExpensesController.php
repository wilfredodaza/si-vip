<?php


namespace App\Controllers;

use App\Controllers\Api\Auth;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\Product;

class ExpensesController extends BaseController
{
    public $tableInvoices;
    public $tableLineInvoices;
    public $controllerHeadquarters;
    public $tableCustomers;
    public $tableProducts;

    public function __construct()
    {
        $this->tableInvoices = new Invoice();
        $this->tableLineInvoices = new LineInvoice();
        $this->controllerHeadquarters = new HeadquartersController();
        $this->tableCustomers = new Customer();
        $this->tableProducts = new Product();
    }

    public function index()
    {
        //echo json_encode($this->request->getGet('product')); die();
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

        $customers = $this->customers();
        $products = $this->products();
        $querys = [];
        $productsQuery = [];


        if ($this->request->getGet('customer')) {
            $querys = array_merge($querys, ['invoices.customers_id' => $this->request->getGet('customer')]);
        }
        if ($this->request->getGet('start_date')) {
            $querys = array_merge($querys, ['line_invoices.start_date >=' => $this->request->getGet('start_date')]);
        }
        if ($this->request->getGet('end_date')) {
            $querys = array_merge($querys, ['line_invoices.start_date <=' => $this->request->getGet('end_date')]);
        }

        $invoices = $this->tableLineInvoices
            ->select([
                'invoices.created_at',
                'invoices.id',
                'customers.name as  customer',
                'payable_amount as total',
                'invoice_status.name as status',
                'invoices.invoice_status_id',
                'invoices.resolution as resolution',
                'invoices.prefix',
                'invoices.type_documents_id',
                'type_documents.name as type_document_name',
                'products.name as product_name',
                'line_invoices.start_date',
                'line_invoices.line_extension_amount'
            ])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('customers', 'customers.id = invoices.customers_id', 'left')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id', 'left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->join('products', ' products.id = line_invoices.products_id', 'left')
            ->where(['invoices.type_documents_id' => 118])
            ->where($querys);
        // var_dump($invoices->get()->getResult());die();

        if ($this->request->getGet('product') && $this->request->getGet('product') != 0) {
            $invoices->whereIn('line_invoices.products_id', $this->request->getGet('product'));
        }
        if ($manager) {
            $invoices->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $invoices->where(['invoices.companies_id' => Auth::querys()->companies_id]);
        }
        $invoices->orderBy('invoices.created_at', 'DESC')->asObject();
        return view('expenses/index', [
            'invoices' => $invoices->paginate(),
            'pager' => $invoices->pager,
            'customers' => $customers,
            'products' => $products
        ]);
    }

    public function create()
    {
        return view('expenses/create');
    }

    public function edit(string $id = null)
    {
        return view('expenses/edit', ['id' => $id]);
    }

    private function customers(): array
    {
        return $this->tableCustomers
            ->select([
                'id',
                'name',
            ])
            ->where([
                'companies_id' => Auth::querys()->companies_id,
                'type_customer_id' => 2,
                'status' => 'Activo'
            ])
            //->whereIN('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->asObject()
            ->findAll();
    }

    private function products(): array
    {
        return $this->tableProducts
            ->where(['kind_product_id' => 3])
            ->whereIN('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->asObject()
            ->get()
            ->getResult();

    }

}
