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
use App\Models\IntegrationShopify;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ReportGeneralController extends BaseController
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
                $session->set('dateStart_general', $datos->dataStart);
            }
            if (!empty($datos->dataEnd)) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $datos->dataEnd . ' 23:59:59']);
                $session = session();
                $session->set('dateEnd_general', $datos->dataEnd);
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

            if (!empty($datos->sellerId)) {
                $querys = array_merge($querys, ['invoices.seller_id' => $datos->sellerId]);
            }

            if (!empty($datos->userId)) {
                $querys = array_merge($querys, ['invoices.user_id' => $datos->userId]);
            }

            $session = session();
            $session->set('querys_general', $querys);
            $session = session();
            $session->set('querysOr_general', $querysOr);
        }

        $invoice = new Invoice();
        $invoice->select([
            'invoices.resolution',
            'type_documents.name as type_documents',
            'customers.name as customer_name',
            'type_document_identifications.name as type_document_identification',
            'customers.identification_number',
            'invoices.type_documents_id',
            'invoices.status_wallet',
            'invoices.payable_amount',
            'invoices.created_at',
            'invoices.line_extesion_amount',
            'invoices.tax_inclusive_amount',
            'invoices.id',
            'invoices.user_id',
            'invoices.seller_id',
            'invoice_status.name as invoice_status'
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('line_invoices', 'line_invoices.invoices_id = invoices.id')
            ->join('products', 'products.id = line_invoices.products_id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id');


        if (!empty(session('querysOr_general'))) {
            if (isset(session('querysOr_general')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_general')['statusInvoice']);
            }
            if (isset(session('querysOr_general')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_general')['statusWallet']);
            }

            if (isset(session('querysOr_general')['typeDocuments'])) {
                $invoice->whereIn('invoices.type_documents_id', session('querysOr_general')['typeDocuments']);
            }
            if (isset(session('querysOr_general')['accounts'])) {
                $invoice->orWhereIn('products.iva', session('querysOr_general')['accounts']);
                $invoice->orWhereIn('products.retefuente', session('querysOr_general')['accounts']);
                $invoice->orWhereIn('products.reteica', session('querysOr_general')['accounts']);
                $invoice->orWhereIn('products.reteiva', session('querysOr_general')['accounts']);
            }
        }
        if (!empty(session('querys_general'))) {
            $data = !empty(session('querys_general')) ? session('querys_general') : [];
            $data['invoices.type_documents_id <='] = 5;
            $invoice->where($data);
        } else {
            $data['invoices.type_documents_id <='] = 5;
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $invoice->where($data);
        }


        $invoices = $invoice->whereIn('invoices.type_documents_id', [1, 2, 3, 4, 5])
            ->asObject()
            ->orderBy('invoices.resolution', 'DESC')
            ->groupBy(['invoices.id']);

        $data = [
            'accoutings'    => $accoutingAccounts,
            'invoices'      => $invoices->paginate(10),
            'pager'         => $invoices->pager,
            'typeDocuments' => $typeDocuments,
        ];
        return view('reportGeneral/report_general', $data);
    }

    public function reset()
    {
        $session = session();
        $session->set('querys_general', []);
        $session = session();
        $session->set('querysOr_general', []);
        $session->set('dateStart_general', null);
        $session->set('dateEnd_general', null);
        return redirect()->to(base_url() . '/report_general');
    }

    public function excel()
    {
        $company = new Company();
        $companies = $company
            ->join('type_document_identifications', 'type_document_identifications.id = companies.type_document_identifications_id')
            ->where(['companies.id' => Auth::querys()->companies_id])
            ->asObject()
            ->first();

            $integration = new IntegrationShopify();
            $integrationShopify = $integration->where(['companies_id' => Auth::querys()->companies_id])
                ->asObject()
                ->first();

        if (session('dateStart_general')) {
            $dateStart = explode('-', session('dateStart_general'));
            $mesStart = mes($dateStart[1]);
        } else {
            $dateStart = explode('-', $companies->created_at);
            $mesStart = mes($dateStart[1]);
        }


        if (session('dateEnd_general')) {
            $dateEnd = explode('-', session('dateEnd_general'));
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
            'invoices.type_documents_id',
            'invoices.status_wallet',
            'invoices.payable_amount',
            'invoices.created_at',
            'invoices.line_extesion_amount',
            'invoices.tax_inclusive_amount',
            'invoices.id',
            'invoices.user_id',
            'invoices.seller_id',
            'invoice_status.name as invoice_status',
            '(select prefix from resolutions where resolutions.companies_id = invoices.companies_id and resolutions.resolution = invoices.resolution_id and resolutions.type_documents_id = invoices.type_documents_id limit 1) as prefix',
            !is_null($integrationShopify) ?  'integration_traffic_light.number_app' : '',
            !is_null($integrationShopify) ? 'integration_traffic_light.created_at as date_send' : '',
            !is_null($integrationShopify) ? 'invoices.notes' : ''
        ])
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'invoices.type_documents_id = type_documents.id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id');
        if(!is_null($integrationShopify)) {
           $invoice = $invoice->join('integration_traffic_light', 'integration_traffic_light.number_mfl = invoices.resolution', 'left');
        }
           $invoice =   $invoice->where(['invoices.type_documents_id <' => 6]);


        if (!empty(session('querysOr_general'))) {
            if (isset(session('querysOr_general')['statusInvoice'])) {
                $invoice->whereIn('invoices.invoice_status_id', session('querysOr_general')['statusInvoice']);
            }
            if (isset(session('querysOr_general')['statusWallet'])) {
                $invoice->whereIn('invoices.status_wallet', session('querysOr_general')['statusWallet']);
            }

            if (isset(session('querysOr_general')['typeDocuments'])) {
                $invoice->whereIn('invoices.type_documents_id', session('querysOr_general')['typeDocuments']);
            }
            if (isset(session('querysOr_general')['accounts'])) {
                $invoice->orWhereIn('products.iva', session('querysOr_general')['accounts']);
                $invoice->orWhereIn('products.retefuente', session('querysOr_general')['accounts']);
                $invoice->orWhereIn('products.reteica', session('querysOr_general')['accounts']);
                $invoice->orWhereIn('products.reteiva', session('querysOr_general')['accounts']);
            }
        }
        if (!empty(session('querys_general'))) {
            $data = !empty(session('querys_general')) ? session('querys_general') : [];
            $invoice->where($data);
        } else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $invoice->where($data);
        }

        $invoices = $invoice //= $invoice->groupBy(['invoices.id',  !is_null($integrationShopify) ? 'number_app': '' ,!is_null($integrationShopify) ? 'integration_traffic_light.created_at' : ''])
            ->asObject()
            ->get()
            ->getResult();


        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }
        $spreadsheet = new Spreadsheet();

        //Encabezado
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A2', 'Empresa')->getStyle('A2')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A3', 'Identificación')->getStyle('A3')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A4', 'Fecha de reporte')->getStyle('A4')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A5', 'Fecha de generación')->getStyle('A5')->getFont()->setBold(true);
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A6', 'Software de Facturación')->getStyle('A6')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A6')->getFont()->getColor()->setARGB('FF2874A6');

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('B2', $companies->company)
            ->setCellValue('B3', ($companies->name . ' ' . number_format($companies->identification_number, 0, '.', '.') . (empty($company->dv) ? '' : '-' . $company->dv)))
            ->setCellValue('B4', 'Reporte General del ' . explode(' ', $dateStart[2])[0] . ' de ' . $mesStart . ' de ' . $dateStart[0] . ' al ' . $dateEnd[2] . ' de ' . $mesEnd . ' de ' . $dateEnd[0])
            ->setCellValue('B5', date('Y-m-d H:i:s'))
            ->setCellValue('B6', 'MiFacturaLegal.com');

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
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A8', 'Tercero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('B8', 'Tipo de identificación');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C8', 'Número de identificación');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('D8', 'Tipo de documento');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('E8', 'Usuario');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('F8', 'Vendedor');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('G8', 'Resolución');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('H8', 'Prefijo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('I8', 'Número de documento');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('J8', 'Estado de factura');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('K8', 'Fecha de factura');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('L8', 'Valor facturado antes de IVA');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('M8', 'IVA ($)');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('N8', 'Retención en la fuente');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('O8', 'Retención de ICA');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('P8', 'Retención de IVA');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q8', 'Total Factura');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('R8', 'Estado de cartera');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('S8', 'Saldo por cobrar');
        if(!is_null($integrationShopify)) {
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('T8', 'Fecha de envio');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('U8', 'N° Orden / Nota');
        }
        

        $i                          = 9;
        $total                      = 0;
        $totalIva                   = 0;
        $totalReteFuente            = 0;
        $totalReteICA               = 0;
        $totalReteIVA               = 0;
        $totalWallet                = 0;
        $totalLineExtensionAmount   = 0;
        foreach ($invoices as $invoice) {
            $lineInvoice = new LineInvoice();
            $lineInvoices = $lineInvoice->select('line_invoice_taxs.*, invoices.resolution_credit, line_invoices.products_id, line_invoices.line_extension_amount, products.free_of_charge_indicator')
                ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
                ->join('products', 'products.id = line_invoices.products_id')
                ->join('invoices', 'line_invoices.invoices_id = invoices.id')
                ->where(['line_invoices.invoices_id' => $invoice->id])
                ->asObject()
                ->get()
                ->getResult();

            $reteFuente = 0;
            $reteIVA = 0;
            $reteICA = 0;
            $iva = 0;
            $free = 0;
            $product = 0;


            if ($invoice->type_documents_id != 4) {
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


                    if ($product != $lineInvoice->products_id) {
                        if ($lineInvoice->free_of_charge_indicator == 'true') {
                            $free += $lineInvoice->line_extension_amount;
                        }
                        $product = $lineInvoice->products_id;
                    }
                }
            } else {
                foreach ($lineInvoices as $lineInvoice) {
                    $lineInvoicesCredits = new LineInvoice();
                    $lineInvoicesCredit = $lineInvoicesCredits->select('line_invoice_taxs.*, invoices.resolution_credit, line_invoices.price_amount')
                        ->join('line_invoice_taxs', 'line_invoice_taxs.line_invoices_id = line_invoices.id')
                        ->join('invoices', 'line_invoices.invoices_id = invoices.id')
                        ->where(['invoices.resolution' => $lineInvoice->resolution_credit, 'invoices.companies_id' => Auth::querys()->companies_id, 'line_invoices.products_id' => $lineInvoice->products_id])
                        ->asObject()
                        ->get()
                        ->getResult();

                    foreach ($lineInvoicesCredit as $optionsCredit) {
                        switch ($optionsCredit->taxes_id) {
                            case 1:
                                $iva += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                                break;
                            case 5:
                                $reteIVA += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                                break;
                            case 7:
                                $reteICA += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                                break;
                            case 6:
                                $reteFuente += $optionsCredit->percent * $lineInvoice->line_extension_amount / 100;
                                break;
                        }
                        if ($product != $lineInvoice->products_id) {
                            if ($lineInvoice->free_of_charge_indicator == 'true') {
                                $free += $lineInvoice->line_extension_amount;
                            }
                            $product = $lineInvoice->products_id;
                        }
                    }
                }
            }


            $wallet = new Wallet();
            $wallets = $wallet->select('sum(value) as value')
                ->where(['invoices_id' => $invoice->id])
                ->groupBy(['invoices_id'])
                ->get()
                ->getResult();

            if ($invoice->type_documents_id != 4) {
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $invoice->customer_name)
                    ->setCellValue('B' . $i, $invoice->type_document_identification)
                    ->setCellValue('C' . $i, $invoice->identification_number)
                    ->setCellValue('D' . $i, $invoice->type_documents)
                    ->setCellValue('E' . $i, userName($invoice->user_id) ? userName($invoice->user_id) : '')
                    ->setCellValue('F' . $i, userName($invoice->seller_id) ? userName($invoice->seller_id) : '')
                    ->setCellValue('G' . $i, $invoice->resolution_id)
                    ->setCellValue('H' . $i, $invoice->prefix)
                    ->setCellValue('I' . $i, $invoice->resolution)
                    ->setCellValue('J' . $i, $invoice->invoice_status)
                    ->setCellValue('K' . $i, $invoice->created_at)
                    ->setCellValue('L' . $i, $invoice->line_extesion_amount - $free)
                    ->setCellValue('M' . $i, $iva)
                    ->setCellValue('N' . $i, $reteFuente)
                    ->setCellValue('O' . $i, $reteICA)
                    ->setCellValue('P' . $i, $reteIVA)
                    ->setCellValue('Q' . $i, $invoice->payable_amount - ($reteIVA + $reteICA + $reteFuente + $free))
                    ->setCellValue('R' . $i, ($invoice->type_documents_id == 1 || $invoice->type_documents_id == 5) ? $invoice->status_wallet : 'Paga');
                    if(!is_null($integrationShopify)) {
                        $spreadsheet->setActiveSheetIndex(0)
                        ->setCellValue('S' . $i, $invoice->number_app)
                        ->setCellValue('T' . $i, $invoice->date_send)
                        ->setCellValue('U' . $i, str_replace('</h5>', '', str_replace('<h5>', '', $invoice->notes)));
                    }
                if ($invoice->status_wallet == 'Pendiente') {
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q' . $i, count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value + $reteIVA + $reteICA + $reteFuente + $free)) : ($invoice->payable_amount - ($reteIVA + $reteICA + $reteFuente + $free)));
                } else {
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q' . $i, 0);
                }
            } else {
                $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':' . 'Q' . $i)->getFont()->getColor()->setARGB('FF85929E');
                $spreadsheet->setActiveSheetIndex(0)
                    ->setCellValue('A' . $i, $invoice->customer_name)
                    ->setCellValue('B' . $i, $invoice->type_document_identification)
                    ->setCellValue('C' . $i, $invoice->identification_number)
                    ->setCellValue('D' . $i, $invoice->type_documents)
                    ->setCellValue('E' . $i, userName($invoice->user_id) ? userName($invoice->user_id) : '')
                    ->setCellValue('F' . $i, userName($invoice->seller_id) ? userName($invoice->seller_id) : '')
                    ->setCellValue('G' . $i, $invoice->resolution_id)
                    ->setCellValue('H' . $i, $invoice->prefix)
                    ->setCellValue('I' . $i, $invoice->resolution)
                    ->setCellValue('J' . $i, $invoice->invoice_status)
                    ->setCellValue('K' . $i, $invoice->created_at)
                    ->setCellValue('L' . $i, '-' . ($invoice->line_extesion_amount - $free))
                    ->setCellValue('M' . $i, '-' . ($iva))
                    ->setCellValue('N' . $i, '-' . ($reteFuente))
                    ->setCellValue('O' . $i, '-' . ($reteICA))
                    ->setCellValue('P' . $i, '-' . ($reteIVA))
                    ->setCellValue('Q' . $i, '-' . ($invoice->payable_amount - ($reteIVA + $reteICA + $reteFuente + $free)))
                    ->setCellValue('R' . $i, (($invoice->type_documents_id == 1 || $invoice->type_documents_id == 5) ? $invoice->status_wallet : 'Paga'));
                    if(!is_null($integrationShopify)) {
                        $spreadsheet->setActiveSheetIndex(0)
                            ->setCellValue('S' . $i, $invoice->number_app)
                            ->setCellValue('T' . $i, $invoice->date_send)
                            ->setCellValue('U' . $i, str_replace('</h5>', '', str_replace('<h5>', '', $invoice->notes)));
                    }
            }
            if ($invoice->type_documents_id != 4) {
                $totalIva                   += $iva;
                $totalReteFuente            += $reteFuente;
                $totalReteICA               += $reteICA;
                $totalReteIVA               += $totalReteIVA;
                $totalLineExtensionAmount   += $invoice->line_extesion_amount - $free;
                if ($invoice->status_wallet == 'Pendiente') {
                    $totalWallet += (count($wallets) > 0 ? ($invoice->payable_amount - ($wallets[0]->value + $reteIVA + $reteICA + $reteFuente + $free)) : ($invoice->payable_amount - ($reteIVA + $reteICA + $reteFuente + $free)));
                }
                $total += $invoice->payable_amount - ($reteFuente + $reteICA + $reteIVA + $free);
            } else {
                $totalIva                   -= $iva;
                $totalReteFuente            -= $reteFuente;
                $totalReteICA               -= $reteICA;
                $totalReteIVA               -= $totalReteIVA;
                $totalLineExtensionAmount   -= $invoice->line_extesion_amount - $free;
                if ($invoice->status_wallet == 'Pendiente') {
                    $totalWallet -= 0;
                }
                $total -= $invoice->payable_amount - ($reteFuente + $reteICA + $reteIVA + $free);
            }
            $i++;
        }


        //Totales
        $spreadsheet->getActiveSheet()->getStyle('A' . ($i) . ':U' . ($i))->applyFromArray($styleArray);

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
            ->setCellValue('L' . ($i), $totalLineExtensionAmount)
            ->setCellValue('M' . ($i), $totalIva)
            ->setCellValue('N' . ($i), $totalReteFuente)
            ->setCellValue('O' . ($i), $totalReteICA)
            ->setCellValue('P' . ($i), $totalReteIVA)
            ->setCellValue('Q' . ($i), $total)
            ->setCellValue('R' . ($i), '***')
            ->setCellValue('S' . ($i), '**')
            ->setCellValue('T' . ($i), '**')
            ->setCellValue('U' . ($i), '**')
            ->setCellValue('V' . ($i), '**');


        $spreadsheet->getActiveSheet()->setTitle('Reporte_General');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_General.xls"');
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

