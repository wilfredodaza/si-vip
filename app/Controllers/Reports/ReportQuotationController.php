<?php


namespace App\Controllers\Reports;


use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\AccountingAcount;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\TypeDocument;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ReportQuotationController extends BaseController
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
                $session->set('dateStart_quotation', $datos->dataStart);

            }
            if (!empty($datos->dataEnd)) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $datos->dataEnd . ' 23:59:59']);
                $session = session();
                $session->set('dateEnd_quotation', $datos->dataEnd);
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
            $session->set('querys_quotation', $querys);
            $session = session();
            $session->set('querysOr_quotation', $querysOr);


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
            'invoices.user_id',
            'invoices.seller_id',
            'invoices.issue_date',
            'invoice_status.name as invoice_status'
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id');


        if (!empty(session('querysOr_quotation'))) {
            if (isset(session('querysOr_quotation')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_quotation')['statusInvoice']);
            }
            if (isset(session('querysOr_quotation')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_quotation')['statusWallet']);
            }
        }
        if (!empty(session('querys_quotation'))) {
            $data = session('querys_quotation');
            $data['invoices.type_documents_id'] = 100;
            $invoice->where(!empty($data) ? $data : []);
        }else {
            $data['invoices.type_documents_id'] = 100;
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $invoice->where($data);
        }

        $invoices = $invoice
            ->groupBy(['invoices.id'])
            ->asObject();

        $data = [
            'accoutings'    => $accoutingAccounts,
            'invoices'                                      => $invoices->paginate(10),
            'pager'                                         => $invoices->pager,
            'typeDocuments' => $typeDocuments,
        ];
        return view('reportGeneral/report_quotation', $data);
    }

    public function reset()
    {
        $session = session();
        $session->set('querys_quotation', []);
        $session = session();
        $session->set('querysOr_quotation', []);
        $session->set('dateStart_quotation', null);
        $session->set('dateEnd_quotation', null);
        return redirect()->to(base_url() . '/report_quotation');
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


        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Empresa')
            ->setCellValue('A3', 'Identificación')
            ->setCellValue('A4', 'Fecha de reporte')
            ->setCellValue('A5', 'Fecha de generación');

        $company = new Company();
        $companies = $company
            ->join('type_document_identifications', 'type_document_identifications.id = companies.type_document_identifications_id')
            ->where([
                'companies.id' => Auth::querys()->companies_id,
            ])
            ->asObject()
            ->get()
            ->getResult()[0];


        if (session('dateStart_quotation')) {
            $dateStart = explode('-', session('dateStart_quotation'));
            $mesStart = mes($dateStart[1]);
        } else {
            $dateStart = explode('-', $companies->created_at);
            $mesStart = mes($dateStart[1]);
        }


        if (session('dateEnd_quotation')) {
            $dateEnd = explode('-', session('dateEnd_quotation'));
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
            'invoices.user_id',
            'invoices.seller_id',
            'invoices.issue_date',
            'invoice_status.name as invoice_status'
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id');


        if (!empty(session('querysOr_quotation'))) {
            if (isset(session('querysOr_quotation')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_quotation')['statusInvoice']);
            }
            if (isset(session('querysOr_quotation')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_quotation')['statusWallet']);
            }
            if (isset(session('querysOr_quotation')['accounts'])) {
                $invoice->orWhereIn('products.iva', session('querysOr_quotation')['accounts']);
                $invoice->orWhereIn('products.retefuente', session('querysOr_quotation')['accounts']);
                $invoice->orWhereIn('products.reteica', session('querysOr_quotation')['accounts']);
                $invoice->orWhereIn('products.reteiva', session('querysOr_quotation')['accounts']);
            }
        }
        if (!empty(session('querys_quotation'))) {
            $data = session('querys_quotation');
            $data['invoices.type_documents_id'] = 100;
            $invoice->where(!empty($data) ? $data : []);
        }else {
            $data['invoices.type_documents_id'] = 100;
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
            ->setCellValue('B4', 'Reporte de Cotizaciones del ' .  explode(' ', $dateStart[2])[0] . ' de ' . $mesStart . ' de ' . $dateStart[0] . ' al ' . $dateEnd[2] . ' de ' . $mesEnd . ' de ' . $dateEnd[0])
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
        $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(40);
        $spreadsheet->getActiveSheet()->getStyle('A8:R8')->applyFromArray($styleArray);
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

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A8', 'Tercero')
            ->setCellValue('B8', 'Tipo de identificación')
            ->setCellValue('C8', 'Número de identificación')
            ->setCellValue('D8', 'Tipo de documento')
            ->setCellValue('E8', 'Número de documento')
            ->setCellValue('F8', 'Fecha')
            ->setCellValue('G8', 'Usuario')
            ->setCellValue('H8', 'Valor cotizado antes de IVA')
            ->setCellValue('I8', 'IVA ($)')
            ->setCellValue('J8', 'Retención en la fuente')
            ->setCellValue('K8', 'Retención de ICA')
            ->setCellValue('L8', 'Retención de IVA')
            ->setCellValue('M8', 'Total Cotizado')
            ->setCellValue('N8', 'Estado de cotización')
            ->setCellValue('O8', 'Fecha de cierre')
            ->setCellValue('P8', 'Cotización facturada')
            ->setCellValue('Q8', 'Cantidad de facturas hechas en base a esta cotización');

        $i = 9;
        $totalReteIVA               = 0;
        $totalReteICA               = 0;
        $totalReteFuente            = 0;
        $totalLineExtensionAmount   = 0;
        $totalIVA                   = 0;
        $total                      = 0;

        foreach ($invoices as $invoice) {
            $lineInvoice = new LineInvoice();
            $lineInvoices = $lineInvoice->select('line_invoice_taxs.*')
                ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
                ->where(['line_invoices.invoices_id' => $invoice->id])
                ->asObject()
                ->get()
                ->getResult();

            $reteFuente = 0;
            $reteIVA    = 0;
            $reteICA    = 0;
            $iva        = 0;
            foreach ($lineInvoices as $lineInvoice) {
                switch ($lineInvoice->taxes_id) {
                    case 1:
                        $iva += $lineInvoice->tax_amount;
                        break;
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
            }

            $quotations = new Invoice();
            $quotation = $quotations->select('count(invoices.id) as quantity')->where(['resolution_credit' => $invoice->id])->get()->getResult();


            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, $invoice->customer_name)
                ->setCellValue('B' . $i, $invoice->type_document_identification)
                ->setCellValue('C' . $i, $invoice->identification_number)
                ->setCellValue('D' . $i, $invoice->type_documents)
                ->setCellValue('E' . $i, $invoice->resolution)
                ->setCellValue('F' . $i, $invoice->created_at)
                ->setCellValue('G' . $i, userName($invoice->user_id) ? userName($invoice->user_id) : '')
                ->setCellValue('H' . $i, $invoice->line_extesion_amount)
                ->setCellValue('I' . $i, $iva)
                ->setCellValue('J' . $i, $reteFuente)
                ->setCellValue('K' . $i, $reteICA)
                ->setCellValue('L' . $i, $reteIVA)
                ->setCellValue('M' . $i, $invoice->payable_amount - ($reteFuente + $reteIVA + $reteICA))
                ->setCellValue('N' . $i, $invoice->invoice_status)
                ->setCellValue('O' . $i, is_null($invoice->issue_date) ? '' : $invoice->issue_date )
                ->setCellValue('P' . $i, (count($quotation) > 0 ? 'Si' : 'No'))
                ->setCellValue('Q' . $i, (count($quotation) > 0 ? $quotation[0]->quantity : '0'));
            $i++;
            $totalLineExtensionAmount   += $invoice->line_extesion_amount;
            $totalIVA                   += $iva;
            $totalReteIVA               += $reteIVA;
            $totalReteICA               += $reteICA;
            $totalReteFuente            += $reteFuente;
            $total                      += $invoice->payable_amount - ($reteFuente + $reteIVA + $reteICA);
        }


        //Totales
        $spreadsheet->getActiveSheet()->getStyle('A' . ($i) . ':Q' . ($i))->applyFromArray($styleArray);
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . ($i), 'TOTALES:')
            ->setCellValue('B' . ($i), '**')
            ->setCellValue('C' . ($i), '**')
            ->setCellValue('D' . ($i), '**')
            ->setCellValue('F' . ($i), '**')
            ->setCellValue('G' . ($i), '**')
            ->setCellValue('H' . ($i), $totalLineExtensionAmount)
            ->setCellValue('I' . ($i), $totalIVA)
            ->setCellValue('J' . ($i), $totalReteFuente)
            ->setCellValue('K' . ($i), $totalReteICA)
            ->setCellValue('L' . ($i), $totalReteIVA)
            ->setCellValue('M' . ($i), $total)
            ->setCellValue('N' . ($i), '**')
            ->setCellValue('O' . ($i), '**')
            ->setCellValue('P' . ($i), '**')
            ->setCellValue('Q' . ($i), '**');


        $spreadsheet->getActiveSheet()->setTitle('Reporte_de_cotizaciones');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_de_cotizaciones.xls"');
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