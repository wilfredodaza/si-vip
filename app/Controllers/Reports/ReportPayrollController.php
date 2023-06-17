<?php

namespace App\Controllers\Reports;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\Payroll;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportPayrollController    extends BaseController
{
    public function index()
    {

        if ($this->request->getJSON()) {
            $querys = ['invoices.companies_id' => Auth::querys()->companies_id];
            $datos = $this->request->getJSON();
            if (!empty($datos->dataStart)) {
                $querys = array_merge($querys, ['invoices.created_at >=' => $datos->dataStart . ' 00:00:00']);
                $session = session();
                $session->set('dateStart_payroll', $datos->dataStart);

            }

            if (!empty($datos->dataEnd)) {
                $querys = array_merge($querys, ['invoices.created_at <=' => $datos->dataEnd . ' 23:59:59']);
                $session = session();
                $session->set('dateEnd_payroll', $datos->dataEnd);
            }

            if (!empty($datos->typeDocumentId)) {       
                $querys = array_merge($querys, ['invoices.type_documents_id' => $datos->typeDocumentId]);
            }else {
                $querys = array_merge($querys, ['invoices.type_documents_id' => 9]);
            }
            $session = session();
            $session->set('querys_payroll', $querys);
        }

  

        return view('reportGeneral/report_payroll');
    }

    public function reset()
    {
        $session = session();
        $session->set('querys_payroll', []);
        $session = session();
        $session->set('dateStart_payroll', null);
        $session->set('dateEnd_payroll', null);
        return redirect()->to(base_url() . '/report_payroll');
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


        if (session('dateStart_payroll')) {
            $dateStart = explode('-', session('dateStart_payroll'));
            $mesStart = mes($dateStart[1]);
        } else {
            $dateStart = explode('-', date("Y-m-01"));
            $mesStart = mes($dateStart[1]);
        }


        if (session('dateEnd_payroll')) {
            $dateEnd = explode('-', session('dateEnd_payroll'));
            $mesEnd = mes($dateEnd[1]);
        } else {
            $dateEnd = explode('-', date("Y-m-t"));
            $mesEnd = mes($dateEnd[1]);
        }


        $model = new Payroll();
        $model->select([
            'pay.id',
            'type_document_identifications.name as type_document_identification_name',
            'customers.name',
            'customers.id as customer_id',
            'customers.identification_number',
            'customer_worker.second_name',
            'customer_worker.surname',
            'customer_worker.second_surname',
            'invoices.prefix',
            'invoices.resolution',
            'invoices.created_at',
            'invoices.id as invoice_id',
            'invoices.uuid',
            'type_documents.name as type_document_name',
            'invoice_status.name as invoice_status_name',
            '(SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) 
            FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id) AS accrueds',
            '(SELECT IFNULL(sum(ded2.payment),0) 
            FROM   payrolls pay2 LEFT JOIN  deductions ded2 ON  ded2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id) AS deductions'

        ])
            ->from('payrolls as pay')
            ->join('invoices', 'invoices.id = pay.invoice_id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
            ->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id');
        if (!empty(session('querys_payroll'))) {
                $data = !empty(session('querys_payroll')) ? session('querys_payroll') : [];
                $data['customers.companies_id']             = Auth::querys()->companies_id;
                $data['customers.type_customer_id']         = 3;
                $data['customers.deleted_at']               = null;
               // $data['type_documents_id']                  = 9;
                $model->where($data);


                session('dateStart_payroll') ?
                    $model->where(['invoices.created_at >=' => session('dateStart_payroll').' 00:00:00']) :
                    $model->where(['invoices.created_at >=' => date('Y-m-01 00:00:00')]);
                session('dateEnd_payroll') ?
                    $model->where(['invoices.created_at <=' => session('dateEnd_payroll').' 00:00:00']) :
                    $model->where(['invoices.created_at <=' => date('Y-m-t 00:00:00')]);
        }else {
            $model->where([
                'customers.companies_id'                    => Auth::querys()->companies_id,
                'customers.type_customer_id'                => 3,
                'customers.deleted_at'                      => null,
                'type_documents_id'                         => 9
            ]);

            session('dateStart_payroll') ?
                $model->where(['invoices.created_at <=' => session('dateStart_payroll').' 00:00:00']) :
                $model->where(['invoices.created_at <=' => date('Y-m-01 00:00:00')]);
            session('dateEnd_payroll') ?
                $model->where(['invoices.created_at >=' => session('dateEnd_payroll').' 00:00:00']) :
                $model->where(['invoices.created_at >=' => date('Y-m-t 00:00:00')]);
        }

          $data =   $model->groupBy([
            'pay.id',
            'customer_worker.id',
            'invoice_status.id'
        ])
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
            ->setCellValue('B4', 'Reporte de Nomina del ' .  explode(' ', $dateStart[2])[0] . ' de ' . $mesStart . ' de ' . $dateStart[0] . ' al ' . $dateEnd[2] . ' de ' . $mesEnd . ' de ' . $dateEnd[0])
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

        $spreadsheet->setActiveSheetIndex(0)
            ->setCellValue('A8', 'N° Nomina')
            ->setCellValue('B8', 'Fecha Creación')
            ->setCellValue('C8', 'Tipo de Documento')
            ->setCellValue('D8', 'Nombre del Empelado')
            ->setCellValue('E8', 'Número de Documento')
            ->setCellValue('F8', 'Estado de la Nómina')
            ->setCellValue('G8', 'Total de Devengados')
            ->setCellValue('H8', 'Total de Deducciones')
            ->setCellValue('I8', 'Pago Total')
            ->setCellValue('J8', 'CUNE');



        $i = 9;
        foreach ($data as $item) {
            $spreadsheet->setActiveSheetIndex(0)
                ->setCellValue('A' . $i, is_null($item->resolution) ? ' -  ' :$item->prefix.' '.$item->resolution)
                ->setCellValue('B' . $i, $item->created_at)
                ->setCellValue('C' . $i, $item->type_document_name)
                ->setCellValue('D' . $i, $item->name.' '.$item->second_name.' '.$item->surname.' '.$item->second_surname)
                ->setCellValue('E' . $i, $item->identification_number)
                ->setCellValue('F' . $i, $item->invoice_status_name)
                ->setCellValue('G' . $i, $item->accrueds)
                ->setCellValue('H' . $i, $item->deductions)
                ->setCellValue('I' . $i, $item->accrueds -  $item->deductions)
                ->setCellValue('J' . $i, $item->uuid);
            $i++;
        }

        $spreadsheet->getActiveSheet()->setTitle('Reporte_de_Nomina');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Reporte_de_Nomina.xls"');
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