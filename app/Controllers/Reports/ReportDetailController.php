<?php


namespace App\Controllers\Reports;


use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\AccountingAcount;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\LineInvoiceTax;
use App\Models\TypeDocument;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ReportDetailController extends BaseController
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
                $session->set('dateStart_detail', $datos->dataStart);
            }
            if (!empty($datos->dataEnd)) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $datos->dataEnd . ' 23:59:59']);
                $session = session();
                $session->set('dateEnd_detail', $datos->dataEnd);
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
            $session->set('querys_detail', $querys);
            $session = session();
            $session->set('querysOr_detail', $querysOr);
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
            'line_invoices.line_extension_amount',
            'invoices.tax_inclusive_amount',
            'line_invoices.id as id',
            'products.name as product_name',
            'products.free_of_charge_indicator',
            'line_invoices.quantity',
            'line_invoices.price_amount',
            'invoices.resolution_credit',
            'invoices.type_documents_id',
            'line_invoices.products_id',
            'invoice_status.name as invoice_status',
            'line_invoices.description as product_description',
            'line_invoices.discount_amount',
            'invoices.user_id',
            'invoices.seller_id'
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id');



        if (!empty(session('querysOr_detail'))) {
            if (isset(session('querysOr_detail')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_detail')['statusInvoice']);
            }
            if (isset(session('querysOr_detail')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_detail')['statusWallet']);
            }

            if (isset(session('querysOr_detail')['typeDocuments'])) {
                $invoice->whereIn('invoices.type_documents_id', session('querysOr')['typeDocuments']);
            }
            if (isset(session('querysOr_detail')['accounts'])) {
                $invoice->orWhereIn('products.iva', session('querysOr_detail')['accounts']);
                $invoice->orWhereIn('products.retefuente', session('querysOr_detail')['accounts']);
                $invoice->orWhereIn('products.reteica', session('querysOr_detail')['accounts']);
                $invoice->orWhereIn('products.reteiva', session('querysOr_detail')['accounts']);
            }
        }
        if (!empty(session('querys_detail'))) {
            $data = !empty(session('querys_detail')) ? session('querys_detail') : [];
            $data['invoices.type_documents_id !=']  = 100;
            $invoice->where($data);
        }else {
            $data['invoices.type_documents_id !=']  = 100;
            $data['invoices.companies_id']         = Auth::querys()->companies_id;
            $invoice->where($data);
        }


        $invoices = $invoice->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5])
            ->orderBy('invoices.resolution', 'DESC')
            ->asObject();

        $data = [
            'accoutings'    => $accoutingAccounts,
            'invoices'      => $invoices->paginate(10),
            'pager'         => $invoices->pager,
            'typeDocuments' => $typeDocuments,
        ];
        return view('reportGeneral/report_detail', $data);
    }


    public function reset()
    {
        $session = session();
        $session->set('querys_detail', []);
        $session = session();
        $session->set('querysOr_detail', []);
        $session->set('dateStart_detail', null);
        $session->set('dateEnd_detail', null);
        return redirect()->to(base_url() . '/report_detail');
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


        if (session('dateStart_detail')) {
            $dateStart = explode('-', session('dateStart_detail'));
            $mesStart = mes($dateStart[1]);
        } else {
            $dateStart = explode('-', $companies->created_at);
            $mesStart = mes($dateStart[1]);
        }


        if (session('dateEnd_detail')) {
            $dateEnd = explode('-', session('dateEnd_detail'));
            $mesEnd = mes($dateEnd[1]);
        } else {
            $dateEnd = explode('-', date('Y-m-d'));
            $mesEnd = mes($dateEnd[1]);
        }



        $invoice = new Invoice();
        $invoice->select([
            'invoices.resolution',
            'invoices.resolution_id',
            'type_documents.name as type_documents',
            'customers.name as customer_name',
            'type_document_identifications.name as type_document_identification',
            'customers.identification_number',
            'invoices.status_wallet',
            'invoices.payable_amount',
            'invoices.created_at',
            'line_invoices.line_extension_amount',
            'invoices.tax_inclusive_amount',
            'line_invoices.id',
            'products.name as product_name',
            'products.free_of_charge_indicator',
            'line_invoices.quantity',
            'line_invoices.price_amount',
            'invoices.resolution_credit',
            'invoices.type_documents_id',
            'line_invoices.products_id',
            'invoice_status.name as invoice_status',
            'line_invoices.description as product_description',
            'line_invoices.discount_amount',
            '(select prefix from resolutions where resolutions.companies_id = invoices.companies_id and resolutions.resolution = invoices.resolution_id and resolutions.type_documents_id = invoices.type_documents_id limit 1) as prefix',
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id');


        if (!empty(session('querysOr_detail'))) {
            if (isset(session('querysOr_detail')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_detail')['statusInvoice']);
            }
            if (isset(session('querysOr_detail')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_detail')['statusWallet']);
            }

            if (isset(session('querysOr_detail')['typeDocuments'])) {
                $invoice->whereIn('invoices.type_documents_id', session('querysOr_detail')['typeDocuments']);
            }
            if (isset(session('querysOr_detail')['accounts'])) {
                $invoice->orWhereIn('products.iva', session('querysOr_detail')['accounts']);
                $invoice->orWhereIn('products.retefuente', session('querysOr_detail')['accounts']);
                $invoice->orWhereIn('products.reteica', session('querysOr_detail')['accounts']);
                $invoice->orWhereIn('products.reteiva', session('querysOr_detail')['accounts']);
            }
        }
        if (!empty(session('querys_detail'))) {
            $data = !empty(session('querys_detail')) ? session('querys_detail') : [];
            $data['invoices.type_documents_id !='] = 100;
            $invoice->where($data);
        }else {
            $data['invoices.type_documents_id !='] = 100;
            $data[ 'invoices.companies_id'] = Auth::querys()->companies_id;
            $invoice->where($data);
        }

        $invoices = $invoice
            ->orderBy('invoices.resolution', 'DESC')
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
            ->setCellValue('B4', 'Reporte Detallado del ' .  explode(' ', $dateStart[2])[0] . ' de ' . $mesStart . ' de ' . $dateStart[0] . ' al ' . $dateEnd[2] . ' de ' . $mesEnd . ' de ' . $dateEnd[0])
            ->setCellValue('B5', date('Y-m-d H:i:s'))
            ->setCellValue('B6','MiFacturaLegal.com');

        $spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->getColor()->setARGB('FF2874A6');
        $spreadsheet->getActiveSheet()->getStyle('B6')->getFont()->getColor()->setARGB('FF2874A6');
        $spreadsheet->setActiveSheetIndex(0)->getStyle('B6')->getFont()->setBold(true);


        //quitar cuadricula
        $spreadsheet->getActiveSheet()->setShowGridlines(false);



        // Columnas A8 hasta T8
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal'    => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical'      => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FFD7DBDD',
                ],

            ]
        ];

        $spreadsheet->getActiveSheet()->getStyle('A8:V8')->applyFromArray($styleArray);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getRowDimension('8')->setRowHeight(40);
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
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A8', 'Tercero')
            ->setCellValue('B8', 'Tipo de identificación')
            ->setCellValue('C8', 'Número de identificación')
            ->setCellValue('D8', 'Tipo de documento')
            ->setCellValue('E8', 'Resolución')
            ->setCellValue('F8', 'Prefijo')
            ->setCellValue('G8', 'Número de documento')
            ->setCellValue('H8', 'Estado de factura')
            ->setCellValue('I8', 'Fecha de factura')
            ->setCellValue('J8', 'Producto / Servicio')
            ->setCellValue('K8', 'Descripción')
            ->setCellValue('L8', 'Producto gratis')
            ->setCellValue('M8', 'Cantidad')
            ->setCellValue('N8', 'Valor unitario del producto')
            ->setCellValue('O8', 'Descuento')
            ->setCellValue('P8', 'Valor total de los productos')
            ->setCellValue('Q8', 'IVA (%)')
            ->setCellValue('R8', 'IVA ($)')
            ->setCellValue('S8', 'Retención en la fuente')
            ->setCellValue('T8', 'Retención de ICA')
            ->setCellValue('U8', 'Retención de IVA')
            ->setCellValue('V8', 'Total Factura');



        $totalIva                   = 0;
        $totalReteFuente            = 0;
        $totalReteICA               = 0;
        $totalReteIVA               = 0;
        $totalPriceAmount           = 0;
        $totalLineExtensionAmount   = 0;
        $totalDiscountAmount        = 0;
        $total                      = 0;
        $i                          = 9;
        foreach ($invoices as $invoice) {

            $lineInvoicesTax = new LineInvoiceTax();
            $lineInvoicesTaxes = $lineInvoicesTax->where(['line_invoices_id' => $invoice->id])->get()->getResult();

            $percent        = 0;
            $iva            = 0;
            $reteIVA        = 0;
            $reteICA        = 0;
            $reteFuente     = 0;


            if ($invoice->type_documents_id != 4) {
                foreach ($lineInvoicesTaxes as $lineInvoicesTax) {
                    switch ($lineInvoicesTax->taxes_id) {
                        case 1:
                            $percent    = $lineInvoicesTax->percent;
                            $iva        = $lineInvoicesTax->tax_amount;
                            break;
                        case 5:
                            $reteIVA    += $lineInvoicesTax->tax_amount;
                            break;
                        case 7:
                            $reteICA    += $lineInvoicesTax->tax_amount;
                            break;
                        case 6:
                            $reteFuente += $lineInvoicesTax->tax_amount;
                            break;
                    }
                }
            } else {
                $lineInvoicesCredits = new LineInvoice();
                $lineInvoicesCredit = $lineInvoicesCredits->select('line_invoice_taxs.*, invoices.resolution_credit, line_invoices.price_amount')
                    ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
                    ->join('invoices', 'line_invoices.invoices_id = invoices.id')
                    ->where(['invoices.resolution' => $invoice->resolution_credit, 'invoices.companies_id' => Auth::querys()->companies_id, 'line_invoices.products_id' => $invoice->products_id])
                    ->asObject()
                    ->get()
                    ->getResult();

                foreach ($lineInvoicesCredit as $optionsCredit) {
                    switch ($optionsCredit->taxes_id) {
                        case 1:
                            $percent        = $optionsCredit->percent;
                            $iva            = $optionsCredit->tax_amount;
                            break;
                        case 5:
                            $reteIVA        += $optionsCredit->percent * $invoice->line_extension_amount / 100;
                            break;
                        case 7:
                            $reteICA        += $optionsCredit->percent * $invoice->line_extension_amount / 100;
                            break;
                        case 6:
                            $reteFuente     += $optionsCredit->percent * $invoice->line_extension_amount / 100;
                            break;
                    }
                }
            }

            if ($invoice->type_documents_id != 4) {
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $invoice->customer_name)
                    ->setCellValue('B' . $i, $invoice->type_document_identification)
                    ->setCellValue('C' . $i, $invoice->identification_number)
                    ->setCellValue('D' . $i, $invoice->type_documents)
                    ->setCellValue('E' . $i, $invoice->resolution_id)
                    ->setCellValue('F' . $i, $invoice->prefix)
                    ->setCellValue('G' . $i, $invoice->resolution)
                    ->setCellValue('H' . $i, $invoice->invoice_status)
                    ->setCellValue('I' . $i, $invoice->created_at)
                    ->setCellValue('J' . $i, $invoice->product_name)
                    ->setCellValue('K' . $i, $invoice->product_description)
                    ->setCellValue('L' . $i, $invoice->free_of_charge_indicator == 'false' || $invoice->free_of_charge_indicator == '' ? 'No' : 'Si')
                    ->setCellValue('M' . $i, $invoice->quantity)
                    ->setCellValue('N' . $i, $invoice->price_amount)
                    ->setCellValue('O' . $i, $invoice->discount_amount)
                    ->setCellValue('P' . $i, $invoice->free_of_charge_indicator == 'false' ||$invoice->free_of_charge_indicator == '' ? $invoice->line_extension_amount : '0')
                    ->setCellValue('Q' . $i, $percent)
                    ->setCellValue('R' . $i, $iva)
                    ->setCellValue('S' . $i, $reteFuente)
                    ->setCellValue('T' . $i, $reteICA)
                    ->setCellValue('U' . $i, $reteIVA)
                    ->setCellValue('V' . $i, ($invoice->free_of_charge_indicator == 'false'  ||$invoice->free_of_charge_indicator == ''? $invoice->line_extension_amount : 0) + $iva - ($reteFuente + $reteICA + $reteIVA));
            } else {
                $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':' . 'T' . $i)->getFont()->getColor()->setARGB('FF85929E');
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $invoice->customer_name)
                    ->setCellValue('B' . $i, $invoice->type_document_identification)
                    ->setCellValue('C' . $i, $invoice->identification_number)
                    ->setCellValue('D' . $i, $invoice->type_documents)
                    ->setCellValue('E' . $i, $invoice->resolution_id)
                    ->setCellValue('F' . $i, $invoice->prefix)
                    ->setCellValue('G' . $i, $invoice->resolution)
                    ->setCellValue('H' . $i, $invoice->invoice_status)
                    ->setCellValue('I' . $i, $invoice->created_at)
                    ->setCellValue('J' . $i, $invoice->product_name)
                    ->setCellValue('K' . $i, $invoice->product_description)
                    ->setCellValue('L' . $i, $invoice->free_of_charge_indicator == 'false'  ||$invoice->free_of_charge_indicator == ''? 'No' : 'Si')
                    ->setCellValue('M' . $i, '-'.($invoice->quantity))
                    ->setCellValue('N' . $i, '-'.($invoice->price_amount))
                    ->setCellValue('O' . $i, '-'.($invoice->discount_amount))
                    ->setCellValue('P' . $i, $invoice->free_of_charge_indicator == 'false'  ||$invoice->free_of_charge_indicator == '' ? '-'.($invoice->line_extension_amount) : '0')
                    ->setCellValue('Q' . $i, '-'.($percent))
                    ->setCellValue('R' . $i, '-'.($iva))
                    ->setCellValue('S' . $i, '-'.($reteFuente))
                    ->setCellValue('T' . $i, '-'.($reteICA))
                    ->setCellValue('U' . $i, '-'.($reteIVA))
                    ->setCellValue('V' . $i, ($invoice->free_of_charge_indicator == 'false'  ||$invoice->free_of_charge_indicator == '' ? '-'.($invoice->line_extension_amount + ($iva - ($reteFuente + $reteICA + $reteIVA))) : 0) );
            }
            if ($invoice->type_documents_id != 4) {
                $totalLineExtensionAmount       += $invoice->free_of_charge_indicator == 'false'  || $invoice->free_of_charge_indicator == '' ? '-'.($invoice->line_extension_amount) : '0';
                $totalIva                       += $iva;
                $totalReteIVA                   += $reteIVA;
                $totalReteFuente                += $reteFuente;
                $totalReteICA                   += $reteICA;
                $total                          += (($invoice->free_of_charge_indicator == 'false'  ||$invoice->free_of_charge_indicator == ''? $invoice->line_extension_amount : 0) + ($iva -($reteFuente + $reteICA + $reteIVA)));
                $totalPriceAmount               += $invoice->price_amount;
                $totalDiscountAmount            += $invoice->discount_amount;
            } else {
                $totalLineExtensionAmount       -= $invoice->free_of_charge_indicator == 'false'  ||$invoice->free_of_charge_indicator == '' ? '-'.($invoice->line_extension_amount) : '0';
                $total                          -= (($invoice->free_of_charge_indicator == 'false'  ||$invoice->free_of_charge_indicator == '' ? $invoice->line_extension_amount : 0) + ($iva -($reteFuente + $reteICA + $reteIVA)));
                $totalIva                       -= $iva;
                $totalReteICA                   -= $reteICA;
                $totalReteIVA                   -= $reteIVA;
                $totalReteFuente                -= $reteFuente;
                $totalPriceAmount               -= $invoice->price_amount;
                $totalDiscountAmount            -= $invoice->discount_amount;
            }
            $i++;
        }


        //Totales
        $spreadsheet->getActiveSheet()->getStyle('A' . ($i) . ':V' . ($i))->applyFromArray($styleArray);

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A' . ($i), 'TOTALES:')
            ->setCellValue('B' . ($i), '**')
            ->setCellValue('C' . ($i), '**')
            ->setCellValue('D' . ($i), '**')
            ->setCellValue('E' . ($i), '**')
            ->setCellValue('F' . ($i), '**')
            ->setCellValue('G' . ($i), '**')
            ->setCellValue('H' . ($i), '**')
            ->setCellValue('I' . ($i), '**')
            ->setCellValue('J' . ($i), '**')
            ->setCellValue('K' . ($i), '**')
            ->setCellValue('L' . ($i), '**')
            ->setCellValue('M' . ($i), '**')
            ->setCellValue('N' . ($i), $totalPriceAmount)
            ->setCellValue('O' . ($i), $totalDiscountAmount)
            ->setCellValue('P' . ($i), $totalLineExtensionAmount)
            ->setCellValue('Q' . ($i), '**')
            ->setCellValue('R' . ($i), $totalIva)
            ->setCellValue('S' . ($i), number_format($totalReteFuente, '3', ',', ''))
            ->setCellValue('T' . ($i), number_format($totalReteICA, '3', ',', ''))
            ->setCellValue('U' . ($i), number_format($totalReteIVA, '3', ',', ''))
            ->setCellValue('V' . ($i), number_format($total, '2', ',', ''));


        $spreadsheet->getActiveSheet()->setTitle('Reporte_Facturación_Detallado');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_Facturación_Detallado.xls"');
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