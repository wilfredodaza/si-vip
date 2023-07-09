<?php


namespace App\Controllers;
use CodeIgniter\API\ResponseTrait;


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
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;


class ReportsController extends BaseController
{
    use ResponseTrait;
    
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

    public $permi;

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
        $this->permi = (session('user')->role_id == 15 || session('user')->role_id == 16 || session('user')->role_id == 17) ? true : false;
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
        $invoicesMax = $this->data(1)->countAllResults();
        // return var_dump($this->totalCa($invoicesMax));
        $invoicesMax30 = $this->data(2)->countAllResults();
        $invoicesMax60 = $this->data(3)->countAllResults();
        $invoicesMax90 = $this->data(4)->countAllResults();

        $data = [
            'invoices' => ['quantity' => $invoicesMax],//, 'total' => $this->totalCa($invoicesMax)
            'invoices30' => ['quantity' => $invoicesMax30],//, 'total' => $this->totalCa($invoicesMax30)
            'invoices60' => ['quantity' => $invoicesMax60],// , 'total' => $this->totalCa($invoicesMax60)
            'invoices90' => ['quantity' => $invoicesMax90], //, 'total' => $this->totalCa($invoicesMax90)
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

    public function data($id, $dataTable = false)
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

        $invoices = $this->tableInvoices
            ->select([
                // 'invoices.id as id',
                // 'invoices.created_at as date',
                'customers.name as name',
                'customers.id as customer_id',
                'SUM(invoices.payable_amount) as total',
                'companies.company as company',
                'COUNT(invoices.id) as total_invoices'
            ])
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            ->whereIn('customers.type_customer_id', [1]);
        if(isset($dataTable->data->customer))
            $invoices->whereIn('invoices.customers_id', $dataTable->data->customer);
        if($dataTable){
            $invoices = $invoices
            ->where(['invoices.created_at >=' => isset($dataTable->data->date_init) ? $dataTable->data->date_init : date('Y-m-d', strtotime(date('Y-m-d') . "- 1 month"))])
            ->where(['invoices.created_at <=' => isset($dataTable->data->date_end) ? $dataTable->data->date_end.' 23:59:59' : date('Y-m-d 23:59:59')]);
        }else{
            if (!is_null($numberTwo)) {
                $invoices->where(['invoices.created_at >=' => $finalDate . ' 00:00:00', 'invoices.created_at <' => $currentDay . ' 23:59:59']);
            } else {
                $invoices->where(['invoices.created_at <' => $currentDay . ' 23:59:59']);
            }
        }
        $invoices = $invoices
            ->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())
            ->groupBy('invoices.customers_id');
        //     ->get()->getResult();
        // foreach ($invoices as $item) {
        //     $item->py = $item->total;
        //     // $item->total = '$ ' . number_format($item->total, '2', ',', '.');
        //     $item->action = '<div class="btn-group" role="group">
        //         <a href="' . base_url() . '/reports/view/' . $item->id . '" target="_blank"
        //             class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
        //             <i class="material-icons">insert_drive_file</i>
        //         </a>
        //     </div>';
        // }
        return $invoices;
    }

    public function getInvoices($dataTable){
        $invoicesM = new Invoice();
        $invoicesM
            ->select([
                'SUM(invoices.payable_amount) as total',
                'customers.name',
                'companies.company',
                'COUNT(invoices.id) as total_invoices'
            ])
            ->join('customers', 'invoices.customers_id = customers.id')
            ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [1, 2, 108])
            ->whereIn('customers.type_customer_id', [1]);
        if(isset($dataTable->data->customer))
            $invoicesM->whereIn('invoices.customers_id', $dataTable->data->customer);
        $invoicesM->where(['invoices.created_at >=' => $dataTable->data->date_init ? $dataTable->datadate_init : date('Y-m-d', strtotime(date('Y-m-d') . "- 1 month"))])
            ->where(['invoices.created_at <=' => $dataTable->data->date_end ? $dataTable->datadate_end : date('Y-m-d 23:59:59')])
            ->groupBy('invoices.customers_id');
        
        return $invoicesM;
    }

    public function kardex($id)
    {
        $dataTable = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 1,
            'page'      => ceil(($start - 1) / $length + 1),
            'columns'   => $_GET['columns'] ?? [],
            'data'      => (object) $this->request->getGet()
        ];
        // $data = $this->getInvoices($dataTable)->asObject()->paginate($dataTable->length, 'dataTable', $dataTable->page);
        $data = $this->data($id, $dataTable)->asObject()->paginate($dataTable->length, 'dataTable', $dataTable->page);
        $total = $this->data($id, $dataTable)->countAllResults();
        return $this->respond([
            "recordsTotal" => $total, //$this->getInvoices($dataTable)->countAllResults(),
            "recordsFiltered" => $total, //$this->getInvoices($dataTable)->countAllResults(),
            'table' => $data,
            "draw" => $dataTable->draw,
            // 'algo' => isset($dataTable->data->customer) ? $dataTable->data->customer : $this->controllerHeadquarters->idsCompaniesHeadquarters()
        ], 200);
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
        $typeDocuments = $this->tableTypeDocuments->get()->getResult();
        // if ($this->request->getGet('option')) {
        // }
        $option = session('user')->role_id == 16 ? 'Ingresos' : ($this->request->getGet('option') ? $this->request->getGet('option'):'');
        switch ($option) {
            case 'Ingresos':
                $documents = [1, 2, 5, 108];
                break;
            case 'Todos':
            case '':
                // $documents = [11, 105, 106, 114, 118, 107];
                // $documents = [1, 2, 5, 11, 105, 106, 114, 118, 108, 107];
                $documents = [];
                foreach ($typeDocuments as $key => $value) {
                    array_push($documents, $value->id);
                }
                break;
            default:
                $documents = [$option];
                break;
        }
        $document2 = implode(', ', $documents);
        //$customers = $this->tableCustomers->whereIn('companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters())->get()->getResult();

        $model = new Invoice();
        $total = $this->totals('income');
        $totalExpense = session('user')->role_id == 16 ? [] : $this->totals();
        $data = $model->select($this->walletController->dataSearch($this->manager, $this->idsCompanies))
            ->select('type_documents.name as name_document')
            ->join('customers', 'customers.id = invoices.customers_id', 'left')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id', 'left')
            ->whereIn('invoices.type_documents_id', $documents);
        
 
        //->whereIn('invoices.invoice_status_id', [2, 3, 4]);
        $this->search($data);
        // if ($this->manager) {
        //     $data->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        // } else {
        //     // $data->where('invoices.companies_id', Auth::querys()->companies_id);
        // }
        $data->where('
            (invoices.companies_id = 2 and invoices.company_destination_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$document2.'))
            or (invoices.company_destination_id = 3 and invoices.companies_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$document2.'))
            or (invoices.companies_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$document2.'))
            or (invoices.company_destination_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$document2.'))
        ');
        $data->where('invoices.deleted_at', null)
            ->orderBy('invoices.id', 'DESC');
            // var_dump([Auth::querys()->companies_id, $document2, $data->get()->getResult()]);die;
        //echo json_encode($totalExpense->get()->getResult());die();
        return view('report/incomeAndExpenses', [
            'info' => $data->asObject()->paginate(10),
            'pager' => $data->pager,
            'total' => $total->get()->getResult(),
            'customers' => $customers,
            'totalE' => session('user')->role_id == 16 ? [] : $totalExpense->get()->getResult(),
            'companies' => $companies,
            'paymentMethod' => $methodPayments,
            'typeDocuments' => $typeDocuments
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

    private function totals($search = null ): Invoice
    {
        $invoices = new Invoice();
        $total = $invoices->select([
            'invoices.id',
            'SUM(invoices.payable_amount) as payable_amount',
            '(SELECT  IFNULL(SUM(value), 0) FROM wallet WHERE wallet.invoices_id = invoices.id  GROUP  BY wallet.invoices_id) as balance',
            '(SELECT IFNULL(SUM(tax_amount), 0) FROM line_invoices INNER JOIN line_invoice_taxs ON line_invoice_taxs.line_invoices_id  =  line_invoices.id WHERE line_invoices.invoices_id = invoices.id AND line_invoice_taxs.taxes_id IN (5,6,7) GROUP BY line_invoices.invoices_id) AS withholdings'
        ]);
        $this->search($total);
        // if ($this->manager) {
        //     // $total->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        // } else {
        //     $total->where('invoices.companies_id', Auth::querys()->companies_id);
        // }
        if ($search == 'income') {
            $documents = '1, 2, 5, 108';
            $total->whereIn('invoices.invoice_status_id', [2, 3, 4]);
                // ->whereIn('invoices.type_documents_id', [1, 2, 5, 108]);
        } else {
            $documents = '11, 105, 106, 114, 118, 107';
            // $total->whereIn('invoices.type_documents_id', [11, 105, 106, 114, 118, 107]);
        }
        $total->where('
            (invoices.companies_id = 2 and invoices.company_destination_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$documents.'))
            or (invoices.company_destination_id = 3 and invoices.companies_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$documents.'))
            or (invoices.companies_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$documents.'))
            or (invoices.company_destination_id = '.Auth::querys()->companies_id.' and invoices.type_documents_id in ('.$documents.'))
        ');

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
                'invoices.user_id',
                'invoices.invoice_status_id',
                'customers.name as name',
                'customers.identification_number as identification',
                'customers.phone',
                'customers.address',
                'customers.email',
                'type_documents.name as nameDocument',
                'type_document_identifications.name as typeDocumentIdentification',
                'municipalities.name as municipio',
                'invoices.type_documents_id',
                'payment_forms.name as payment_forms_name',
                'users.name as user_name',
                'companies.company as company_name',
                'companies.address as company_address',
                'companies.email as company_email',
                'accounting_account.name as accounting_account_name',
            ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('users', 'users.id = invoices.user_id', 'left')
            ->join('municipalities', 'customers.municipality_id = municipalities.id', 'left')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('companies', 'invoices.companies_id = companies.id','left')
            ->join('payment_forms', 'payment_forms.id = invoices.payment_forms_id', 'left')
            ->join('wallet', 'wallet.invoices_id = invoices.id', 'left')
            ->join('accounting_account', 'accounting_account.id = wallet.payment_method_id', 'left')
            ->where(['invoices.id' => $id])->asObject()->first();
        // var_dump($document); die();
        if($document->invoice_status_id == 28){
            $userM = new User();
            $user = $userM->where(['id' => $document->user_id])->asObject()->first();
        }else{
            $user = (object) [];
        }
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
            'margin_top' => 38,
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
            'withholding'   => $lineDocuments,
            'user' => $user
        ]), \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->SetHTMLFooter('
        <table width="100%">
            <tr>
                <td width="50%" align="left">Software elaborado por IPlanet Colombia SAS</td>
                <td width="50%" align="right">Pagina {PAGENO}/{nbpg}</td>
            </tr>
        </table>');
        // $mpdf->setFooter('{PAGENO}');
        $mpdf->Output();

        die();

        /*return view('invoice/view', [
            'document' => $document,
            'lineDocuments' => $lineDocuments,
            'taxTotal' => $taxTotal
        ]);*/
    }

    public function client($id, $type){
        $mpdf = new \Mpdf\Mpdf([
            'format' => 'Letter',
            'default_font_size' => 9,
            'default_font' => 'Roboto',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_header' => 5,
            'margin_footer' => 5
        ]);
        if($type == 1){
            $types = [1, 2, 108];
        }else $types = [107];
        $invoices = $this->tableInvoices
        ->select([
            'invoices.*',
            'payment_forms.name as payment_form',
            'customers.name name_customer'
        ])
        ->join('customers', 'invoices.customers_id = customers.id')
        ->join('payment_forms', 'payment_forms.id = invoices.payment_forms_id',' left')
        ->whereIn('invoices.type_documents_id', $types)//[1, 2, 108]
        ->whereIn('customers.type_customer_id', [$type])
        ->where(['customers.id' => $id])
        ->where(['invoices.created_at >=' => $this->request->getGet('date_init')])
        ->where(['invoices.created_at <=' => $this->request->getGet('date_end').' 23:59:59'])
        ->orderBy('invoices.id', 'DESC')
        ->get()->getResult();

        $stylesheet = file_get_contents(base_url() . '/assets/css/bootstrap.css');

        $mpdf->WriteHtml($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        // $mpdf->SetHTMLHeader(view('invoice/previsualizador/header_report', [
        //     'date_init'       => $this->request->getGet('date_init'),
        //     'date_end'       => $this->request->getGet('date_end'),
        // ]), \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->WriteHtml(view('invoice/previsualizador/body_report', [
            'invoices'  => $invoices,
            'date_init' => $this->request->getGet('date_init'),
            'date_end'  => $this->request->getGet('date_end'),
            'type'      => $type,
            // 'user' => $user
        ]), \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->setFooter('{PAGENO}');
        $mpdf->Output();
        die();
        // return 'hola';
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
        $headquarters = $this->tableCompanies->select(['id', 'company'])->where(['headquarters_id' => 2])->whereIn('id', $this->controllerHeadquarters->idsCompaniesHeadquarters())->asObject()->get()->getResult();
        /*
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
                ->where(['invoices.deleted_at' => null])->orderBy('invoices.id', 'DESC')
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
            $payments->whereIn('invoices.type_documents_id', [11, 105, 106, 107])
                ->where(['invoices.deleted_at' => null])->orderBy('invoices.id', 'DESC')
                ->groupBy('invoices.id')
                ->asObject();
            
            var_dump($payments->get()->getResult()); die();

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
        */
        $userM = new User();
        $usuarios = $userM->where('id !=', 1)->asObject()->get()->getResult();

        $invoiceM = new Invoice();
        $invoiceM->select(['invoices.*', 'payment_methods.type_entry'])//, 'wallet.value as wallet_value'
            ->join('wallet','invoices.id = wallet.invoices_id', 'left')
            ->join('payment_methods', 'payment_methods.id = invoices.payment_methods_id', 'left');
        $this->extracted($invoiceM);
        $invoices = $invoiceM->asObject()->get()->getResult();

        $walletM = new Wallet();
        $walletM->select(['invoices.*', 'wallet.value as wallet_value', 'payment_methods.type_entry'])
            ->join('invoices', 'invoices.id = wallet.invoices_id', 'left')
            ->join('payment_methods', 'payment_methods.id = wallet.payment_method_id', 'left')
            ->where([
                'wallet.created_at >=' => $this->request->getGet('start_date') ? $this->request->getGet('start_date').' 00:00:00' : date('Y-m-d 00:00:00'),
                'wallet.created_at <=' => $this->request->getGet('end_date') ? $this->request->getGet('end_date').' 23:59:59' : date('Y-m-d 23:59:59'),
            ])
            ->where('(invoices.payment_forms_id = 2 or invoices.companies_id = 2)');
        if ($this->permi){
            if($this->request->getGet('headquarters_providers')) $walletM->where(['invoices.companies_id' => $this->request->getGet('headquarters_providers')]);
            if($this->request->getGet('user_id')) $walletM->where(['invoices.user_id' => $this->request->getGet('user')]);
        }else{
            $walletM->where(['invoices.companies_id' => session('user')->companies_id]);
            $walletM->where(['invoices.user_id' => session('user')->id]);
        }
        $wallets = $walletM->asObject()->get()->getResult();
        // var_dump($invoices); die;

        $data = (object)[
            'permiso'   => $this->permi,
            'ventas'    => (object)['total' => 0, 'total_costos' => 0],
            'gastos'    => (object)['total' => 0, 'gastos_nomina' => 0, 'otros_gastos' => 0]
        ];
        $type = (isset($_GET['type'])) ? $_GET['type'] : '';
        switch ($type) {
            case 'ventas':
            default:
                $data->CxC = (object)[
                    'total'     => 0,
                    'detail'    => (object)[
                        'efectivo'      => (object) ['total' => 0,'name'  => 'Efectivo'],
                        'transferencia' => (object) ['total' => 0,'name'  => 'Transferencia'],
                    ]
                ];
                $data->CxP = (object)[
                    'total'     => 0,
                    'detail'    => (object)[
                        'efectivo'      => (object) ['total' => 0,'name'  => 'Efectivo'],
                        'transferencia' => (object) ['total' => 0,'name'  => 'Transferencia'],
                    ]
                ];
                $data->ventas->detail = (object)[
                    'efectivo'      => (object) ['total' => 0,'name'  => 'Efectivo'],
                    'transferencia' => (object) ['total' => 0,'name'  => 'Transferencia'],
                    'credito'       => (object) ['total' => 0,'name'  => 'Credito'],
                ];
                $data->gastos->detail = (object)[
                    'efectivo'      => (object) ['total' => 0,'name'  => 'Efectivo'],
                    'transferencia' => (object) ['total' => 0,'name'  => 'Transferencia'],
                    'credito'       => (object) ['total' => 0,'name'  => 'Credito'],
                ];
                foreach ($invoices as $key => $invoice) {
                    switch ($invoice->type_documents_id) {
                        case '108': // Salida remision
                        case '1':
                        case '2':
                        case '3':
                            $data->ventas->total += $invoice->payable_amount;
                            if($invoice->payment_forms_id == 1){
                                $data->ventas->detail->efectivo->total += ($invoice->type_entry != 1) ? $invoice->payable_amount : 0;
                                $data->ventas->detail->transferencia->total += ($invoice->type_entry == 1) ? $invoice->payable_amount : 0;
                            }else $data->ventas->detail->credito->total += $invoice->payable_amount;
                            break;
                        case '118':
                            $data->gastos->total += $invoice->payable_amount;
                            if($invoice->payment_forms_id == 1){
                                $data->gastos->detail->efectivo->total += ($invoice->type_entry != 1) ? $invoice->payable_amount : 0;
                                $data->gastos->detail->transferencia->total += ($invoice->type_entry == 1) ? $invoice->payable_amount : 0;
                            }else $data->gastos->detail->credito->total += $invoice->payable_amount;
                            break;
                    }
                }
                foreach ($wallets as $key => $wallet) {
                    switch ($wallet->type_documents_id) {
                        case '107': // entrada por remision
                            $data->CxP->total += $wallet->wallet_value;
                            $data->CxP->detail->efectivo->total += ($wallet->type_entry != 1) ? $wallet->wallet_value : 0;
                            $data->CxP->detail->transferencia->total += ($wallet->type_entry == 1) ? $wallet->wallet_value : 0;
                            break;
                        case '108':
                        case '1':
                            $data->CxC->total += $wallet->wallet_value;
                            $data->CxC->detail->efectivo->total += ($wallet->type_entry != 1) ? $wallet->wallet_value : 0;
                            $data->CxC->detail->transferencia->total += ($wallet->type_entry == 1) ? $wallet->wallet_value : 0;
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
                $data->total = (object)[
                    'bruto' => (($data->ventas->total + $data->CxC->total) - ($data->gastos->total + $data->CxP->total)),
                    'efectivo' => (
                        ($data->ventas->detail->efectivo->total + $data->CxC->detail->efectivo->total) 
                        - ($data->gastos->detail->efectivo->total + $data->CxP->detail->efectivo->total)
                    )
                ];
                break;
            case 'utilidad':
                $data->gastos->detail = (object)[
                    'efectivo'      => (object) ['total' => 0,'name'  => 'Efectivo'],
                    'transferencia' => (object) ['total' => 0,'name'  => 'Transferencia'],
                    'credito'       => (object) ['total' => 0,'name'  => 'Credito'],
                ];
                foreach ($invoices as $key => $invoice) {
                    switch ($invoice->type_documents_id) {
                        case '108': // Salida remision
                        case '1':
                        case '2':
                        case '3':
                            $invoice->line_invoice = $invoiceM->getLineInvoicesReports($invoice->id);
                            foreach ($invoice->line_invoice as $key => $line_invoice) {
                                // if(!filter_var($line_invoice->payroll, FILTER_VALIDATE_BOOLEAN))
                                    $data->ventas->total_costos += $line_invoice->cost_amount;
                            }
                            $data->ventas->total += $invoice->payable_amount;
                            break;
                        case '118':
                        // case '107':
                            $data->gastos->total += $invoice->payable_amount;
                            if($invoice->payment_forms_id == 1){
                                $data->gastos->detail->efectivo->total += ($invoice->type_entry != 1) ? $invoice->payable_amount : 0;
                                $data->gastos->detail->transferencia->total += ($invoice->type_entry == 1) ? $invoice->payable_amount : 0;
                            }else $data->gastos->detail->credito->total += $invoice->payable_amount;
                            break;
                        // case '118':
                        //     $data->gastos->total += $invoice->payable_amount;
                        //     if($invoice->payment_forms_id == 1){
                        //         $data->gastos->detail->efectivo->total += ($invoice->type_entry != 1) ? $invoice->payable_amount : 0;
                        //         $data->gastos->detail->transferencia->total += ($invoice->type_entry == 1) ? $invoice->payable_amount : 0;
                        //     }else $data->gastos->detail->credito->total += $invoice->payable_amount;
                        //     break;
                        default:
                            # code...
                            break;
                    }
                }
                foreach ($wallets as $key => $wallet) {
                    switch ($wallet->type_documents_id) {
                        case '107': // entrada por remision
                            $data->gastos->otros_gastos += $invoice->payable_amount;
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                }
                break;
            case 'gastos':
                foreach ($invoices as $key => $invoice) {
                    switch ($invoice->type_documents_id) {
                        case '107': // Entrada remision
                        case '118': // Gastos nomina
                            // $invoice->line_invoice = $invoiceM->getLineInvoicesReports($invoice->id);
                            // foreach ($invoice->line_invoice as $key => $line_invoice) {
                            //     // if(!filter_var($line_invoice->payroll, FILTER_VALIDATE_BOOLEAN))
                            //         $data->ventas->total_costos += $line_invoice->cost_amount;
                            // }
                            $data->gastos->total += $invoice->payable_amount;
                            break;
                        // case '118':
                        // case '107':
                        //     if($invoice->type_documents_id == '118') $data->gastos->gastos_nomina += $invoice->payable_amount;
                        //     else $data->gastos->otros_gastos += $invoice->payable_amount;
                        //     $data->gastos->total = $data->gastos->otros_gastos + $data->gastos->gastos_nomina;
                        //     break;
                        default:
                            # code...
                            break;
                    }
                }
                foreach ($wallets as $key => $wallet) {
                    switch ($wallet->type_documents_id) {
                        case '107': // entrada por remision
                            // if($wallet->type_documents_id == '118') $data->gastos->gastos_nomina += $wallet->wallet_value;
                            $data->gastos->otros_gastos += $wallet->wallet_value;
                            break;
                        case '118':
                            $wallet->line_invoice = $invoiceM->getLineInvoicesReports($invoice->id);
                            foreach ($wallet->line_invoice as $key => $line_invoice) {
                                // if(!filter_var($line_invoice->payroll, FILTER_VALIDATE_BOOLEAN))
                                    $data->ventas->otros_gastos += $line_invoice->line_extension_amount;
                            }
                        default:
                            # code...
                            break;
                    }
                    $data->gastos->total = $data->gastos->otros_gastos + $data->gastos->gastos_nomina;
                }
                break;
            case 'productos':
                $invoiceM = new Invoice();
                $invoiceM->select([
                    'line_invoices.products_id',
                    'concat(products.name, " - ", products.tax_iva) AS name_product',
                    'IFNULL(SUM(line_invoices.price_amount * line_invoices.quantity), 0) AS price_amount',
                    'IFNULL(SUM(line_invoices.cost_amount * line_invoices.quantity), 0) AS cost_amount',
                    'IFNULL(SUM(line_invoices.quantity), 0) AS quantity'
                ])
                ->join('line_invoices', 'line_invoices.invoices_id = invoices.id', 'inner')
                ->join('products', 'products.id = line_invoices.products_id', 'inner')
                ->whereIn('invoices.type_documents_id', [1, 2 ,3, 108]);
                
                $invoiceM->where('invoices.created_at >=', $this->request->getGet('start_date') ? $this->request->getGet('start_date').' 00:00:00' : date('Y-m-d H:i:s'));
                $invoiceM->where('invoices.created_at <=', $this->request->getGet('end_date') ? $this->request->getGet('end_date').' 23:59:59' : date('Y-m-d H:i:s'));
                if ($this->permi){
                    if($this->request->getGet('headquarters_providers')) $invoiceM->where(['invoices.companies_id' => $this->request->getGet('headquarters_providers')]);
                    if($this->request->getGet('user')) $invoiceM->where(['invoices.user_id' => $this->request->getGet('user')]);
                }else{
                    $invoiceM->where(['invoices.companies_id' => session('user')->companies_id]);
                    $invoiceM->where(['invoices.user_id' => session('user')->id]);
                }

                if ($this->request->getGet('orderBy')) {
                    switch ($this->request->getGet('orderBy')) {
                        case 'quantity':
                            $invoiceM->orderBy('quantity', $this->request->getGet('DESC'));
                            break;
                        case 'cost_amount':
                            $invoiceM->orderBy('cost_amount', $this->request->getGet('DESC'));
                            break;
                        case 'price_amount':
                            $invoiceM->orderBy('price_amount', $this->request->getGet('DESC'));
                            break;
                            
                        default:
                            $invoiceM->orderBy('(price_amount - cost_amount)', $this->request->getGet('DESC'));
                            break;
                    }
                }

                $invoiceM->groupBy('products.id')
                ->asObject();
                // ->orderBy('(SUM(line_invoices.price_amount) - SUM(line_invoices.cost_amount))', 'DESC')
                $data = (object)[
                    'products'  => $invoiceM->paginate(10),
                    'pager'     => $invoiceM->pager,
                    'permiso'   => $this->permi
                ];
                break;
        }

        // var_dump($data);die();

        // $data_aux = (object)[
        //     'CxP' => $CxP, 
        //     'CxC' => $CxC,
        //     'ventas' => $ventasT, 
        //     'ventasEfectivo' => $ventasEfectivo, 
        //     'ventasTransferencia' => $ventasTransferencia, 
        //     'ventasCredito' => $ventasCredito,
        //     'gastos' => $gastos,
        //     'totalVentas' => ($ventasT + $CxC),
        //     'totalGastos' => ($gastos + $CxP)
        // ];
        // var_dump($data_aux); die();
        // var_dump([$invoices, $wallets]);die();


        // echo json_encode($cost);die();
        // $type = (isset($_GET['type'])) ? $_GET['type'] : '';
        // switch ($type) {
        //     case 'ventas_old':
        //         $data = [
        //             'sell' => $paymentsMethod,
        //             'sellTotal' => $sellTotal,
        //             'pays' => $pays->first(),
        //             'bills' => [],
        //             'payments' => 0,
        //             'cost' => $costTotal,
        //             'headquarters' => $headquarters
        //         ];
        //         break;
        //     case 'gastos_old':
        //         $data = [
        //             'sell' => [],
        //             'sellTotal' => 0,
        //             'pays' => (object)['total' => 0],
        //             'bills' => $bills->get()->getResult(),
        //             'payments' => $paymentTotal,
        //             'cost' => $costTotal,
        //             'headquarters' => $headquarters
        //         ];
        //         break;
        //     case 'utilidad_old':
        //         $data = [
        //             'sell' => [],
        //             'sellTotal' => $sellTotal,
        //             'pays' => (object)['total' => 0],
        //             'bills' => $bills->get()->getResult(),
        //             'payments' => 0,
        //             'cost' => $costTotal,
        //             'headquarters' => $headquarters
        //         ];
        //         break;
        //     case 'ventas':
        //         $data = [
        //             'sell' => [],
        //             'sellTotal' => 0,
        //             'pays' => (object)['total' => 0],
        //             'bills' => [],
        //             'payments' => 0,
        //             'cost' => 0,
        //             'headquarters' => $headquarters
        //         ];
        //         break;
        //     default:
        //         $data = [
        //             'sell' => [],
        //             'sellTotal' => 0,
        //             'pays' => (object)['total' => 0],
        //             'bills' => [],
        //             'payments' => 0,
        //             'cost' => 0,
        //             'headquarters' => $headquarters
        //         ];
        //         break;
        // }
        // $data['data'] = $data;
        return view('report/sell', ['data' => $data, 'headquarters' => $headquarters, 'type_informe' => $type, 'users' => $usuarios]);
    }

    /**
     * @param Invoice $payments
     */
    private function extracted(Invoice $payments): void
    {
        if ($this->permi){
            if($this->request->getGet('headquarters_providers')) $payments->where(['invoices.companies_id' => $this->request->getGet('headquarters_providers')]);
            if($this->request->getGet('user')) $payments->where(['invoices.user_id' => $this->request->getGet('user')]);
        }else{
            $payments->where(['invoices.companies_id' => session('user')->companies_id]);
            $payments->where(['invoices.user_id' => session('user')->id]);
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
        $invoicesMax = $this->dataProvider(1)->countAllResults();
        $invoicesMax30 = $this->dataProvider(2)->countAllResults();
        $invoicesMax60 = $this->dataProvider(3)->countAllResults();
        $invoicesMax90 = $this->dataProvider(4)->countAllResults();
        $customers = $this->tableCustomers->whereIn('companies_id',  $this->controllerHeadquarters->idsCompaniesHeadquarters())->whereNotIn('name', ['gerente', 'Gerente'])->where(['type_customer_id' => 2])->asObject()->get()->getResult();
        $customers = $this->controllerCustomers->organization($customers);
        $data = [
            'invoices' => ['quantity' => $invoicesMax],
            'invoices30' => ['quantity' => $invoicesMax30],
            'invoices60' => ['quantity' => $invoicesMax60],
            'invoices90' => ['quantity' => $invoicesMax90],
            'customers' => $customers,
            'title' => 'Edades Proveedores'
        ];
        return view('report/customer_ages', $data);
    }

    public function dataProvider($id, $dataTable = false)
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
        $query = $this->caseDataAge($id);
        $invoices = $this->tableInvoices
            ->select([
                // 'invoices.id as id',
                // 'invoices.created_at as date',
                'customers.name as name',
                'customers.id as customer_id',
                // 'invoices.payable_amount as total',
                // 'companies.company as company',
                'SUM(invoices.payable_amount) as total',
                // 'companies.company as company',
                'COUNT(invoices.id) as total_invoices'
            ])
            ->join('customers', 'invoices.customers_id = customers.id')
            // ->join('companies', 'companies.id = invoices.companies_id')
            ->whereIn('invoices.type_documents_id', [101, 102, 107])
            ->whereIn('customers.type_customer_id', [2])
            ->whereIn('invoices.companies_id', $this->controllerHeadquarters->idsCompaniesHeadquarters());
        if(isset($dataTable->data->customer))
            $invoices->whereIn('invoices.customers_id', $dataTable->data->customer);
        if($dataTable){
            $invoices = $invoices
            ->where(['invoices.created_at >=' => isset($dataTable->data->date_init) ? $dataTable->data->date_init : date('Y-m-d', strtotime(date('Y-m-d') . "- 1 month"))])
            ->where(['invoices.created_at <=' => isset($dataTable->data->date_end) ? $dataTable->data->date_end.' 23:59:59' : date('Y-m-d 23:59:59')]);
        }else{
            if (!is_null($numberTwo)) {
                $invoices->where(['invoices.created_at >=' => $finalDate . ' 00:00:00', 'invoices.created_at <' => $currentDay . ' 23:59:59']);
            } else {
                $invoices->where(['invoices.created_at <' => $currentDay . ' 23:59:59']);
            }
        }
        $invoices = $invoices
            ->groupBy('invoices.customers_id');
            // ->get()->getResult();
        // foreach ($invoices as $item) {
        //     $item->py = $item->total;
            // $item->total = '$ ' . number_format($item->total, '2', ',', '.');
            // $item->action = '<div class="btn-group" role="group">
            //     <a href="' . base_url() . '/reports/view/' . $item->id . '" target="_top"
            //         class="btn btn-small green darken-1  tooltipped" data-position="top" data-tooltip="ver detalle">
            //         <i class="material-icons">insert_drive_file</i>
            //     </a>
            // </div>';
        // }
        return $invoices;
    }

    public function kardexP($id)
    {
        $dataTable = (object) [
            'draw'      => $_GET['draw'] ?? 1,
            'length'    => $length = $_GET['length'] ?? 10,
            'start'     => $start = $_GET['start'] ?? 1,
            'page'      => ceil(($start - 1) / $length + 1),
            'columns'   => $_GET['columns'] ?? [],
            'data'      => (object) $this->request->getGet()
        ];
        // $data = $this->getInvoices($dataTable)->asObject()->paginate($dataTable->length, 'dataTable', $dataTable->page);
        $data = $this->dataProvider($id, $dataTable)->asObject()->paginate($dataTable->length, 'dataTable', $dataTable->page);
        $total = $this->dataProvider($id, $dataTable)->countAllResults();
        return $this->respond([
            "recordsTotal" => $total, //$this->getInvoices($dataTable)->countAllResults(),
            "recordsFiltered" => $total, //$this->getInvoices($dataTable)->countAllResults(),
            'table' => $data,
            "draw" => $dataTable->draw,
            // 'algo' => isset($dataTable->data->customer) ? $dataTable->data->customer : $this->controllerHeadquarters->idsCompaniesHeadquarters()
        ], 200);
        return json_encode($this->dataProvider($id));
    }

    /**
     * casos y querys para edades cliente proveedores
     * @param $id
     * @return array|string[]
     */
    public function caseDataAge($id, $dataTable = false)
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
        $query = []; $queryIn = [];
        if($dataTable){
            if($dataTable->data->date_init){
                $query['invoices.created_at >='] = $dataTable->data->date_init . ' 00:00:00';
            }
            if($dataTable->data->date_end){
                $query ['invoices.created_at <='] = $dataTable->data->date_end . ' 23:59:59';
            }
            if (isset($dataTable->data->customer)) {
                $queryIn = $dataTable->data->customer;
            }
        }
        // else{
        //     if (!is_null($numberTwo)) {
        //         $query = ['invoices.created_at >=' => $finalDate . ' 00:00:00', 'invoices.created_at <' => $currentDay . ' 23:59:59'];
        //     } else {
        //         $query = ['invoices.created_at <' => $currentDay . ' 23:59:59'];
        //     }
        // }
        // if ($this->request->getGet('customer')) {
        //     $query = array_merge($query, ['invoices.customers_id' => $this->request->getGet('customer')]);
        // }
        // echo json_encode($query);die();
        return (object)['query' => $query, 'queryIn' => $queryIn];
    }
}