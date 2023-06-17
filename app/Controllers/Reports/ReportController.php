<?php


namespace App\Controllers\Reports;

require '../vendor/autoload.php';


use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\AccountingAcount;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\Product;
use App\Models\Resolution;
use App\Models\TypeDocument;
use App\Models\User;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ReportController extends BaseController
{
    public function index()
    {
        $accoutingAccount = new AccountingAcount();
        $accoutingAccounts = $accoutingAccount
            ->asObject()
            ->where(['companies_id' => Auth::querys()->companies_id])
            ->get()
            ->getResult();

        $typeDocument = new TypeDocument();
        $typeDocuments = $typeDocument->whereIn('id', [1, 2, 3, 4, 5])->asObject()->get()->getResult();

        if ($this->request->getJSON()) {


            $querys = ['invoices.companies_id' => Auth::querys()->companies_id];
            $querysOr = [];
            $datos = $this->request->getJSON();

            if (!empty($datos->customerId)) {
                $querys = array_merge($querys, ['customers.id' => $datos->customerId]);
            }
            if (!empty($datos->dataStart)) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $datos->dataStart . ' 00:00:00']);
                $session = session();
                $session->set('dateStart', $datos->dataStart);

            }
            if (!empty($datos->dataEnd)) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $datos->dataEnd . ' 23:59:59']);
                $session = session();
                $session->set('dateEnd', $datos->dataEnd);
            }
            if (count($datos->statusInvoice) != 0) {
                $querysOr = array_merge($querysOr, ['statusInvoice' => $datos->statusInvoice]);
            }
            if (count($datos->statusWallet) != 0) {
                $querysOr = array_merge($querysOr, ['statusWallet' => $datos->statusWallet]);
            }

            if (count($datos->typeDocumentId) != 0) {
                $querysOr = array_merge($querysOr, ['typeDocuments' => $datos->typeDocumentId]);
            }

            if (count($datos->accountId) != 0) {
                $querysOr = array_merge($querysOr, ['accounts' => $datos->accountId]);
            }

            if(!empty($datos->sellerId)) {
                $querys = array_merge($querys, ['invoices.seller_id' => $datos->sellerId]);
            }

            if(!empty($datos->userId)) {
                $querys = array_merge($querys, ['invoices.user_id' => $datos->userId]);
            }

            $session = session();
            $session->set('querys', $querys);
            $session = session();
            $session->set('querysOr', $querysOr);


        }


        $wallet = new Invoice();
        $wallet->select('
                invoices.id as invoices_id,
                invoices.resolution, 
                invoices.created_at, 
                invoices.payable_amount,
                invoices.status_wallet,
                invoices.id, 
                customers.name as customer_name,
                invoice_status.name as invoice_status_name,
                type_documents.name as type_document,
                invoices.tax_exclusive_amount,
                IFNULL(SUM(i.balance),0) as balance'
        )
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'line_invoices.products_id = products.id')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
            ->join('(SELECT invoices_id,  SUM(value) as balance FROM
                    wallet GROUP  BY invoices_id) i', 'invoices.id = i.invoices_id', 'left');
        $wallet->where('invoices.type_documents_id !=', 100);
        if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $wallet->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $wallet->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $wallet->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }

            if (isset(session('querysOr')['accounts'])) {
                $wallet->orWhereIn('products.iva', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }

        }
        if (!empty(session('querys'))) {
            $wallet->where(!empty(session('querys')) ? session('querys') : []);
        }else {
            $wallet->where([ 'invoices.companies_id' => Auth::querys()->companies_id]);
        }
        $wallets = $wallet->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5])
            ->asObject()
            ->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id');


        $wallet = new Invoice();
        $wallet->select('
                invoices.id as invoices_id,
                invoices.resolution, 
                invoices.created_at, 
                invoices.payable_amount,
                invoices.status_wallet,
                invoices.id, 
                customers.name as customer_name,
                invoice_status.name as invoice_status_name,
                type_documents.name as type_document,
                invoices.tax_exclusive_amount,
                IFNULL(SUM(i.balance),0) as balance'
        )
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'line_invoices.products_id = products.id')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
            ->join('(SELECT invoices_id,  SUM(value) as balance FROM
                    wallet GROUP  BY invoices_id) i', 'invoices.id = i.invoices_id', 'left');
        $wallet->where('invoices.type_documents_id !=', 100);
        if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $wallet->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $wallet->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $wallet->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr')['accounts'])) {
                $wallet->orWhereIn('products.iva', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }


        }
        if (!empty(session('querys'))) {
            $wallet->where(!empty(session('querys')) ? session('querys') : []);
        }else {
            $wallet->where([ 'invoices.companies_id' => Auth::querys()->companies_id]);
        }
        $wallets2 = $wallet->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5])
            ->asObject()
            ->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id');


        $data = [
            'accoutings' => $accoutingAccounts,
            'invoices' => $wallets->paginate(10),
            'pager' => $wallets->pager,
            'typeDocuments' => $typeDocuments,
            'count' => $wallets2->countAllResults()
        ];
        return view('report/index', $data);
    }

    public function reportSale($id)
    {
        $lineInvoice = new LineInvoice();
        $lineInvoices = $lineInvoice->select('
        line_invoices.id,
        invoices.resolution, 
        customers.identification_number, 
        customers.name as customer_name,
        products.entry_credit,
        products.entry_debit,
        products.name  as product_name,
        line_invoices.quantity,
        products.iva,
        products.retefuente,
        products.reteica,
        products.reteiva,
        invoices.notes,
        products.account_pay,
        line_invoices.price_amount,
        invoices.type_documents_id
        ')
            ->where(['invoices_id' => $id])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->asObject()
            ->get()
            ->getResult();

        $i = 0;


        $data = [];
        foreach ($lineInvoices as $item) {
            $l = 0;
            $account = [
                $item->entry_credit,
                $item->entry_debit,
                $item->iva,
                $item->retefuente,
                $item->reteica,
                $item->reteiva,
                $item->account_pay
            ];


            foreach ($account as $item2 => $key) {
                $accountigAccount = new AccountingAcount();
                $lineInvoiceTax = new LineInvoiceTax();
                $info = $accountigAccount->select('code, percent, name as account_name, nature, type_accounting_account_id')
                    ->where(['id' => $key])
                    ->asObject()
                    ->get()
                    ->getResult()[0];
                if (!empty($key)) {
                    switch ($item2) {
                        case '2':
                            $dataPercent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 1])
                                ->get()
                                ->getResult();
                            if (count($dataPercent) > 0) {
                                $info->percent = $dataPercent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                        case '3':
                            $dataPercent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 6])
                                ->get()
                                ->getResult();
                            if (count($dataPercent) > 0) {
                                $info->percent = $dataPercent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                        case '4':
                            $dataPercent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 7])
                                ->get()
                                ->getResult();
                            if (count($dataPercent) > 0) {
                                $info->percent = $dataPercent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                        case '5':
                            $dataPercent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 5])
                                ->get()
                                ->getResult();
                            if (count($dataPercent) > 0) {
                                $info->percent = $dataPercent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                    }
                }
                $item->account[$l] = $info;

                $l++;
            }
            $data[$i] = $item;
            $i++;
        }

        $dates = [
            'lineInvoices' => $data
        ];


        return view('report/sale', $dates);
    }

    public function reportTax($id)
    {
        $lineInvoice = new LineInvoice();
        $lineInvoices = $lineInvoice->select('
        line_invoices.id,
        invoices.resolution, 
        customers.identification_number, 
        customers.name as customer_name,
        products.name as product_name,
        line_invoices.quantity,
        products.iva,
        products.retefuente,
        products.reteica,
        products.reteiva,
        line_invoices.price_amount,
        invoices.type_documents_id
        ')
            ->where(['invoices_id' => $id])
            ->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->asObject()
            ->get()
            ->getResult();

        $i = 0;

        $data = [];
        foreach ($lineInvoices as $item) {
            $l = 0;
            $account = [
                $item->iva,
                $item->retefuente,
                $item->reteica,
                $item->reteiva,
            ];


            foreach ($account as $item2 => $key) {
                $accountigAccount = new AccountingAcount();
                $lineInvoiceTax = new LineInvoiceTax();
                $info = $accountigAccount->select('code, name as account_name, percent, nature, type_accounting_account_id')
                    ->where(['id' => $key])
                    ->asObject()
                    ->get()
                    ->getResult()[0];
                if (!empty($key)) {
                    switch ($item2) {
                        case '0':
                            $percent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 1])
                                ->get()
                                ->getResult();
                            if ($percent) {
                                $info->percent = $percent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                        case '1':
                            $percent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 6])
                                ->get()
                                ->getResult();
                            if ($percent) {
                                $info->percent = $percent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                        case '2':
                            $percent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 7])
                                ->get()
                                ->getResult();
                            if ($percent) {
                                $info->percent = $percent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                        case '3':
                            $percent = $lineInvoiceTax->select('percent')
                                ->where(['line_invoices_id' => $item->id, 'taxes_id' => 5])
                                ->get()
                                ->getResult();
                            if ($percent) {
                                $info->percent = $percent[0]->percent;
                            } else {
                                $info->percent = 0;
                            }
                            break;
                    }
                }
                $lineInvoices[$i]->account[$l] = $info;


                $l++;
            }
            $data[$i] = $lineInvoices[$i];
            $i++;
        }

        $dates = [
            'lineInvoices' => $data
        ];


        return view('report/tax', $dates);
    }

    public function csv()
    {



        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        $wallet = new Invoice();
        $wallet->select('
                invoices.id as invoices_id,
                invoices.resolution, 
                invoices.created_at, 
                invoices.payable_amount,
                invoices.status_wallet,
                customers.name,
                invoice_status.name as invoice_status_name,
                type_documents.name as type_document,
                invoices.tax_exclusive_amount,
                invoices.uuid,
                invoices.notes,
                payment_forms.name as payment_forms_name,
                payment_methods.name as payment_methods_name,
                IFNULL(SUM(i.balance),0) as balance'
        )->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'line_invoices.products_id = products.id')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
            ->join('payment_methods', 'payment_methods.id = invoices.payment_methods_id', 'left')
            ->join('payment_forms', 'payment_forms.id = invoices.payment_forms_id', 'left')
            ->join('(SELECT invoices_id,  SUM(value) as balance FROM 
                    wallet GROUP  BY invoices_id) i', 'invoices.id = i.invoices_id', 'left');
        $wallet->where('invoices.type_documents_id !=', 100);

        if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $wallet->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $wallet->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $wallet->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr')['accounts'])) {
                $wallet->orWhereIn('products.iva', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }
        }
        if (!empty(session('querys'))) {
            $wallet->where(!empty(session('querys')) ? session('querys') : []);
        }else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $data['invoices.type_documents_id !=']= 100;
            $wallet->where($data);
        }


        $wallets = $wallet->orderBy('invoices.id', 'DESC')
            ->groupBy('invoices.id')
            ->asObject()
            ->get()
            ->getResult();

// Add some data
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '#')
            ->setCellValue('B1', 'Fecha')
            ->setCellValue('C1', 'Documento')
            ->setCellValue('D1', 'Cliente')
            ->setCellValue('E1', 'Estado')
            ->setCellValue('F1', 'Estado en Cartera')
            ->setCellValue('G1', 'Metodo de Pago')
            ->setCellValue('H1', 'Forma de Pago')
            ->setCellValue('I1', 'Impuesto')
            ->setCellValue('J1', 'Total')
            ->setCellValue('K1', 'UUID')
            ->setCellValue('L1', 'Notas');

        $i = 2;
        foreach ($wallets as $item) {

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $item->resolution)
                ->setCellValue('B' . $i, $item->created_at)
                ->setCellValue('C' . $i, $item->type_document)
                ->setCellValue('D' . $i, $item->name)
                ->setCellValue('E' . $i, $item->invoice_status_name)
                ->setCellValue('F' . $i, $item->status_wallet)
                ->setCellValue('G' . $i, $item->payment_methods_name)
                ->setCellValue('H' . $i, $item->payment_forms_name)
                ->setCellValue('I' . $i, number_format(($item->payable_amount - $item->tax_exclusive_amount), '2', '.', ','))
                ->setCellValue('J' . $i, number_format($item->payable_amount, '2', '.', ','))
                ->setCellValue('K' . $i, str_replace(["\r", "\n"], '', $item->uuid))
                ->setCellValue('L' . $i, strip_tags(str_replace(["\r", "\n", '&nbsp;', "", ':', ';'], '', $item->notes)));
            $i++;
        }


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->save("csv.csv");
        header("Content-disposition: attachment; filename=csv.csv");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-type: application/csv");
        readfile('csv.csv');
    }

    public function csvExportReportTax()
    {
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }


        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');


        $lineInvoice = new LineInvoice();
        $lineInvoice->select('
        line_invoices.id,
        invoices.resolution, 
        line_invoices.quantity,
        customers.identification_number, 
        customers.name as customer_name,
        products.iva,
        products.name as product_name,
        products.retefuente,
        products.reteica,
        products.reteiva,
        invoices.notes,
        line_invoices.price_amount,
        invoices.type_documents_id,
        type_documents.name as type_document,
        invoices.status_wallet,
        invoice_status.name as status
        ')->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id');
        $lineInvoice->where('invoices.type_documents_id !=', 100);

        if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $lineInvoice->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $lineInvoice->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $lineInvoice->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr')['accounts'])) {
                $lineInvoice->orWhereIn('products.iva', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }
        }
        if (!empty(session('querys'))) {
            $lineInvoice->where(!empty(session('querys')) ? session('querys') : []);
        }else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $data['invoices.type_documents_id !=']= 100;
            $lineInvoice->where($data);
        }

        $lineInvoices = $lineInvoice->asObject()
            ->orderBy('line_invoices.id', 'asc')
            ->get()
            ->getResult();

        $i = 0;

        $data = [];
        foreach ($lineInvoices as $item) {
            $l = 0;
            if ($this->request->getGet('account')) {
                $infoAccount = $this->request->getGet('account');
                $account = [
                    $item->iva == $infoAccount ? $item->iva : 0,
                    $item->retefuente == $infoAccount ? $item->retefuente : 0,
                    $item->reteica == $infoAccount ? $item->reteica : 0,
                    $item->reteiva == $infoAccount ? $item->reteiva : 0,
                ];
            } else {
                $account = [
                    $item->iva,
                    $item->retefuente,
                    $item->reteica,
                    $item->reteiva,
                ];
            }

            foreach ($account as $item2 => $key) {
                $lineInvoiceTax = new LineInvoiceTax();
                $accountigAccount = new AccountingAcount();
                $info = $accountigAccount->where(['id' => $key])
                    ->asObject()
                    ->get()
                    ->getResult();


                if (!empty($info) > 0) {
                    if (!empty($key)) {
                        switch ($item2) {
                            case '0':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 1])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }
                                break;
                            case '1':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 6])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }

                                break;
                            case '2':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 7])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }

                                break;
                            case '3':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 5])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }
                                break;
                        }
                    }
                    $lineInvoices[$i]->account[$l] = $info[0];

                    $l++;
                }
            }


            $data[$i] = $lineInvoices[$i];
            $i++;
        }


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '#')
            ->setCellValue('B1', 'Nit')
            ->setCellValue('C1', 'Nombre')
            ->setCellValue('D1', 'Producto')
            ->setCellValue('E1', 'Cantidad')
            ->setCellValue('F1', 'Estado documento')
            ->setCellValue('G1', 'Estado de cartera')
            ->setCellValue('H1', 'Tipo de documento')
            ->setCellValue('I1', 'CTA')
            ->setCellValue('J1', 'IVA')
            ->setCellValue('K1', 'Base')
            ->setCellValue('L1', '%')
            ->setCellValue('M1', 'Notas');

        $i = 2;


        foreach ($data as $item => $key) {
            if (isset($key->account) && count($key->account) > 0) {
                foreach ($key->account as $item2 => $value) {
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . $i, $key->resolution);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . $i, $key->identification_number);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . $i, $key->customer_name);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . $i, $key->product_name);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . $i, $key->quantity);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('F' . $i, $key->status);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . $i, $key->status_wallet);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, $key->type_document);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('I' . $i, $value->code . ' - ' . $value->name);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, number_format(($value->percent * $key->price_amount) / 100), '2', '.', ',');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . $i, number_format($key->price_amount, '2', '.', ','));
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('L' . $i, number_format($value->percent, '2', '.', ','));
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('M' . $i, strip_tags(str_replace(["\r", "\n", '&nbsp;', "", ':', ';'], '', $key->notes)));
                    $i++;
                }
            }

        }


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->save("reportTax.csv");
        header("Content-disposition: attachment; filename=reportTax.csv");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-type: application/csv");
        readfile('reportTax.csv');

    }

    public function csvExportReportSale()
    {

        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }



        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');


        $lineInvoice = new LineInvoice();
        $lineInvoice->select('
        line_invoices.id,
        line_invoices.quantity,
        invoices.resolution, 
        customers.identification_number, 
        customers.name as customer_name,
        products.name as product_name,
        products.entry_credit,
        products.entry_debit,
        products.iva,
        products.retefuente,
        products.reteica,
        products.reteiva,
        products.account_pay,
        line_invoices.price_amount,
        invoices.type_documents_id,
        invoices.notes,
        type_documents.name as type_document,
        invoices.status_wallet,
        invoice_status.name as status
        ')->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id');
        $lineInvoice->where('invoices.type_documents_id !=', 100);

        if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $lineInvoice->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $lineInvoice->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $lineInvoice->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr')['accounts'])) {
                $lineInvoice->orWhereIn('products.iva', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }
        }
        if (!empty(session('querys'))) {
            $lineInvoice->where(!empty(session('querys')) ? session('querys') : []);
        }else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $data['invoices.type_documents_id !=']= 100;
            $lineInvoice->where($data);
        }


        $lineInvoices = $lineInvoice->asObject()
            ->orderBy('line_invoices.id', 'desc')
            ->get()
            ->getResult();

        $i = 0;

        $data = [];
        foreach ($lineInvoices as $item) {
            $l = 0;
            if ($this->request->getGet('account')) {
                $infoAccount = $this->request->getGet('account');
                $account = [
                    $item->entry_credit == $infoAccount ? $item->entry_credit : 0,
                    $item->entry_debit == $infoAccount ? $item->entry_debit : 0,
                    $item->iva == $infoAccount ? $item->iva : 0,
                    $item->retefuente == $infoAccount ? $item->retefuente : 0,
                    $item->reteica == $infoAccount ? $item->reteica : 0,
                    $item->reteiva == $infoAccount ? $item->reteiva : 0,
                    $item->account_pay == $infoAccount ? $item->account_pay : 0,
                ];
            } else {
                $account = [
                    $item->entry_credit,
                    $item->entry_debit,
                    $item->iva,
                    $item->retefuente,
                    $item->reteica,
                    $item->reteiva,
                    $item->account_pay
                ];
            }


            foreach ($account as $item2 => $key) {
                $lineInvoiceTax = new LineInvoiceTax();
                $accountigAccount = new AccountingAcount();
                $info = $accountigAccount->where(['id' => $key])
                    ->asObject()
                    ->get()
                    ->getResult();


                if (!empty($info) > 0) {
                    if (!empty($key)) {
                        switch ($item2) {
                            case '2':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 1])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if (!empty($date)) {
                                    $info[0]->percent = $date[0]->percent;
                                }
                                break;
                            case '3':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 6])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if (!empty($date)) {
                                    $info[0]->percent = $date[0]->percent;
                                }

                                break;
                            case '4':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 7])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if (!empty($date)) {
                                    $info[0]->percent = $date[0]->percent;
                                }

                                break;
                            case '5':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 5])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if (!empty($date)) {
                                    $info[0]->percent = $date[0]->percent;
                                }
                                break;
                        }
                    }
                    $lineInvoices[$i]->account[$l] = $info[0];

                    $l++;
                }
            }


            $data[$i] = $lineInvoices[$i];
            $i++;
        }


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', '#')
            ->setCellValue('B1', 'Nit')
            ->setCellValue('C1', 'Nombre')
            ->setCellValue('D1', 'Producto')
            ->setCellValue('E1', 'Cantidad')
            ->setCellValue('F1', 'Estado documento')
            ->setCellValue('G1', 'Estado de cartera')
            ->setCellValue('H1', 'Tipo de documento')
            ->setCellValue('I1', 'CTA')
            ->setCellValue('J1', 'Débito')
            ->setCellValue('K1', 'Crédito')
            ->setCellValue('L1', 'Nota');

        $i = 2;

        $debit = 0;
        $credit = 0;
        $resolution = 0;

        foreach ($data as $item => $key) {

            $impuetos = 0;
            $retencion = 0;
            if (isset($key->account) && count($key->account) > 0) {
                foreach ($key->account as $item2 => $value) {


                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . $i, $key->resolution);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . $i, $key->identification_number);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . $i, $key->customer_name);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . $i, $key->product_name);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . $i, $key->quantity);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('F' . $i, $key->status);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . $i, $key->status_wallet);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, $key->type_document);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('I' . $i, $value->name . ' - ' . $value->code);

                    if ($key->type_documents_id == 1 || $key->type_documents_id == 5) {

                        if ($value->nature == 'Débito') {
                            if ($value->type_accounting_account_id == 4) {
                                $debit += ($impuetos + $key->price_amount) - $retencion;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, number_format(($impuetos + $key->price_amount) - $retencion, '2', '.', ','));
                            } else {
                                $debit += $key->price_amount * $value->percent / 100;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, number_format($key->price_amount * $value->percent / 100, '2', '.', ','));
                            }
                        } else {
                            $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, 0);
                        }

                        if ($value->nature == 'Crédito') {
                            if ($value->type_accounting_account_id == 1 || $value->type_accounting_account_id == 4) {
                                $credit += $key->price_amount;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . $i, number_format($key->price_amount, '2', '.', ','));
                            } else {
                                $credit += $key->price_amount * $value->percent / 100;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . $i, number_format($key->price_amount * $value->percent / 100, '2', '.', ','));
                            }
                        } else {
                            $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . $i, 0);
                        }

                    } else if ($key->type_documents_id == 4) {
                        if ($value->nature != 'Débito') {
                            if ($value->type_accounting_account_id == 1 || $value->type_accounting_account_id == 4) {
                                $debit += $key->price_amount;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, number_format($key->price_amount, '2', '.', ','));
                            } else {
                                $debit += $key->price_amount * $value->percent / 100;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, number_format($key->price_amount * $value->percent / 100, '2', '.', ','));
                            }
                        } else {
                            $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, 0);
                        }

                        if ($value->nature == 'Crédito') {
                            if ($value->type_accounting_account_id == 4) {
                                $credit += ($impuetos + $key->price_amount) - $retencion;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . $i, number_format(($impuetos + $key->price_amount) - $retencion), '2', '.', ',');
                            } else {
                                $credit += $key->price_amount * $value->percent / 100;
                                $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . $i, number_format($key->price_amount * $value->percent / 100, '2', '.', ','));
                            }
                        } else {
                            $spreadsheet->setActiveSheetIndex(0)->setCellValue('K' . $i, 0);
                        }

                    }
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('L' . $i, strip_tags(str_replace(["\r", "\n", '&nbsp;', "", ':', ';'], '', $key->notes)));
                    if ($value->type_accounting_account_id == 2):
                        $impuetos += $key->price_amount * $value->percent / 100;
                    elseif ($value->type_accounting_account_id == 3):
                        $retencion += $key->price_amount * $value->percent / 100;
                    endif;


                    $i++;

                }
            }


        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->save('reportSale.csv');
        header("Content-disposition: attachment; filename=reportSale.csv");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-type: application/csv");
        readfile('reportSale.csv');

    }

    public function csvExportHelisa()
    {

      $model = new Invoice();
      $model->select([
          'resolution',
          'invoices.id as invoice_id',
          'products.description',
          'iva.name as iva_name',
          'iva.code as iva_code',
          'iva.nature as iva_nature',
          'retefuente.name as retefuente_name',
          'retefuente.nature as retefuente_nature',
          'retefuente.code as retefuente_code',
          'reteica.name as reteica_name',
          'reteica.nature as reteica_nature',
          'reteica.code as reteica_code',
          'reteiva.name as reteiva_name',
          'reteiva.nature as reteiva_nature',
          'reteiva.code as reteiva_code',
          'invoices.payable_amount',
          'account_pay.code as account_pay_code',
          'account_pay.name as account_pay_name',
          'account_pay.name as account_pay_nature',
          'line_invoices.id as line_invoice_id',
          'line_invoices.line_extension_amount',
          'entry_credit.code as entry_credit_code',
          'entry_debit.code as entry_debit_code',
          'invoices.type_documents_id',
          'invoices.created_at',
          'customers.identification_number'
      ])->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
          ->join('products', 'products.id = line_invoices.products_id', 'left')
          ->join('customers', 'invoices.customers_id = customers.id', 'left')
          ->join('accounting_account as iva', 'iva.id = products.iva', 'left')
          ->join('accounting_account as retefuente', 'retefuente.id = products.retefuente', 'left')
          ->join('accounting_account as reteica', 'reteica.id = products.reteica', 'left')
          ->join('accounting_account as account_pay', 'account_pay.id = products.account_pay', 'left')
          ->join('accounting_account as entry_credit', ' entry_credit.id = products.entry_credit', 'left')
          ->join('accounting_account as entry_debit', 'entry_debit.id = products.entry_debit', 'left')
         ->join('accounting_account as reteiva', 'reteiva.id = products.reteiva', 'left')
          ->where([
              'invoices.invoice_status_id >' => 1,
              'invoices.type_documents_id <=' => 5
          ]);


      if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $model->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $model->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $model->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr')['accounts'])) {
                $model->orWhereIn('products.iva', session('querysOr')['accounts']);
                $model->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $model->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $model->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }
        }
        if (!empty(session('querys'))) {
            $model->where(!empty(session('querys')) ? session('querys') : []);
        } else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $model->where($data);
        }

        $invoices = $model->asObject()
            ->get()
            ->getResult();





      $arrayAccount = [];

      $i = 0;
      foreach ($invoices as $item) {

        $model = new LineInvoice();
        $lineInvoices = $model->join('line_invoice_taxs', 'line_invoices.id = line_invoice_taxs.line_invoices_id')
              ->where(['line_invoices.id' => $item->line_invoice_id])
              ->asObject()
              ->get()
              ->getResult();



          $account        = [];

          $account[$item->account_pay_code] = [
              'type_document_id'=> $item->type_documents_id,
              'name'            => $item->account_pay_name.' '.$item->resolution,
              'value'           => (double) $item->payable_amount,
              'nature'          =>  'Débito',
              'created_at'      => $item->created_at,
              'customer_id'     => $item->identification_number,
              'resolution'      => $item->resolution,
              'total'           =>  true
          ];


          $retention = 0;
          foreach ($lineInvoices as $item2) {
                  switch($item2->taxes_id) {
                      case '1':

                              $account[$item->iva_code] = [
                                  'type_document_id'=> $item->type_documents_id,
                                  'name'            => $item->iva_name,
                                  'value'           => (double) $item2->tax_amount,
                                  'nature'          => $item->iva_nature,
                                  'created_at'      => $item->created_at,
                                  'customer_id'     => $item->identification_number,
                                  'resolution'      => $item->resolution
                              ];



                          break;
                      case '5':
                          $account[$item->reteiva_code] = [
                              'type_document_id'=> $item->type_documents_id,
                              'name'            => $item->reteiva_name,
                              'value'           => (double) $item2->tax_amount,
                              'nature'          => $item->reteiva_nature,
                              'created_at'      => $item->created_at,
                              'customer_id'     => $item->identification_number,
                              'resolution'      => $item->resolution
                          ];
                          $retention += (double) $item2->tax_amount;
                          break;
                      case '6':
                              $account[$item->retefuente_code] = [
                                  'type_document_id'=> $item->type_documents_id,
                                  'name'            => $item->retefuente_name,
                                  'value'           => (double) $item2->tax_amount,
                                  'nature'          => $item->retefuente_nature,
                                  'created_at'      => $item->created_at,
                                  'customer_id'     => $item->identification_number,
                                  'resolution'      => $item->resolution
                              ];
                              $retention += (double) $item2->tax_amount;
                          break;
                      case '7':
                              $account[$item->reteica_code] = [
                                  'type_document_id'=> $item->type_documents_id,
                                  'name'            => $item->reteica_name,
                                  'value'           =>  (double) $item2->tax_amount,
                                  'nature'          => $item->reteica_nature,
                                  'created_at'      => $item->created_at,
                                  'customer_id'     => $item->identification_number,
                                  'resolution'      => $item->resolution
                              ];
                              $retention += (double) $item2->tax_amount;
                          break;
                }
          }
          //Productos
          $account[($item->type_documents_id == 1 ? $item->entry_credit_code : $item->entry_debit_code) . '-' . $i++] = [
              'type_document_id'    => $item->type_documents_id,
              'name'                => $item->description,
              'value'               => (double)$lineInvoices[0]->line_extension_amount,
              'created_at'          => $item->created_at,
              'nature'              => $item->type_documents_id == 1 ? 'Crédito': 'Dèbito',
              'customer_id'         => $item->identification_number,
              'resolution'          => $item->resolution
          ];

          $account[$item->account_pay_code]['value'] -= $retention;
          array_push($arrayAccount, [ 'invoices_id' => $item->invoice_id, 'accounts' =>  $account]);
      }


      $accountExist = [];
      $groupAccount = [];
      $invoiceExist = [];
      foreach ($arrayAccount as $value) {

          if (in_array($value['invoices_id'], $invoiceExist)){
            foreach ($value['accounts'] as $value2 => $key) {
                if(!isset($key['total'])) {
                    if(in_array($value2, $accountExist)){
                        if(!empty($value2)) {
                            $groupAccount[$value['invoices_id']][$value2]['value'] += (double) $key['value'];
                        }
                    }else {
                        if(!empty($value2)) {
                            $groupAccount[$value['invoices_id']][$value2] = $key;
                            array_push($accountExist, $value2);
                        }

                    }
                }
            }

          }else {
              foreach ($value['accounts'] as $value2 => $key) {
                  if(!empty($value2)) {
                      $groupAccount[$value['invoices_id']][$value2] = $key;
                      array_push($accountExist, $value2);
                  }
              }
          }


      }







        /*if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }*/
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'TIPO DOCUMENTO')
            ->setCellValue('B1', 'NUMERO DOCUMENTO')
            ->setCellValue('C1', 'FECHA')
            ->setCellValue('D1', 'CUENTA')
            ->setCellValue('E1', 'CONCEPTO')
            ->setCellValue('F1', 'VALOR')
            ->setCellValue('G1', 'NATURALEZA')
            ->setCellValue('H1', 'IDENTIDAD TERCERO')
            ->setCellValue('I1', 'DOCUMENTO FUENTE');

        $i = 2;
        foreach ($groupAccount as $item) {
            foreach ($item as $data => $key) {
                $type = '';
                switch ($key['type_document_id']) {
                    case '1':
                        $type = 'FV';
                        break;
                    case '4':
                        $type = 'NC';
                        break;
                    case '5':
                        $type = 'ND';
                        break;
                }
                if($key['value'] != 0) {
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . $i, $type);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . $i, $key['resolution']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . $i, $key['created_at']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . $i, explode('-', $data)[0]);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . $i, $key['name']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('F' . $i, number_format($key['value'], '2', '.', ','));
                    if ($key['nature'] == 'Crédito') {
                        $nature = 'C';
                    } else {
                        $nature = 'D';
                    }
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . $i, $nature);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, $key['customer_id']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('I' . $i, '');
                    $i++;
                }
            }
        }
















       /* $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');


        $lineInvoice = new LineInvoice();
        $lineInvoice->select('
        line_invoices.id,
        invoices.resolution, 
        line_invoices.quantity,
        customers.identification_number, 
        customers.name as customer_name,
        products.iva,
        products.name as product_name,
        products.retefuente,
        products.reteica,
        products.reteiva,
        invoices.notes,
        invoices.created_at,
        line_invoices.price_amount,
        invoices.type_documents_id,
        type_documents.prefix as type_document,
        invoices.status_wallet,
        invoice_status.name as status
        ')->join('invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
            ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id');
        $lineInvoice->where('invoices.type_documents_id !=', 100);
        if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $lineInvoice->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $lineInvoice->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $lineInvoice->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr')['accounts'])) {
                $lineInvoice->orWhereIn('products.iva', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $lineInvoice->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }
        }
        if (!empty(session('querys'))) {
            $lineInvoice->where(!empty(session('querys')) ? session('querys') : []);
        } else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $data['invoices.type_documents_id !=']= 100;
            $lineInvoice->where($data);
        }

        $lineInvoices = $lineInvoice->asObject()
            ->orderBy('line_invoices.id', 'asc')
            ->get()
            ->getResult();
        $i = 0;
        $data = [];
        foreach ($lineInvoices as $item) {
            $l = 0;
            if ($this->request->getGet('account')) {
                $infoAccount = $this->request->getGet('account');
                $account = [
                    $item->iva == $infoAccount ? $item->iva : 0,
                    $item->retefuente == $infoAccount ? $item->retefuente : 0,
                    $item->reteica == $infoAccount ? $item->reteica : 0,
                    $item->reteiva == $infoAccount ? $item->reteiva : 0,
                ];
            } else {
                $account = [
                    $item->iva,
                    $item->retefuente,
                    $item->reteica,
                    $item->reteiva,
                ];
            }

            foreach ($account as $item2 => $key) {
                $lineInvoiceTax = new LineInvoiceTax();
                $accountigAccount = new AccountingAcount();
                $info = $accountigAccount->where(['id' => $key])
                    ->asObject()
                    ->get()
                    ->getResult();


                if (!empty($info) > 0) {
                    if (!empty($key)) {
                        switch ($item2) {
                            case '0':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 1])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }
                                break;
                            case '1':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 6])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }

                                break;
                            case '2':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 7])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }

                                break;
                            case '3':
                                $date = $lineInvoiceTax->select('percent')
                                    ->where(['line_invoices_id' => $item->id, 'taxes_id' => 5])
                                    ->asObject()
                                    ->get()
                                    ->getResult();
                                if ($date) {
                                    $info[0]->percent = $date[0]->percent;
                                } else {
                                    $info[0]->percent = 0;
                                }
                                break;
                        }
                    }
                    $lineInvoices[$i]->account[$l] = $info[0];

                    $l++;
                }
            }


            $data[$i] = $lineInvoices[$i];
            $i++;
        }


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'TIPO DOCUMENTO')
            ->setCellValue('B1', 'NUMERO DOCUMENTO')
            ->setCellValue('C1', 'FECHA')
            ->setCellValue('D1', 'CUENTA')
            ->setCellValue('E1', 'CONCEPTO')
            ->setCellValue('F1', 'VALOR')
            ->setCellValue('G1', 'NATURALEZA')
            ->setCellValue('H1', 'IDENTIDAD TERCERO')
            ->setCellValue('I1', 'DOCUMENTO FUENTE');

        $i = 2;


        foreach ($data as $item => $key) {
            if (isset($key->account) && count($key->account) > 0) {
                foreach ($key->account as $item2 => $value) {
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . $i, strtoupper($key->type_document));
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . $i, $key->resolution);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . $i, $key->created_at);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . $i, $value->code);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . $i, $key->product_name);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('F' . $i, number_format(($key->price_amount * $value->percent) / 100, '2', '.', ','));
                    if ($value->nature == 'Crédito') {
                        $nature = 'C';
                    } else {
                        $nature = 'D';
                    }
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . $i, $nature);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, $key->identification_number);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('I' . $i, '');
                    $i++;
                }
            }

        }
*/

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
        $writer->save("reportTax.csv");
        header("Content-disposition: attachment; filename=reportTax.csv");
        header('Content-Type: application/vnd.ms-excel');
        header("Content-type: application/csv");
        readfile('reportTax.csv');

    }

    public function csvWordOffice()
    {


        $wallet = new Invoice();
        $wallet->select('
                companies.company,
                type_documents.prefix as type_document_prefix,
                invoices.id as invoices_id,
                invoices.resolution, 
                invoices.resolution_id,
                invoices.resolution_credit,
                customers.identification_number as customer_identification_number,
                invoices.created_at,
                companies.identification_number,
                invoices.notes,
                payment_forms.name as payment_forms_name,
                line_invoices.quantity,
                line_invoices.line_extension_amount,
                line_invoices.discount_amount,
                invoices.payment_due_date,
                line_invoices.products_id,
                line_invoices.id as line_invoices_id,
                type_currencies.code as code_currency,
                invoices.calculationrate'
        )->join('customers', 'customers.id = invoices.customers_id')
            ->join('line_invoices', 'invoices.id = line_invoices.invoices_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('products', 'line_invoices.products_id = products.id')
            ->join('companies', 'invoices.companies_id = companies.id')
            ->join('type_currencies', 'invoices.idcurrency = type_currencies.id')
            ->join('payment_forms', 'payment_forms.id = invoices.payment_forms_id', 'left');

        if (!empty(session('querysOr'))) {
            if (isset(session('querysOr')['statusInvoice'])) {
                $wallet->whereIn('invoices.invoice_status_id', session('querysOr')['statusInvoice']);
            }
            if (isset(session('querysOr')['statusWallet'])) {
                $wallet->whereIn('invoices.status_wallet', session('querysOr')['statusWallet']);
            }

            if (isset(session('querysOr')['typeDocuments'])) {
                $wallet->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr')['accounts'])) {
                $wallet->orWhereIn('products.iva', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.retefuente', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteica', session('querysOr')['accounts']);
                $wallet->orWhereIn('products.reteiva', session('querysOr')['accounts']);
            }
        }
        if (!empty(session('querys'))) {
            $wallet->where(!empty(session('querys')) ? session('querys') : []);
        }else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $data['invoices.type_documents_id !=']= 100;
            $wallet->where($data);
        }


        $wallets = $wallet->orderBy('invoices.id', 'DESC')
            // ->groupBy('invoices.id')
            ->asObject()
            ->get()
            ->getResult();


        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);

            return;
        }

        $spreadsheet = new Spreadsheet();


        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('Office 2007 XLSX Test Document')
            ->setSubject('Office 2007 XLSX Test Document')
            ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
            ->setKeywords('office 2007 openxml php')
            ->setCategory('Test result file');

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Empresa')
            ->setCellValue('B1', 'Tipo Documento')
            ->setCellValue('C1', 'Prefijo')
            ->setCellValue('D1', 'DocumentoNúmero')
            ->setCellValue('E1', 'Fecha')
            ->setCellValue('F1', 'Tercero Interno')
            ->setCellValue('G1', 'Tercero Externo')
            ->setCellValue('H1', 'Nota')
            ->setCellValue('I1', 'FormaDePago')
            ->setCellValue('J1', 'FechaEntrega')
            ->setCellValue('K1', 'Moneda')
            ->setCellValue('L1', 'TRM')
            ->setCellValue('M1', 'Verificado') //  = 0;
            ->setCellValue('N1', 'Anulado') // = 0
            ->setCellValue('O1', 'Personalizado1')
            ->setCellValue('P1', 'Personalizado2')
            ->setCellValue('Q1', 'Personalizado3')
            ->setCellValue('R1', 'Personalizado4')
            ->setCellValue('S1', 'Personalizado5')
            ->setCellValue('T1', 'Personalizado6')
            ->setCellValue('U1', 'Personalizado7')
            ->setCellValue('V1', 'Personalizado8')
            ->setCellValue('W1', 'Personalizado9')
            ->setCellValue('X1', 'Personalizado10')
            ->setCellValue('Y1', 'Personalizado11')
            ->setCellValue('Z1', 'Personalizado12')
            ->setCellValue('AA1', 'Personalizado13')
            ->setCellValue('AB1', 'Personalizado14')
            ->setCellValue('AC1', 'Personalizado15')
            ->setCellValue('AD1', 'Importacion')
            ->setCellValue('AE1', 'Producto')
            ->setCellValue('AF1', 'Bodega')// Principal
            ->setCellValue('AG1', 'UnidadDeMedida') //Und.
            ->setCellValue('AH1', 'Cantidad')
            ->setCellValue('AI1', 'Iva')
            ->setCellValue('AJ1', 'Valor')
            ->setCellValue('AK1', 'Descuento')
            ->setCellValue('AL1', 'Vencimiento')
            ->setCellValue('AM1', 'Nota Detalle')
            ->setCellValue('AN1', 'Centro Costos')
            ->setCellValue('AO1', 'Moneda Det')
            ->setCellValue('AP1', 'TRM Det')
            ->setCellValue('AQ1', 'Personalizado1Det')
            ->setCellValue('AR1', 'Personalizado2Det')
            ->setCellValue('AS1', 'Personalizado3Det')
            ->setCellValue('AT1', 'Personalizado4Det')
            ->setCellValue('AU1', 'Personalizado5Det')
            ->setCellValue('AV1', 'Personalizado6Det')
            ->setCellValue('AW1', 'Personalizado7Det')
            ->setCellValue('AX1', 'Personalizado8Det')
            ->setCellValue('AY1', 'Personalizado9Det')
            ->setCellValue('AZ1', 'Personalizado10Det')
            ->setCellValue('BA1', 'Personalizado11Det')
            ->setCellValue('BB1', 'Personalizado12Det')
            ->setCellValue('BC1', 'Personalizado13Det')
            ->setCellValue('BD1', 'Personalizado14Det')
            ->setCellValue('BE1', 'Personalizado15Det');

        $i = 2;
        foreach ($wallets as $item) {

            $resolution = new Resolution();
            $product = new Product();


            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $item->company);

            switch ($item->type_document_prefix) {
                case('fv'):
                    $type_document = 'fv';
                    $resolutions = $resolution->where([
                        'resolution' => $item->resolution_id,
                        'companies_id' => session('user')->companies_id,
                        'type_documents_id' => 1
                    ])
                        ->get()
                        ->getResult();

                    $products = $product->select('accounting_account.code')
                        ->join('accounting_account', 'products.entry_credit = accounting_account.id')
                        ->where(['products.id' => $item->products_id])
                        ->get()
                        ->getResult();
                    break;
                case('nc'):
                    $type_document = 'nc';
                    $resolutions = $resolution->where([
                        'companies_id' => session('user')->companies_id,
                        'type_documents_id' => 4
                    ])
                        ->get()
                        ->getResult();
                    $products = $product->select('accounting_account.code')
                        ->join('accounting_account', 'products.entry_debit = accounting_account.id')
                        ->where(['products.id' => $item->products_id])
                        ->get()
                        ->getResult();
                    break;
                case('nd'):
                    $type_document = 'nd';
                    $resolutions = $resolution->where([
                        'companies_id' => session('user')->companies_id,
                        'type_documents_id' => 5
                    ])->get()->getResult();
                    $products = $product->select('accounting_account.code')
                        ->join('accounting_account', 'products.entry_credit = accounting_account.id')
                        ->where(['products.id' => $item->products_id])->get()->getResult();
                    break;
            }


            $lineInvoicesTax = new LineInvoiceTax();
            $lineInvoicesTaxs = $lineInvoicesTax
                ->select('percent')
                ->where(
                    [
                        'line_invoices_id' => $item->line_invoices_id,
                        'taxes_id' => 1
                    ])
                ->get()
                ->getResult();

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('B' . $i, $type_document);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('C' . $i, count($resolutions) == 0 ? '' : $resolutions[0]->prefix);

            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('D' . $i, $item->resolution)
                ->setCellValue('E' . $i, formatDate($item->created_at))
                ->setCellValue('F' . $i, $item->identification_number)
                ->setCellValue('G' . $i, $item->customer_identification_number)
                ->setCellValue('H' . $i, strip_tags($item->notes))
                ->setCellValue('I' . $i, $item->payment_forms_name)
                ->setCellValue('J' . $i, '');


            //nota credito y debito prefix fv

                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('K' . $i,'')
                    ->setCellValue('L' . $i, '');



            $spreadsheet->setActiveSheetIndex(0)->setCellValue('M' . $i, '0') //  = 0;
            ->setCellValue('N' . $i, '0') // = 0
            ->setCellValue('O' . $i, '')
                ->setCellValue('P' . $i, '')
                ->setCellValue('Q' . $i, '')
                ->setCellValue('R' . $i, '')
                ->setCellValue('S' . $i, '')
                ->setCellValue('T' . $i, '')
                ->setCellValue('U' . $i, '')
                ->setCellValue('V' . $i, '')
                ->setCellValue('W' . $i, '')
                ->setCellValue('X' . $i, '')
                ->setCellValue('Y' . $i, '')
                ->setCellValue('Z' . $i, '')
                ->setCellValue('AA' . $i, '')
                ->setCellValue('AB' . $i, '')
                ->setCellValue('AC' . $i, '')
                ->setCellValue('AD' . $i, '')
                ->setCellValue('AE' . $i, count($products) > 0 ? $products[0]->code : '')
                ->setCellValue('AF' . $i, 'Principal') //cuenta contable ingreso
                ->setCellValue('AG' . $i, 'Und.')// Principal
                ->setCellValue('AH' . $i, $item->quantity) //Und.
                ->setCellValue('AI' . $i, count($lineInvoicesTaxs) > 0 ? $lineInvoicesTaxs[0]->percent / 100 : '')
                ->setCellValue('AJ' . $i, $item->line_extension_amount)
                ->setCellValue('AK' . $i, $item->discount_amount)
                ->setCellValue('AL' . $i, formatDate($item->payment_due_date))
                ->setCellValue('AM' . $i, '')
                ->setCellValue('AN' . $i, '')
                ->setCellValue('AO' . $i, '')
                ->setCellValue('AP' . $i, '')
                ->setCellValue('AQ' . $i, '')
                ->setCellValue('AR' . $i, '')
                ->setCellValue('AS' . $i, '')
                ->setCellValue('AT' . $i, '')
                ->setCellValue('AU' . $i, '')
                ->setCellValue('AV' . $i, '')
                ->setCellValue('AW' . $i, '')
                ->setCellValue('AX' . $i, '')
                ->setCellValue('AY' . $i, '')
                ->setCellValue('AZ' . $i, '')
                ->setCellValue('BA' . $i, '')
                ->setCellValue('BB' . $i, '')
                ->setCellValue('BC' . $i, '')
                ->setCellValue('BD' . $i, '');
            $i++;
        }


        /* $writer = new \PhpOffice\PhpSpreadsheet\Writer\Csv($spreadsheet);
         $writer->save('reportSale.csv');
         header("Content-disposition: attachment; filename=reportSale.csv");
         header('Content-Type: application/vnd.ms-excel');
         header("Content-type: application/csv");
         readfile('reportSale.csv');*/


        $spreadsheet->getActiveSheet()->setTitle('Simple');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="WordOffice.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
        exit;


    }

    public function reset()
    {
        $session = session();
        $session->set('querys', []);
        $session = session();
        $session->set('querysOr', []);
        $session->set('startDate', null);
        $session->set('endDate', null);
        return redirect()->to(base_url() . '/report');
    }

    private function _users($id)
    {
        $user = new User();
        return $user->where('id', $id)->get()->getResult();
    }
}