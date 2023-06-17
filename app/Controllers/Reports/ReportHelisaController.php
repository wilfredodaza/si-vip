<?php
/**
 * Esta clase esta encargada de generar el reporte de helisa version en la nube.
 * @author Wilson Andres Bachiller Ortiz
 * @version 1.0.0
 */
namespace App\Controllers\Reports;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\AccountingAcount;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\TypeDocument;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class ReportHelisaController extends BaseController
{
    /**
     * Este metodo esta encargado de manejar la vista de
     *
     * @return \CodeIgniter\HTTP\RedirectResponse|string
     */
    public function index()
    {
        $typeDocument = new TypeDocument();
        $typeDocuments = $typeDocument->whereIn('id', [1, 2, 3, 4, 5])->asObject()->get()->getResult();

        if ($this->request->getGet()) {
          //  echo json_encode($this->request->getJSON()); die();
            $querys = ['invoices.companies_id' => Auth::querys()->companies_id];
            if (!empty($this->request->getGet('date_start'))) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $this->request->getGet('date_start'). ' 00:00:00']);
                $session = session();
                $session->set('dateStart_helisa', $this->request->getGet('date_start'));
            }
            if (!empty($this->request->getGet('date_end'))) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $this->request->getGet('date_end'). ' 23:59:59']);
                $session = session();
                $session->set('dateEnd_helisa', $this->request->getGet('date_end'));
            }



            if ($this->request->getGet('type_document')) {
                $querys = array_merge($querys, ['invoices.type_documents_id' => $this->request->getGet('type_document')]);
            }


            $session = session();
            $session->set('query_helisa', $querys);
            return redirect()->to(base_url('/report/helisa/invoice').'?date_start='.$this->request->getGet('date_start').'&date_end='.$this->request->getGet('date_end'))
                ->with('success', 'Consulta generada con exito.');

        }
        return view('reportGeneral/report_helisa', ['typeDocuments' => $typeDocuments]);
    }

    public function reportHelisaInvoice()
    {
        $helper = new Sample();
        if ($helper->isCli()) {
            $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
            return;
        }
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()->setCreator('MiFacturaLegal.com - MAWII');

        //Columnas A8 Hasta Q8
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:W1')->applyFromArray($styleArray);

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
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getStyle('B')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        $spreadsheet->getActiveSheet()->getStyle('G')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $spreadsheet->getActiveSheet()->getStyle('O')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);

        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'Tipo Doc');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('B1', 'Número Doc.');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C1', 'Fecha');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('D1', 'Cuenta');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('E1', 'Concepto');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('F1', 'Centro de costo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('G1', 'Valor');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('H1', 'Naturaleza');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('I1', 'Identidad del tercero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('J1', 'D.V.');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('K1', 'Nombre del tercero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('L1', 'Ciudad');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('M1', 'País');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('N1', 'Cuenta bancaria');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('O1', 'Doc. fuente');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('P1', 'Código del negocio');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q1', 'Nombre del negocio');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('R1', 'Código del tercero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('S1', 'Base');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('T1', 'Inversión');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('U1', 'Obligación financiera');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('V1', 'Clase mov.');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('W1', 'Índice a afectar');


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
            'invoices.tax_exclusive_amount',
            'invoices.tax_inclusive_amount',
            'account_pay.code as account_pay_code',
            'account_pay.name as account_pay_name',
            'account_pay.name as account_pay_nature',
            'line_invoices.id as line_invoice_id',
            'line_invoices.line_extension_amount',
            'entry_credit.code as entry_credit_code',
            'entry_debit.code as entry_debit_code',
            'invoices.type_documents_id',
            'DATE_FORMAT(invoices.created_at, "%d/%m/%Y") as date',
            'customers.identification_number',
            'customers.type_document_identifications_id',
            'customers.dv',
            'customers.name as customer_name',
            'municipalities.name as municipality_name',
            'countries.name as country_name',
        ])->join('line_invoices', 'invoices.id = line_invoices.invoices_id', 'left')
            ->join('products', 'products.id = line_invoices.products_id', 'left')
            ->join('customers', 'invoices.customers_id = customers.id', 'left')
            ->join('municipalities', 'municipalities.id = customers.municipality_id', 'left')
            ->join('departments', 'departments.id = municipalities.department_id', 'left')
            ->join('countries', 'countries.id = departments.country_id', 'left')
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



        if (!empty(session('query_helisa')['invoices.type_documents_id'])) {
           $model->whereIn('invoices.type_documents_id', !empty(session('query_helisa')['invoices.type_documents_id']) ? session('query_helisa')['invoices.type_documents_id'] : [1,2,4,5]);
        }
        if(!empty(session('query_helisa')['invoices.created_at >=']) || !empty(session('query_helisa')['invoices.created_at <='])) {
            $operations = session('query_helisa');
            unset($operations['invoices.type_documents_id']);
            $model->where($operations);
        }else {
            $data['invoices.companies_id'] = Auth::querys()->companies_id;
            $model->where($data);
        }

        $invoices = $model->asObject()
           ->get()
            ->getResult();


        $arrayAccount = [];

        $m = 0;
        $i = 0;
        foreach ($invoices as $item) {

            $model = new LineInvoice();
            $lineInvoices = $model->join('line_invoice_taxs', 'line_invoices.id = line_invoice_taxs.line_invoices_id')
                ->where(['line_invoices.id' => $item->line_invoice_id])
                ->asObject()
                ->get()
                ->getResult();



            $account        = [];


            $retention = 0;
            foreach ($lineInvoices as $item2) {
                switch($item2->taxes_id) {
                 /*   case '1':
                        $account[$item->iva_code.'-'.$m] = [
                            'type_document_id'                      => $item->type_documents_id,
                            'concept'                               => $item->iva_name,
                            'value'                                 => (double) $item2->tax_amount,
                            'nature'                                => $item->iva_nature,
                            'type_document_identifications_id'      => $item->type_document_identifications_id,
                            'dv'                                    => $item->dv,
                            'customer_id'                           => $item->identification_number,
                            'resolution'                            => $item->resolution,
                            'date'                                  => $item->date,
                            'customer_name'                         => $item->customer_name,
                            'municipality_name'                     => $item->municipality_name,
                            'country_name'                          => $item->country_name,
                        ];
                        break;*/
                    case '5':
                        $account[$item->reteiva_code.'-'.$m] = [
                            'type_document_id'                      => $item->type_documents_id,
                            'concept'                               => $item->reteiva_name,
                            'value'                                 => (double) $item2->tax_amount,
                            'type_document_identifications_id'      => $item->type_document_identifications_id,
                            'dv'                                    => $item->dv,
                            'nature'                                => $item->reteiva_nature,
                            'customer_id'                           => $item->identification_number,
                            'resolution'                            => $item->resolution,
                            'date'                                  => $item->date,
                            'customer_name'                         => $item->customer_name,
                            'municipality_name'                     => $item->municipality_name,
                            'country_name'                          => $item->country_name,
                        ];
                        $retention += (double) $item2->tax_amount;
                        break;
                    case '6':
                        $account[$item->retefuente_code.'-'.$m] = [
                            'type_document_id'                      => $item->type_documents_id,
                            'concept'                               => $item->retefuente_name,
                            'value'                                 => (double) $item2->tax_amount,
                            'nature'                                => $item->retefuente_nature,
                            'customer_id'                           => $item->identification_number,
                            'resolution'                            => $item->resolution,
                            'date'                                  => $item->date,
                            'type_document_identifications_id'      => $item->type_document_identifications_id,
                            'dv'                                    => $item->dv,
                            'customer_name'                         => $item->customer_name,
                            'municipality_name'                     => $item->municipality_name,
                            'country_name'                          => $item->country_name,
                        ];
                        $retention += (double) $item2->tax_amount;
                        break;
                    case '7':
                        $account[$item->reteica_code.'-'.$m] = [
                            'type_document_id'                      => $item->type_documents_id,
                            'concept'                               => $item->reteica_name,
                            'value'                                 =>  (double) $item2->tax_amount,
                            'nature'                                => $item->reteica_nature,
                            'customer_id'                           => $item->identification_number,
                            'resolution'                            => $item->resolution,
                            'date'                                  => $item->date,
                            'type_document_identifications_id'      => $item->type_document_identifications_id,
                            'dv'                                    => $item->dv,
                            'customer_name'                         => $item->customer_name,
                            'municipality_name'                     => $item->municipality_name,
                            'country_name'                          => $item->country_name,
                        ];
                        $retention += (double) $item2->tax_amount;
                        break;
                }
                $m++;
            }


            $account[$item->account_pay_code] = [
                'type_document_id'                      => $item->type_documents_id,
                'concept'                               => $item->account_pay_name, //.' '.$item->resolution,
                'value'                                 => (double) $item->payable_amount,
                'nature'                                => $item->type_documents_id == 1 ? 'Dèbito' : 'Crédito',
                'customer_id'                           => $item->identification_number,
                'type_document_identifications_id'      => $item->type_document_identifications_id,
                'dv'                                    => $item->dv,
                'resolution'                            => $item->resolution,
                'total'                                 =>  true,
                'date'                                  => $item->date,
                'customer_name'                         => $item->customer_name,
                'municipality_name'                     => $item->municipality_name,
                'country_name'                          => $item->country_name,
            ];

            $account[$item->iva_code] = [
                'type_document_id'                      => $item->type_documents_id,
                'concept'                               => $item->iva_name,
                'value'                                 => (double)$item->tax_inclusive_amount - (double)$item->tax_exclusive_amount,
                'nature'                                => $item->type_documents_id == 1 ? 'Crédito': 'Dèbito',
                'type_document_identifications_id'      => $item->type_document_identifications_id,
                'dv'                                    => $item->dv,
                'customer_id'                           => $item->identification_number,
                'resolution'                            => $item->resolution,
                'date'                                  => $item->date,
                'customer_name'                         => $item->customer_name,
                'municipality_name'                     => $item->municipality_name,
                'country_name'                          => $item->country_name,
            ];

            //Productos
            $account[($item->type_documents_id == 1 ? $item->entry_credit_code : $item->entry_debit_code) . '-' . $i++] = [
                'type_document_id'                      => $item->type_documents_id,
                'concept'                               => $item->description,
                'value'                                 => (double)$lineInvoices[0]->line_extension_amount,
                'nature'                                => $item->type_documents_id == 1 ? 'Crédito': 'Dèbito',
                'customer_id'                           => $item->identification_number,
                'resolution'                            => $item->resolution,
                'date'                                  => $item->date,
                'type_document_identifications_id'      => $item->type_document_identifications_id,
                'dv'                                    => $item->dv,
                'customer_name'                         => $item->customer_name,
                'municipality_name'                     => $item->municipality_name,
                'country_name'                          => $item->country_name,

            ];

            $account[$item->account_pay_code]['value'] -= $retention;
            array_push($arrayAccount, [ 'invoices_id' => $item->invoice_id, 'accounts' =>  $account]);
        }

      //  echo json_encode($arrayAccount);die();


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




        $i = 2;
        foreach ($groupAccount as $item) {
            foreach ($item as $data => $key) {
                $type = '';
                switch ($key['type_document_id']) {
                    case '1':
                        $type = 'FV';
                        break;
                    case '4':
                        $type = 'NCCL';
                        break;
                    case '5':
                        $type = 'NDCL';
                        break;
                }
      
                if($key['value'] != 0) {
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('A' . $i, $type);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('B' . $i, str_pad($key['resolution'], 7, '0', STR_PAD_LEFT));
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('C' . $i, $key['date']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D' . $i, explode('-', $data)[0]);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('E' . $i, $key['concept']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('F' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('G' . $i, number_format($key['value'], '2', '.', ''));
                    if ($key['nature'] == 'Crédito') {
                        $nature = 'C';
                    } else {
                        $nature = 'D';
                    }
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('H' . $i, $nature);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('I' . $i, $key['customer_id']);
                    if($key['type_document_identifications_id'] != 6) {
                        $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, $key['dv']);
                    }else {
                        $spreadsheet->setActiveSheetIndex(0)->setCellValue('J' . $i, $key['dv']);
                    }

                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('k' . $i, $key['customer_name']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('L' . $i, $key['municipality_name']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('M' . $i, $key['country_name']);
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('N' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('O' . $i, str_pad($key['resolution'], 7, '0', STR_PAD_LEFT));
                  
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('P' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('R' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('S' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('T' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('U' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('V' . $i, '');
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('W' . $i, '');
                    $i++;
                }
            }
        }


        $spreadsheet->getActiveSheet()->setTitle('Helisa Documentos');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Helisa_Documentos.xls"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');

        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');

        $writer = IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');

       // exit;
        return redirect()->to(base_url('/report/helisa/invoice',''))->with('success', 'Documento descargado con exito.');



    }

    public function reset()
    {
        $session = session();
        $session->set('query_helisa', []);
        $session->set('dateStart_helisa', null);
        $session->set('dateEnd_helisa', null);
        return redirect()->to(base_url() . '/report/helisa/invoice');
    }

}