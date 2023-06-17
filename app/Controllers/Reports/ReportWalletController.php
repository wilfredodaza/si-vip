<?php


namespace App\Controllers\Reports;


use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\AccountingAcount;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\TypeDocument;
use App\Models\Wallet;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ReportWalletController extends BaseController
{
    public function index()
    {
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
                $session->set('dateStart_wallet', $datos->dataStart);

            }
            if (!empty($datos->dataEnd)) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $datos->dataEnd . ' 23:59:59']);
                $session = session();
                $session->set('dateEnd_wallet', $datos->dataEnd);
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
            $session->set('querys_wallet', $querys);
            $session = session();
            $session->set('querysOr_wallet', $querysOr);


        }
        $accoutingAccount = new AccountingAcount();
        $accoutingAccounts = $accoutingAccount
            ->asObject()
            ->where(['companies_id' => Auth::querys()->companies_id])
            ->get()
            ->getResult();

        $typeDocument = new TypeDocument();
        $typeDocuments = $typeDocument->whereIn('id', [1, 2, 3, 4, 5])->asObject()->get()->getResult();
        $invoice = new Invoice();
        $invoice->select([
            'invoices.resolution',
            'type_documents.name as type_documents',
            'customers.name as customer_name',
            'type_document_identifications.name as type_document_identification',
            'customers.identification_number',
            'invoices.status_wallet',
            'invoices.payable_amount',
            'invoices.created_at',
            'invoices.line_extesion_amount',
            'invoices.tax_inclusive_amount',
            'invoices.id',
            'invoices.payment_due_date',
            'invoices.resolution_credit',
            'invoices.type_documents_id',
            'invoice_status.name as invoice_status'
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id');


        if (!empty(session('querysOr_wallet'))) {
            if (isset(session('querysOr_wallet')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_wallet')['statusInvoice']);
            }
            if (isset(session('querysOr_wallet')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_wallet')['statusWallet']);
            }

/*
            if (isset(session('querysOr_wallet')['accounts'])) {
                $invoice->orWhereIn('products.iva', session('querysOr_wallet')['accounts']);
                $invoice->orWhereIn('products.retefuente', session('querysOr_wallet')['accounts']);
                $invoice->orWhereIn('products.reteica', session('querysOr_wallet')['accounts']);
                $invoice->orWhereIn('products.reteiva', session('querysOr_wallet')['accounts']);
            }*/
        }
        if (!empty(session('querys_wallet'))) {
            $data = !empty(session('querys_wallet')) ? session('querys_wallet') : [];
            $data['invoices.type_documents_id ='] = 1;
            $invoice->where($data);
        } else {
            $data['invoices.type_documents_id'] = 1;
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $invoice->where($data);
        }




        $invoices = $invoice->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5])
            ->asObject();

        $data = [
            'accoutings'    => $accoutingAccounts,
            'invoices'      => $invoices->paginate(10),
            'pager'         => $invoices->pager,
            'typeDocuments' => $typeDocuments,
        ];
        return view('reportGeneral/report_wallet', $data);
    }

    public function reset()
    {
        $session = session();
        $session->set('querys_wallet', []);
        $session = session();
        $session->set('querysOr_wallet', []);
        $session->set('dateStart_wallet', null);
        $session->set('dateEnd_wallet', null);
        return redirect()->to(base_url() . '/report_wallet');
    }

    public function excel()
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


        $company = new Company();
        $companies = $company
            ->join('type_document_identifications', 'type_document_identifications.id = companies.type_document_identifications_id')
            ->where([
                'companies.id' => Auth::querys()->companies_id,
            ])
            ->asObject()
            ->get()
            ->getResult()[0];


        if (session('dateStart_wallet')) {
            $dateStart = explode('-', session('dateStart_wallet'));
            $mesStart = mes($dateStart[1]);
        } else {
            $dateStart = explode('-', $companies->created_at);
            $mesStart = mes($dateStart[1]);
        }


        if (session('dateEnd_wallet')) {
            $dateEnd = explode('-', session('dateEnd_wallet'));
            $mesEnd = mes($dateEnd[1]);
        } else {
            $dateEnd = explode('-', date('Y-m-d'));
            $mesEnd = mes($dateEnd[1]);
        }


        $invoice = new Invoice();
        $invoice->select([
            'invoices.resolution',
            'type_documents.name as type_documents',
            'customers.name as customer_name',
            'type_document_identifications.name as type_document_identification',
            'customers.identification_number',
            'invoices.status_wallet',
            'invoices.payable_amount',
            'invoices.created_at',
            'invoices.line_extesion_amount',
            'invoices.tax_inclusive_amount',
            'invoices.id',
            'invoices.payment_due_date',
            'invoices.resolution_credit'
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id');


        if (!empty(session('querysOr_wallet'))) {
            if (isset(session('querysOr_wallet')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_wallet')['statusInvoice']);
            }
            if (isset(session('querysOr_wallet')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_wallet')['statusWallet']);
            }


            if (isset(session('querysOr_wallet')['accounts'])) {
                $invoice->orWhereIn('products.iva', session('querysOr_wallet')['accounts']);
                $invoice->orWhereIn('products.retefuente', session('querysOr_wallet')['accounts']);
                $invoice->orWhereIn('products.reteica', session('querysOr_wallet')['accounts']);
                $invoice->orWhereIn('products.reteiva', session('querysOr_wallet')['accounts']);
            }
        }
        if (!empty(session('querys_wallet'))) {
            $data = !empty(session('querys_wallet')) ? session('querys_wallet') : [];
            $data['invoices.type_documents_id'] = 1;
            $invoice->where($data);
        } else {
            $data['invoices.type_documents_id'] = 1;
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $invoice->where($data);
        }
	


        $invoices = $invoice->groupBy(['invoices.id'])
            ->asObject()
            ->get()
            ->getResult();

        //Encabezados
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Empresa')->getStyle('A2')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Identificación')->getStyle('A3')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', 'Fecha de reporte')->getStyle('A4')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', 'Fecha de generación')->getStyle('A5')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', 'Software de Facturación')->getStyle('A6')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B2', $companies->company)
            ->setCellValue('B3', ($companies->name . ' ' . number_format($companies->identification_number, 0, '.', '.') . (empty($company->dv) ? '' : '-' . $company->dv)))
            ->setCellValue('B4', 'Reporte de Cartera del ' .  explode(' ', $dateStart[2])[0] . ' de ' . $mesStart . ' de ' . $dateStart[0] . ' al ' . $dateEnd[2] . ' de ' . $mesEnd . ' de ' . $dateEnd[0])
            ->setCellValue('B5', date('Y-m-d H:i:s'))
            ->setCellValue('B6','MiFacturaLegal.com');

        $spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->getColor()->setARGB('FF2874A6');
        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->getColor()->setARGB('FF2874A6');
        $spreadsheet->setActiveSheetIndex(0)->getStyle('B6')->getFont()->setBold(true);


        //quitar cuadricula
        $spreadsheet->getActiveSheet()->setShowGridlines(false);

        //Columnas A8 Hasta Q8
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFD7DBDD',
                ],

            ]
        ];
        $spreadsheet->getActiveSheet()->getStyle('A8:R8')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(40);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A8', 'Tercero')
            ->setCellValue('B8', 'Tipo de identificación')
            ->setCellValue('C8', 'Número de identificación')
            ->setCellValue('D8', 'Tipo de documento')
            ->setCellValue('E8', 'Número de documento')
            ->setCellValue('F8', 'Fecha de factura')
            ->setCellValue('G8', 'Fecha de vencimiento')
            ->setCellValue('H8', 'Saldo por cobrar')
            ->setCellValue('I8', 'Estado de cartera')
            ->setCellValue('J8', 'Edad de cartera en días')
            ->setCellValue('K8', 'Corrientes')
            ->setCellValue('L8', 'De 0 a 30 días')
            ->setCellValue('M8', 'De 30 a 60 días')
            ->setCellValue('N8', 'De 60 a 90 días')
            ->setCellValue('O8', 'De 90 a 120 días')
            ->setCellValue('P8', 'De 120 a 180 días')
            ->setCellValue('Q8', 'De 180 a 365 días')
            ->setCellValue('R8', 'Mayores a 365 días');

        $i                      = 9;
        $total                  = 0;
        $totalCorriente         = 0;
        $total0entry30          = 0;
        $total30entry60         = 0;
        $total60entry90         = 0;
        $total90entry120        = 0;
        $total120entry180       = 0;
        $total180entry365       = 0;
        $total365               = 0;
        foreach ($invoices as $invoice) {

            $invoiceCredits = new Invoice();
            $invoiceCredit = $invoiceCredits->select('invoices.*')
                ->where(['invoices.resolution_credit' => $invoice->resolution, 'invoices.companies_id' => Auth::querys()->companies_id])
                ->asObject()
                ->get()
                ->getResult();

            $lineInvoice = new LineInvoice();
            $lineInvoices = $lineInvoice->select('line_invoice_taxs.*, line_invoices.products_id, line_invoices.line_extension_amount, products.free_of_charge_indicator')
                ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
                ->join('products', 'products.id = line_invoices.products_id')
                ->where(['line_invoices.invoices_id' => $invoice->id])
                ->asObject()
                ->get()
                ->getResult();

            $reteFuente = 0;
            $reteIVA    = 0;
            $reteICA    = 0;
            $product    = 0;
            $free       = 0;

            foreach ($lineInvoices as $lineInvoice) {
                switch ($lineInvoice->taxes_id) {
                    case 5:
                        $reteIVA += $lineInvoice->tax_amount;
                        break;
                    case 7:
                        $reteICA += $lineInvoice->tax_amount;
                        break;
                    case 6:
                        $reteFuente += $lineInvoice->tax_amount;
                        break;
                }

                if ($product != $lineInvoice->products_id) {
                    if ($lineInvoice->free_of_charge_indicator == 'true') {
                        $free += $lineInvoice->line_extension_amount;
                    }
                    $product = $lineInvoice->products_id;
                }
            }

            $credit = 0;
            foreach ($invoiceCredit as $itemCredit) {
                $credit += $itemCredit->payable_amount - ($reteICA + $reteIVA + $reteFuente);
            }


            $wallet = new Wallet();
            $wallets = $wallet->select('sum(value) as value')
                ->where(['invoices_id' => $invoice->id])
                ->groupBy(['invoices_id'])
                ->get()
                ->getResult();


            $date1                  = new \DateTime($invoice->payment_due_date);
            $date2                  = new \DateTime(date('Y-m-d'));
            $interval 	            = $date1->diff($date2);
            $daysDiff 	            =  str_replace ('+',  '', $interval->format('%R%a'));



            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $invoice->customer_name)
                ->setCellValue('B' . $i, $invoice->type_document_identification)
                ->setCellValue('C' . $i, $invoice->identification_number)
                ->setCellValue('D' . $i, $invoice->type_documents)
                ->setCellValue('E' . $i, $invoice->resolution)
                ->setCellValue('F' . $i, $invoice->created_at)
                ->setCellValue('G' . $i, $invoice->payment_due_date)
                ->setCellValue('H' . $i, count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)))
                ->setCellValue('I' . $i, $invoice->status_wallet)
                ->setCellValue('J' . $i, $daysDiff)
                ->setCellValue('K' . $i, ($daysDiff <= 0) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '')
                ->setCellValue('L' . $i, ($daysDiff > 0 && $daysDiff <= 30) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '')
                ->setCellValue('M' . $i, ($daysDiff > 30 && $daysDiff <= 60) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free )) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '')
                ->setCellValue('N' . $i, ($daysDiff > 60 && $daysDiff <= 90) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free )) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '')
                ->setCellValue('O' . $i, ($daysDiff > 90 && $daysDiff <= 120) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free )) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '')
                ->setCellValue('P' . $i, ($daysDiff > 120 && $daysDiff <= 180) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '')
                ->setCellValue('Q' . $i, ($daysDiff > 180 && $daysDiff <= 365) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '')
                ->setCellValue('R' . $i, ($daysDiff > 365) ? count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free)) : '');
            $i++;

            if($daysDiff <= 0) {
                $totalCorriente += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            if($daysDiff > 0 && $daysDiff <= 30) {
                $total0entry30 += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            if($daysDiff > 30 && $daysDiff <= 60) {
                $total30entry60 += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            if($daysDiff > 60 && $daysDiff <= 90) {
                $total60entry90 += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            if($daysDiff > 90 && $daysDiff <= 120) {
                $total90entry120 += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            if($daysDiff > 120 && $daysDiff <= 180) {
                $total120entry180 += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            if($daysDiff > 180 && $daysDiff <= 365) {
                $total180entry365 += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            if($daysDiff > 365) {
                $total365 += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
            }
            $total += count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value  + $credit + $reteFuente + $reteIVA +$reteICA + $free)) : ($invoice->payable_amount - ($credit  + $reteFuente + $reteIVA +$reteICA + $free));
        }



        //Totales
        $spreadsheet->getActiveSheet()->getStyle('A' . ($i) . ':R' . ($i))->applyFromArray($styleArray);
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . ($i), 'TOTALES:')
            ->setCellValue('B' . ($i), '**')
            ->setCellValue('C' . ($i), '**')
            ->setCellValue('D' . ($i), '**')
            ->setCellValue('F' . ($i), '**')
            ->setCellValue('G' . ($i), '**')
            ->setCellValue('H' . ($i), $total)
            ->setCellValue('I' . ($i), '**')
            ->setCellValue('J' . ($i), '**')
            ->setCellValue('K' . ($i), $totalCorriente)
            ->setCellValue('L' . ($i), $total0entry30)
            ->setCellValue('M' . ($i), $total30entry60)
            ->setCellValue('N' . ($i), $total60entry90)
            ->setCellValue('O' . ($i), $total90entry120)
            ->setCellValue('P' . ($i), $total120entry180)
            ->setCellValue('Q' . ($i), $total180entry365)
            ->setCellValue('R' . ($i), $total365);



        $spreadsheet->getActiveSheet()->setTitle('Reporte_de_cartera');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_de_cartera.xls"');
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
}