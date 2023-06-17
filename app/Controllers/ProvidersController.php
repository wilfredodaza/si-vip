<?php

namespace App\Controllers;

use App\Models\Customer;
use App\Models\CustomerWorker;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\Notification;
use App\Models\PaymentPolicies;
use App\Models\TrackingCustomer;
use App\Models\User;
use App\Models\InvoiceDocumentUpload;
use DateInterval;
use DatePeriod;
use DateTime;


class ProvidersController extends BaseController
{
    public $tableCustomers;
    public $tableInvoices;
    public $tablePaymentPolicies;
    public $tableLineInvoices;
    public $controllerWallet;
    public $tableCustomerWorker;
    public $controllerTracking;

    public function __construct()
    {
        $this->tableCustomers = new Customer();
        $this->tableCustomerWorker = new CustomerWorker();
        $this->tableInvoices = new Invoice();
        $this->tableLineInvoices = new LineInvoice();
        $this->tablePaymentPolicies = new PaymentPolicies();
        $this->controllerWallet = new WalletController();
        $this->controllerTracking = new TrackingController();
    }

    public function profile($id)
    {
        //echo json_encode($id);die();
        $customer = $this->tableCustomers
            ->select([
                'customers.id',
                'customers.name',
                'customers.identification_number as identification',
                'type_document_identifications.name as type_identification',
                'customers.phone',
                'customers.email',
                'customers.address',
                'customers.quota',
                'customers.payment_policies',
                'customer_worker.surname',
                'customers.type_client_status',
                'customers.frequency'
            ])
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('customer_worker', 'customers.id = customer_worker.customer_id', 'left')
            ->where('customers.id', $id)->asObject()->first();
        $invoices = new Invoice();
        $querys = [];
        $querysc = [];
        $lastShoppingTotal = 0;
        $lastProductsShoppingTotal = 0;
        if ($this->request->getGet('option') == 'c') {
            if ($this->request->getGet('start_date')) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }
        if ($this->request->getGet('option') == 'p') {
            if ($this->request->getGet('start_date')) {
                $querysc = array_merge($querysc, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querysc = array_merge($querysc, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }
        $lastShopping = $invoices
            ->select([
                'invoices.payable_amount as total',
            ])
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->where(['invoices.customers_id' => $id])
            ->where($querys)
            ->whereIn('invoices.type_documents_id', [107])
            //->whereIn('invoices.invoice_status_id', [2, 3])
            ->orderBy('invoices.created_at', 'DESC')
            ->get()->getResult();
        foreach ($lastShopping as $item) {
            $lastShoppingTotal += $item->total;
        }
        $lineInvoices = new LineInvoice();
        $productsShopping = $lineInvoices
            ->select([
                'products.code as code',
                'products.name as nameProduct',
                'products.tax_iva as reference',
                'SUM(line_invoices.quantity) as tQuantity',
                'SUM(line_invoices.line_extension_amount) as total'
            ])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->where(['invoices.customers_id' => $id])
            ->whereIn('invoices.type_documents_id', [107])
            ->groupBy('line_invoices.products_id')
            ->orderBy('tQuantity', 'DESC')
            ->asObject()->get(10)->getResult();
        $lineInvoices = new LineInvoice();
        $lastProductsShopping = $lineInvoices
            ->select([
                'line_invoices.line_extension_amount as total'
            ])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->where(['invoices.customers_id' => $id])
            ->where($querysc)
            ->whereIn('invoices.type_documents_id', [107])
            ->orderBy('line_invoices.id', 'DESC')
            ->get()->getResult();
        foreach ($lastProductsShopping as $item) {
            $lastProductsShoppingTotal += $item->total;
        }
        return view('provider/profile', [
            'customer' => $customer,
            'paymentPolicies' => $this->tablePaymentPolicies->get()->getResult(),
            'lastShopping' => $lastShoppingTotal,
            'productsShopping' => $productsShopping,
            'lastProductsShopping' => $lastProductsShoppingTotal,
            'debt' => $this->controllerWallet->totalPerson($id)
        ]);

    }

    public function products($id)
    {
        $querys = [];

        if ($this->request->getGet('option') == 'p') {
            if ($this->request->getGet('start_date')) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }

        $lastProductsShopping = $this->tableLineInvoices
            ->select([
                'invoices.created_at as date',
                'products.name as nameProduct',
                'products.tax_iva as reference',
                'line_invoices.quantity as tQuantity',
                'line_invoices.line_extension_amount as total'
            ])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->where(['invoices.customers_id' => $id])
            ->where($querys)
            ->whereIn('invoices.type_documents_id', [107])
            ->orderBy('line_invoices.id', 'DESC')
            ->get()->getResult();
        foreach ($lastProductsShopping as $item) {
            $item->name = $item->nameProduct . '-' . $item->reference;
            $item->total = '$ ' . number_format($item->total, '2', ',', '.');
        }
        return json_encode($lastProductsShopping);
    }

    public function shopping($id)
    {
        $querys = [];
        if ($this->request->getGet('option') == 'c') {
            if ($this->request->getGet('start_date')) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('start_date')]);
            }
            if ($this->request->getGet('end_date')) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('end_date')]);
            }
        }
        $lastShopping = $this->tableInvoices
            ->select([
                'invoices.id as id',
                'invoices.created_at as date',
                'invoices.payable_amount as total',
                'type_documents.name as document',
            ])
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->where(['invoices.customers_id' => $id])
            ->where($querys)
            ->whereIn('invoices.type_documents_id', [107])
            //->whereIn('invoices.invoice_status_id', [2, 3])
            ->orderBy('invoices.created_at', 'DESC')
            ->get()->getResult();
        foreach ($lastShopping as $item) {
            $item->total = '$ ' . number_format($item->total, '2', ',', '.');
            $item->action = '<div class="btn-group" role="group">
                                                        <a href="' . base_url() . '/reports/view/' . $item->id. '"
                                                           class="btn btn-small  yellow darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                                                            <i class="material-icons">insert_drive_file</i>
                                                        </a>
                                                    </div>';
        }

        return json_encode($lastShopping);
    }

    public function updatePayment($id)
    {
        try {
            if ($this->tableCustomers->update($id,
                ['quota' => $this->request->getPost('quota'),
                    'payment_policies' => $this->request->getPost('payment_policies'),
                    'type_client_status' => $this->request->getPost('type_client_status'),
                    'name' => $this->request->getPost('name'),
                    'identification' => $this->request->getPost('identification'),
                    'address' => $this->request->getPost('address'),
                    'email' => $this->request->getPost('email'),
                    'phone' => $this->request->getPost('phone'),
                    'frequency' => $this->request->getPost('frequency'),
                ]
            )) {
                return redirect()->to(base_url('/providers/profile/' . $id))->with('success', 'Datos actualizados correctamente');
            } else {
                throw  new \Exception('Los datos no se actualizaron con exíto');
            }
        } catch (\Exception $e) {
            return redirect()->to(base_url('/providers/profile/' . $id))->with('error', $e->getMessage());
        }
    }
}