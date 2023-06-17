<?php


namespace App\Controllers;


use App\Controllers\Api\Auth;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\PaymentMethod;
use App\Models\ProductsDetails;
use App\Models\TypeDocument;
use App\Models\Wallet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;


class ReportsController extends BaseController
{
    public $tableInvoices;
    public $tableLineInvoices;
    public $tableTaxLineInvoices;
    public $controllerHeadquarters;
    public $tableCustomers;
    public $manager;
    public $idsCompanies;
    public $walletController;
    public $tableTypeDocuments;
    public $tableMethodPayment;
    public $tableCompanies;
    public $tableWallet;
    public $tableProductDetails;
    public $controllerCustomers;

    public function __construct()
    {
        $this->tableInvoices = new Invoice();
        $this->tableLineInvoices = new LineInvoice();
        $this->tableTaxLineInvoices = new LineInvoiceTax();
        $this->controllerHeadquarters = new HeadquartersController();
        $this->tableCustomers = new Customer();
        $this->walletController = new WalletController();
        $this->tableTypeDocuments = new TypeDocument();
        $this->tableMethodPayment = new PaymentMethod();
        $this->tableCompanies = new Company();
        $this->tableWallet = new Wallet();
        $this->tableProductDetails = new ProductsDetails();
        $this->controllerCustomers = new CustomerController();
    }

    private function activeUser()
    {
        $this->manager = $this->controllerHeadquarters->permissionManager(session('user')->role_id);
        $this->idsCompanies = $this->controllerHeadquarters->idsCompaniesText();
        if (!$this->manager) {
            $this->idsCompanies = Auth::querys()->companies_id;
        }
    }

    public function customerAges()
    {
        $invoicesMax = $this->data(1);
        $invoicesMax30 = $this->data(2);
        $invoicesMax60 = $this->data(3);
        $invoicesMax90 = $this->data(4);

        $data = [
            'invoices' => ['quantity' => count($invoicesMax), 'total' => $this->totalCa($invoicesMax)],
            'invoices30' => ['quantity' => count($invoicesMax30), 'total' => $this->totalCa($invoicesMax30)],
            'invoices60' => ['quantity' => count($invoicesMax60), 'total' => $this->totalCa($invoicesMax60)],
            'invoices90' => ['quantity' => count($invoicesMax90), 'total' => $this->totalCa($invoicesMax90)],
            'customers' => $this->tableCustomers->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())->whereIn('type_customer_id', [1])->asObject()->get()->getResult(),
            'title' => 'Edades Clientes'
        ];
        return view('report/customer_ages', $data);
    }

    public function totalCa($data)
    {
        $total = 0;
        foreach ($data as $item) {
            $total += $item->py;
        }
        return $total;
    }

    public function data($id)
    {
        $query = $this->caseDataAge($id);
        $invoices = $this->tableInvoices
            ->select([
                'invoices.id as id',
                'invoices.created_at as date',
                'customers.name as name',
                'invoices.payable_amount as total',
                'companies.company as company'
            ])
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            ->whereIn('customers.type_customer_id', [1])
            ->where($query)
            ->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->get()->getResult();
        foreach ($invoices as $item) {
            $item->py = $item->total;
            $item->total = '$ ' . number_format($item->total, '2', ',', '.');
            $item->action = '<div class="btn-group" role="group">
                <a href="' . base_url() . '/reports/view/' . $item->id . '" target="_blank"
                    class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                    <i class="material-icons">insert_drive_file</i>
                </a>
            </div>';
        }
        return $invoices;
    }

    public function kardex($id)
    {
        return json_encode($this->data($id));
    }

    public function incomeAndExpenses()
    {
        $this->activeUser();
        $option = '';
        $customers = $this->tableCustomers->whereIn('companies_id',  $this->controllerHeadquarters->idsCompaniesHeadquarters())->whereNotIn('name', ['gerente', 'Gerente'])->asObject()->get()->getResult();
        $customers = $this->controllerCustomers->organization($customers);
        $companies = $this->tableCompanies->whereIn('id', $this->controllerHeadquarters->idsCompaniesHeadquarters())->where(['id !=' => 1])->get()->getResult();
        $methodPayments = $this->tableMethodPayment->get()->getResult();
        if ($this->request->getGet('option')) {
            $option = $this->request->getGet('option');
        }
        switch ($option) {
            case 'Ingresos':
                $documents = [1, 2, 5, 108];
                break;
            case 'Egresos':
                $documents = [11, 105, 106, 114, 118, 107];
                break;
            default:
                $documents = [1, 2, 5, 11, 105, 106, 114, 118, 108, 107];
                break;
        }
        //$customers = $this->tableCustomers->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())->get()->getResult();

        $model = new Invoice();
        $total = $this->totals('income');
        $totalExpense = $this->totals();
        $data = $model->select($this->walletController->dataSearch($this->manager, $this->idsCompanies))->join('customers', 'customers.id = invoices.customers_id')
            ->whereIn('invoices.type_documents_id', $documents);
        //->whereIn('invoices.invoice_status_id', [2, 3, 4]);
        $this->search($data);
        if ($this->manager) {
            $data->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $data->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        $data->where('invoices.deleted_at', null)
            ->orderBy('CAST(invoices.resolution as UNSIGNED)', 'DESC');
        //echo json_encode($totalExpense->get()->getResult());die();
        return view('report/incomeAndExpenses', [
            'info' => $data->asObject()->paginate(10),
            'pager' => $data->pager,
            'total' => $total->get()->getResult(),
            'customers' => $customers,
            'totalE' => $totalExpense->get()->getResult(),
            'companies' => $companies,
            'paymentMethod' => $methodPayments
        ]);
    }

    protected function search(Invoice $data): void
    {
        $this->request->getGet('start_date') ? $data->where('invoices.created_at >=', $this->request->getGet('start_date') . ' 00:00:00') : '';
        $this->request->getGet('end_date') ? $data->where('invoices.created_at <=', $this->request->getGet('end_date') . ' 23:59:59') : '';
        $this->request->getGet('number') ? $data->where('invoices.resolution', $this->request->getGet('number')) : '';
        $this->request->getGet('customer') ? $data->where('invoices.customers_id', $this->request->getGet('customer')) : '';
        $this->request->getGet('company') ? $data->where('invoices.companies_id', $this->request->getGet('company')) : '';
        $this->request->getGet('payment_method') ? $data->where('invoices.payment_methods_id', $this->request->getGet('payment_method')) : '';
    }

    public function ageIncomeExpenses()
    {
        $this->activeUser();
        $search = 'income';
        if ($this->request->getGet('search')) {
            $search = $this->request->getGet('search');
        }
        //echo json_encode($this->dataAgeIncomeExpenses($search, 4));die();
        $invoicesMax = $this->dataAgeIncomeExpenses($search, 1);
        $invoicesMax30 = $this->dataAgeIncomeExpenses($search, 2);
        $invoicesMax60 = $this->dataAgeIncomeExpenses($search, 3);
        $invoicesMax90 = $this->dataAgeIncomeExpenses($search, 4);
        $invoicesMax180 = $this->dataAgeIncomeExpenses($search, 5);
        $customers = $this->tableCustomers->whereIn('companies_id',  $this->controllerHeadquarters->idsCompaniesHeadquarters())->whereNotIn('name', ['gerente', 'Gerente'])->asObject()->get()->getResult();
        $customers = $this->controllerCustomers->organization($customers);
        $total = $this->totals($search);
        return view('report/ageIncomeAndExpenses', [
            'total' => $total->get()->getResult(),
            'customers' => $customers,
            'invoicesMax' => ['quantity' => count($invoicesMax), 'total' => $this->totalCa($invoicesMax)],
            'invoicesMax30' => ['quantity' => count($invoicesMax30), 'total' => $this->totalCa($invoicesMax30)],
            'invoicesMax60' => ['quantity' => count($invoicesMax60), 'total' => $this->totalCa($invoicesMax60)],
            'invoicesMax90' => ['quantity' => count($invoicesMax90), 'total' => $this->totalCa($invoicesMax90)],
            'invoicesMax180' => ['quantity' => count($invoicesMax180), 'total' => $this->totalCa($invoicesMax180)],
            'document' => $search
        ]);
    }

    public function dataAgeIncomeExpenses($typeSearch, $id)
    {
        switch ($id) {
            case 2:
                $number = 1;
                $numberTwo = 2;
                break;
            case 3:
                $number = 2;
                $numberTwo = 3;
                break;
            case 4:
                $number = 3;
                $numberTwo = 6;
                break;
            case 5:
                $number = 6;
                $numberTwo = null;
                break;
            default:
                $number = 0;
                $numberTwo = 1;
                break;
        }
        $currentDay = date('Y/m/d', strtotime(date('Y/m/d') . "- " . $number . " month"));
        $finalDate = date('Y/m/d', strtotime(date('Y/m/d') . "- " . $numberTwo . " month"));
        $typeDocuments = $this->tableTypeDocuments->get()->getResult();
        $documents = [1, 2, 5, 108];
        if ($typeSearch == 'expenses') {
            $documents = [11, 105, 106, 114, 107, 118];
        }
        if (!is_null($numberTwo)) {
            $query = ['invoices.created_at >=' => $finalDate . ' 00:00:00', 'invoices.created_at <' => $currentDay . ' 23:59:59'];
        } else {
            $query = ['invoices.created_at <' => $currentDay . ' 23:59:59'];
        }
        $model = new Invoice();
        $data = $model->select($this->walletController->dataSearch($this->manager, $this->idsCompanies))
            ->join('customers', 'customers.id = invoices.customers_id')
            ->whereIn('invoices.type_documents_id', $documents);
        if ($typeSearch == 'income') {
            $data->whereIn('invoices.invoice_status_id', [2, 3, 4]);
        }
        if ($this->manager) {
            $data->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $data->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        $this->search($data);
        $data->where('invoices.deleted_at', null)
            ->where($query)
            ->orderBy('CAST(invoices.resolution as UNSIGNED)', 'DESC');
        $info = $data->get()->getResult();
        foreach ($info as $item) {
            foreach ($typeDocuments as $typeDocument) {
                if ($item->type_documents_id == $typeDocument->id) {
                    $item->nameTypeDocument = $typeDocument->name;
                }
            }
            $item->py = $item->payable_amount - $item->withholdings;
            $item->payable_amount = '$ ' . number_format(($item->payable_amount - $item->withholdings), '2', ',', '.');
            $item->action = '<div class="btn-group" role="group">
                <a href="' . base_url() . '/reports/view/' . $item->id . '" target="_blank"
                    class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                    <i class="material-icons">insert_drive_file</i>
                </a>
            </div>';
        }
        return $info;
    }

    private function name($info, $id): string
    {
        $name = '';
        switch ($info) {
            case 'company':
                $data = $this->tableCompanies->where(['id' => $id])->asObject()->first();
                $name = $data->company;
                break;
            case 'paymentMethod':
                $data = $this->tableMethodPayment->where(['id' => $id])->asObject()->first();
                $name = $data->name;
                break;
        }
        return $name;
    }

    public function dataIeA($id)
    {
        $this->activeUser();
        return json_encode($this->dataAgeIncomeExpenses($this->request->getGet('search'), $id));
    }

    private function totals($search = null): Invoice
    {
        $invoices = new Invoice();
        $total = $invoices->select([
            'SUM(invoices.payable_amount) as payable_amount',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id  GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) GROUP BY line_invoices.invoices_id) AS withholdings'
        ]);
        $this->search($total);
        if ($this->manager) {
            $total->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $total->where('invoices.companies_id', Auth::querys()->companies_id);
        }
        if ($search == 'income') {
            $total->whereIn('invoices.invoice_status_id', [2, 3, 4])
                ->whereIn('invoices.type_documents_id', [1, 2, 5, 108]);
        } else {
            $total->whereIn('invoices.type_documents_id', [11, 105, 106, 114, 118, 107]);
        }
        $total->where('invoices.deleted_at', null)->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id')
            ->asObject();

        return $total;
    }

    public function view($id)
    {
        $document = $this->tableInvoices
            ->select([
                'invoices.id',
                'invoices.notes',
                'invoices.line_extesion_amount',
                'invoices.tax_inclusive_amount',
                'invoices.tax_exclusive_amount',
                'invoices.payable_amount',
                'invoices.issue_date',
                'invoices.payment_due_date',
                'invoices.resolution',
                'invoices.created_at',
                'customers.name as name',
                'customers.identification_number as identification',
                'customers.phone',
                'customers.address',
                'customers.email',
                'type_documents.name as nameDocument',
                'type_document_identifications.name as typeDocumentIdentification',
                'municipalities.name as municipio',
                'invoices.type_documents_id',
                'payment_forms.name as payment_forms_name'
            ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('municipalities', 'customers.municipality_id = municipalities.id', 'left')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('payment_forms', 'payment_forms.id = invoices.payment_forms_id', 'left')
            ->where(['invoices.id' => $id])->asObject()->first();
        // var_dump($document); die();
        $lineDocuments = $this->tableLineInvoices
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
                'line_invoices.discount_amount'
            ])
            ->join('products', 'products.id = line_invoices.products_id')
            ->where(['invoices_id' => $document->id])
            ->asObject()
            ->findAll();
        $taxTotal = 0;
        foreach ($lineDocuments as $item) {
            $taxes = $this->tableTaxLineInvoices->where(['line_invoices_id' => $item->id])->whereIn('taxes_id', [5, 6, 7])->asObject()->get()->getResult();
            foreach ($taxes as $tax) {
                $taxTotal += $tax->tax_amount;
            }
        }

        $mpdf = new \Mpdf\Mpdf([
            'format' => 'Letter',
            'default_font_size' => 9,
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 35,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 5
        ]);


        $stylesheet = file_get_contents(base_url() . '/assets/css/bootstrap.css');

        $mpdf->WriteHtml($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLHeader(view('invoice/previsualizador/header', [
            'invoice'       => $document,
        ]));
        $mpdf->WriteHtml(view('invoice/previsualizador/body', [
            'invoice'       => $document,
            'taxTotal' =>   $taxTotal,
            'withholding'   => $lineDocuments
        ]), \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->setFooter('{PAGENO}');
        $mpdf->Output();

        die();

        /*return view('invoice/view', [
            'document' => $document,
            'lineDocuments' => $lineDocuments,
            'taxTotal' => $taxTotal
        ]);*/
    }

    public function sell()
    {
        $paymentsMethod = [];
        $order = [];
        $pay = 0;
        $sellTotal = 0;
        $paymentTotal = 0;
        $idsCost = [];
        // todas la ventas
        $headquarters = $this->tableCompanies->select(['id', 'company'])->whereIn('id', $this->controllerHeadquarters->idsCompaniesHeadquarters())->asObject()->get()->getResult();
        $invoices = new Invoice();
        $totalSell = $invoices->select([
            'invoices.id',
            'invoices.payment_methods_id as method_payment',
            'SUM(invoices.payable_amount) as payable_amount',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id  GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) GROUP BY line_invoices.invoices_id) AS withholdings'
        ]);
        $this->extracted($totalSell);
        $totalSell
            //->whereIn('invoices.invoice_status_id', [2, 3, 4])
            ->whereIn('invoices.type_documents_id', [1, 2, 5, 108])
            ->where(['invoices.deleted_at' => null, 'invoices.payment_forms_id' => 1])->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id')
            ->asObject();
        // echo json_encode($totalSell->get()->getResult());die();
        $methodPayments = $this->tableMethodPayment->get()->getResult();
        foreach ($totalSell->get()->getResult() as $item) {
            $pay += $item->balance;
            array_push($idsCost, $item->id);
            foreach ($methodPayments as $methodPayment) {
                if ($methodPayment->id == $item->method_payment) {
                    $valor = ($item->payable_amount - $item->withholdings) - $item->balance;
                    $sellTotal += $valor;
                    if (isset($paymentsMethod[$methodPayment->name])) {
                        $paymentsMethod[$methodPayment->name]['total'] = $paymentsMethod[$methodPayment->name]['total'] + $valor;
                    } else {
                        $paymentsMethod[$methodPayment->name] = ['total' => $valor, 'name' => $methodPayment->name];
                    }
                }
            }
        }
        foreach ($paymentsMethod as $key => $row) {
            $order[$key] = $row['name'];
        }
        array_multisort($order, SORT_ASC, $paymentsMethod);
        //sort($paymentsMethod, 'SORT_NATURAL');

        // abonos
        $wallet = new Wallet();
        $pays = $wallet
            ->select(['Sum(wallet.value) as total'])
            ->join('invoices', 'wallet.invoices_id = invoices.id')
            //->whereIn('invoices.invoice_status_id', [2, 3, 4])
            ->whereIn('invoices.type_documents_id', [1, 2, 5, 108]);
        if (!isset($_GET['headquarters']) || $_GET['headquarters'] == 'todos') {
            $pays->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $pays->where('invoices.companies_id', $_GET['headquarters']);
        }
        $this->request->getGet('start_date') ? $pays->where('wallet.created_at >=', $this->request->getGet('start_date') . ' 00:00:00') : $pays->where('wallet.created_at >=', date('Y-m-d') . ' 00:00:00');
        $this->request->getGet('end_date') ? $pays->where('wallet.created_at <=', $this->request->getGet('end_date') . ' 23:59:59') : $pays->where('wallet.created_at <=', date('Y-m-d') . ' 23:59:59');
        $pays->asObject();
        //echo json_encode($pays->first());die();
        // todos los gatos Modulo Gastos
        $lineInvoices = new LineInvoice();
        $bills = $lineInvoices->select([
            'SUM(line_invoices.line_extension_amount) as payable_amount',
            'products.name as name'
        ]);
        if (!isset($_GET['headquarters']) || $_GET['headquarters'] == 'todos') {
            $bills->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $bills->where('invoices.companies_id', $_GET['headquarters']);
        }
        $this->request->getGet('start_date') ? $bills->where('invoices.created_at >=', $this->request->getGet('start_date') . ' 00:00:00') : $bills->where('invoices.created_at >=', date('Y-m-d') . ' 00:00:00');
        $this->request->getGet('end_date') ? $bills->where('invoices.created_at <=', $this->request->getGet('end_date') . ' 23:59:59') : $bills->where('invoices.created_at <=', date('Y-m-d') . ' 23:59:59');
        $bills->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->where(['invoices.deleted_at' => null, 'invoices.type_documents_id' => 118])
            ->orderBy('products.name', 'ASC')
            ->groupBy('products.id')
            ->asObject();
        // echo json_encode($bills->get()->getResult());die();
        // todos los pagos
        $invoices = new Invoice();
        $payments = $invoices->select([
            'SUM(invoices.payable_amount) as payable_amount',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id  GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) GROUP BY line_invoices.invoices_id) AS withholdings'
        ]);
        $this->extracted($payments);
        $payments->whereIn('invoices.type_documents_id', [11, 105, 106, 114, 107])
            ->where(['invoices.deleted_at' => null])->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id')
            ->asObject();
        foreach ($payments->get()->getResult() as $item) {
            $paymentTotal = ($item->payable_amount - $item->withholdings) - $item->balance;
        }

        // cost
        $costTotal = 0;
        if (count($idsCost)) {
            $cost = $this->costs($idsCost);
            foreach ($cost as $item) {
                $costTotal += $item->quantity * $item->cost;
            }
        }
        // echo json_encode($cost);die();
        $type = (isset($_GET['type'])) ? $_GET['type'] : '';
        switch ($type) {
            case 'ventas':
                $data = [
                    'sell' => $paymentsMethod,
                    'sellTotal' => $sellTotal,
                    'pays' => $pays->first(),
                    'bills' => [],
                    'payments' => 0,
                    'cost' => $costTotal,
                    'headquarters' => $headquarters
                ];
                break;
            case 'gastos':
                $data = [
                    'sell' => [],
                    'sellTotal' => 0,
                    'pays' => (object)['total' => 0],
                    'bills' => $bills->get()->getResult(),
                    'payments' => $paymentTotal,
                    'cost' => $costTotal,
                    'headquarters' => $headquarters
                ];
                break;
            case 'utilidad':
                $data = [
                    'sell' => [],
                    'sellTotal' => $sellTotal,
                    'pays' => (object)['total' => 0],
                    'bills' => $bills->get()->getResult(),
                    'payments' => 0,
                    'cost' => $costTotal,
                    'headquarters' => $headquarters
                ];
                break;
            default:
                $data = [
                    'sell' => $paymentsMethod,
                    'sellTotal' => $sellTotal,
                    'pays' => $pays->first(),
                    'bills' => $bills->get()->getResult(),
                    'payments' => $paymentTotal,
                    'cost' => $costTotal,
                    'headquarters' => $headquarters
                ];
                break;
        }
        return view('report/sell', $data);
    }

    /**
     * @param Invoice $payments
     */
    private function extracted(Invoice $payments): void
    {
        if (!isset($_GET['headquarters']) || $_GET['headquarters'] == 'todos') {
            $payments->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $payments->where('invoices.companies_id', $_GET['headquarters']);
        }
        $this->request->getGet('start_date') ? $payments->where('invoices.created_at >=', $this->request->getGet('start_date') . ' 00:00:00') : $payments->where('invoices.created_at >=', date('Y-m-d') . ' 00:00:00');
        $this->request->getGet('end_date') ? $payments->where('invoices.created_at <=', $this->request->getGet('end_date') . ' 23:59:59') : $payments->where('invoices.created_at <=', date('Y-m-d') . ' 23:59:59');
    }

    private function costs($ids)
    {
        //echo json_encode($ids);die();
        $lineInvoices = new LineInvoice();
        $cost = $lineInvoices->select([
            'products.id as idProduct',
            'products.cost',
            'line_invoices.quantity'
        ]);
        if (!isset($_GET['headquarters']) || $_GET['headquarters'] == 'todos') {
            $cost->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        } else {
            $cost->where('invoices.companies_id', $_GET['headquarters']);
        }
        $this->request->getGet('start_date') ? $cost->where('invoices.created_at >=', $this->request->getGet('start_date') . ' 00:00:00') : $cost->where('invoices.created_at >=', date('Y-m-d') . ' 00:00:00');
        $this->request->getGet('end_date') ? $cost->where('invoices.created_at <=', $this->request->getGet('end_date') . ' 23:59:59') : $cost->where('invoices.created_at <=', date('Y-m-d') . ' 23:59:59');
        $cost->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', ' products.id = line_invoices.products_id')
            ->whereIn('invoices.id', $ids)
            ->asObject();
        $data = $cost->get()->getResult();
        foreach ($data as $item) {
            $detail = $this->tableProductDetails->select(['cost_value'])->where(['id_product' => $item->idProduct, 'status' => 'active'])->asObject()->first();
            if (!is_null($detail)) {
                $item->cost = $detail->cost_value;
            }
        }
        return $data;
    }

    public function providersAges()
    {
        $invoicesMax = $this->dataProvider(1);
        $invoicesMax30 = $this->dataProvider(2);
        $invoicesMax60 = $this->dataProvider(3);
        $invoicesMax90 = $this->dataProvider(4);
        $customers = $this->tableCustomers->whereIn('companies_id',  $this->controllerHeadquarters->idsCompaniesHeadquarters())->whereNotIn('name', ['gerente', 'Gerente'])->where(['type_customer_id' => 2])->asObject()->get()->getResult();
        $customers = $this->controllerCustomers->organization($customers);
        $data = [
            'invoices' => ['quantity' => count($invoicesMax), 'total' => $this->totalCa($invoicesMax)],
            'invoices30' => ['quantity' => count($invoicesMax30), 'total' => $this->totalCa($invoicesMax30)],
            'invoices60' => ['quantity' => count($invoicesMax60), 'total' => $this->totalCa($invoicesMax60)],
            'invoices90' => ['quantity' => count($invoicesMax90), 'total' => $this->totalCa($invoicesMax90)],
            'customers' => $customers,
            'title' => 'Edades Proveedores'
        ];
        return view('report/customer_ages', $data);
    }

    public function dataProvider($id): array
    {
        $query = $this->caseDataAge($id);
        $invoices = $this->tableInvoices
            ->select([
                'invoices.id as id',
                'invoices.created_at as date',
                'customers.name as name',
                'invoices.payable_amount as total',
                'companies.company as company'
            ])
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [101, 102, 107])
            ->whereIn('customers.type_customer_id', [2])
            ->where($query)
            ->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->get()->getResult();
        foreach ($invoices as $item) {
            $item->py = $item->total;
            $item->total = '$ ' . number_format($item->total, '2', ',', '.');
            $item->action = '<div class="btn-group" role="group">
                <a href="' . base_url() . '/reports/view/' . $item->id . '" target="_top"
                    class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
                    <i class="material-icons">insert_drive_file</i>
                </a>
            </div>';
        }
        return $invoices;
    }

    public function kardexP($id)
    {
        return json_encode($this->dataProvider($id));
    }

    /**
     * casos y querys para edades cliente proveedores
     * @param $id
     * @return array|string[]
     */
    public function caseDataAge($id): array
    {
        switch ($id) {
            case 1:
                $number = 0;
                $numberTwo = 1;
                break;
            case 3:
                $number = 2;
                $numberTwo = 3;
                break;
            case 4:
                $number = 3;
                $numberTwo = null;
                break;
            default:
                $number = 1;
                $numberTwo = 2;
                break;
        }
        $currentDay = date('Y/m/d', strtotime(date('Y/m/d') . "- " . $number . " month"));
        $finalDate = date('Y/m/d', strtotime(date('Y/m/d') . "- " . $numberTwo . " month"));

        //echo json_encode($finalDate);die();
        if (!is_null($numberTwo)) {
            $query = ['invoices.created_at >=' => $finalDate . ' 00:00:00', 'invoices.created_at <' => $currentDay . ' 23:59:59'];
        } else {
            $query = ['invoices.created_at <' => $currentDay . ' 23:59:59'];
        }
        if ($this->request->getGet('customer')) {
            $query = array_merge($query, ['invoices.customers_id' => $this->request->getGet('customer')]);
        }
        return $query;
    }
}