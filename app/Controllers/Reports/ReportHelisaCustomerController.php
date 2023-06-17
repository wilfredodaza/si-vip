<?php

namespace App\Controllers\Reports;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\LineInvoice;
use App\Models\TypeDocument;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportHelisaCustomerController extends BaseController
{
    public function index()
    {

        $typeDocument = new TypeDocument();
        $typeDocuments = $typeDocument->whereIn('id', [1, 2, 3, 4, 5])->asObject()->get()->getResult();

        if ($this->request->getJSON()) {
            $querys = ['customers.companies_id' => Auth::querys()->companies_id];
            $datos = $this->request->getJSON();

            if (!empty($datos->dataStart)) {
                $querys = array_merge($querys, ['customers.created_at >=' => $datos->dataStart . ' 00:00:00']);
                $session = session();
                $session->set('dateStart_helisa', $datos->dataStart);
            }
            if (!empty($datos->dataEnd)) {
                $querys = array_merge($querys, ['customers.created_at <=' => $datos->dataEnd . ' 23:59:59']);
                $session = session();
                $session->set('dateEnd_helisa', $datos->dataEnd);
            }

            $session = session();
            $session->set('query_helisa_customer', $querys);
            echo json_encode($querys);
            die();
        }
        return view('reportGeneral/report_helisa_customer', ['typeDocuments' => $typeDocuments]);
    }

    public function reportHelisaInvoice()
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

        //Columnas A8 Hasta Q8
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ]
        ];

        $spreadsheet->getActiveSheet()->getStyle('A1:CJ1')->applyFromArray($styleArray);

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
        $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BA')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BB')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BC')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BD')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BE')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BF')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BG')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BH')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BI')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BJ')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BK')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BL')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BM')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BN')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BO')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BP')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BQ')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BR')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BS')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BT')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BU')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BV')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BW')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BY')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('BZ')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CA')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CB')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CC')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CD')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CE')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CF')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CG')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CH')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CI')->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getColumnDimension('CJ')->setAutoSize(true);


        $spreadsheet->setActiveSheetIndex(0)->setCellValue('A1', 'Código');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('B1', 'Identidad');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('C1', 'D.V.');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('D1', 'Clase');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('E1', 'Nombre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('F1', 'Local');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('G1', 'Representante');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('H1', 'Encargado de pagos');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('I1', 'Encargado de compras');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('J1', 'Dirección');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('K1', 'Dirección de entregas');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('L1', 'Teléfonos');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('M1', 'Fax');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('N1', 'Ciudad');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('O1', 'Nombre de la ciudad');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('P1', 'Apartado');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q1', 'Correo electrónico');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('R1', 'Zona');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('S1', 'Grupo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('T1', 'Campo libre uno');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('U1', 'Campo libre dos');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('V1', 'Tolerancia');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('W1', 'Cupo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('X1', 'Cliente desde');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('Y1', 'Días de pago');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('Z1', 'Observaciones');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AA1', 'Régimen');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AB1', 'Área ICA');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AC1', 'Forma de pago');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AD1', 'Lista de precios de artículos');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AE1', 'Lista de precios de servicios');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AF1', 'Saldo inicial');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AG1', 'Débitos Enero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AH1', 'Débitos Febrero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AI1', 'Débitos Marzo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AJ1', 'Débitos Abril');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AK1', 'Débitos Mayo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AL1', 'Débitos Junio');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AM1', 'Débitos Julio');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AN1', 'Débitos Agosto');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AO1', 'Débitos Septiembre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AP1', 'Débitos Octubre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AQ1', 'Débitos Noviembre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AR1', 'Débitos Diciembre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AS1', 'Débitos Ajustes');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AT1', 'Débitos Cancelación');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AU1', 'Créditos Enero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AV1', 'Créditos Febrero');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AW1', 'Créditos Marzo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AX1', 'Créditos Abril');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AY1', 'Créditos Mayo');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('AZ1', 'Créditos Junio');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BA1', 'Créditos Julio');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BB1', 'Créditos Agosto');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BC1', 'Créditos Septiembre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BD1', 'Créditos Octubre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BE1', 'Créditos Noviembre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BF1', 'Créditos Diciembre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BG1', 'Créditos Ajustes');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BH1', 'Créditos Cancelación');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BI1', 'Saldo actual');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BJ1', 'Código del vendedor');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BK1', 'Cuenta de cartera');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BL1', 'Cartera');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BM1', 'Primer apellido');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BN1', 'Segundo apellido');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BO1', 'Primer nombre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BP1', 'Segundo nombre');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BQ1', 'Número de móvil');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BR1', 'Código de área');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BS1', 'Naturaleza');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BT1', 'Cód. de actividad económica');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BU1', 'Tipo ID contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BV1', 'Ident. contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BW1', 'Nombre contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BX1', 'Tlf. contecto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BY1', 'Correo contecto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('BZ1', 'Dig. Verif. contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CA1', 'Régimen contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CB1', 'País contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CC1', 'Dpto. contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CD1', 'Ciudad contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CE1', 'Locald. contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CF1', 'Barrio contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CG1', 'Responsabilidad fiscal contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CH1', 'Nombre comercial contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CI1', 'Contrato mandato contacto FE');
        $spreadsheet->setActiveSheetIndex(0)->setCellValue('CJ1', 'Régimen cliente FE');

        $model = new Customer();
      $model->select([
            'customers.identification_number',
            'customers.dv',
            'customers.type_document_identifications_id',
            'customers.name',
            'customers.phone',
            'customers.address',
            'customers.email',
            'customers.type_liability_id',
            'customers.type_customer_id',
            'municipalities.name as  municipality_name',
            'municipalities.code as  municipality_code',
      ])->join('municipalities', 'municipalities.id = customers.municipality_id')
      ->whereIn('customers.type_customer_id', [1, 2]);

      if(!empty(session('query_helisa_customer')['customers.created_at >=']) || !empty(session('query_helisa_customer')['customers.created_at <='])) {
          $operations = session('query_helisa_customer');
          $model->where($operations);
      }else {
          $data['customers.companies_id'] = Auth::querys()->companies_id;
          $model->where($data);
      }
      $customers = $model->asObject()
          ->get()
          ->getResult();

       
        $i = 2;
        foreach ($customers as $item){
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('A'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.$i, $item->identification_number);
            if($item->type_document_identifications_id == 6) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C'.$i, $item->dv);
            }else {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('C'.$i, $item->dv);
            }

            switch ($item->type_document_identifications_id) {
                case 6:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$i, 'A');
                    break;
                case 3:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$i, 'C');
                    break;
                case 1:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$i, 'R');
                    break;
                case 7:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$i, 'P');
                    break;
                default:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('D'.$i, 'O');
                    break;
            }

            $spreadsheet->setActiveSheetIndex(0)->setCellValue('E'.$i, $item->name);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('F'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('G'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('H'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('I'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('J'.$i, $item->address);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('K'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('L'.$i, $item->phone);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('M'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('N'.$i, $item->municipality_code);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('O'.$i, $item->municipality_name);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('P'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('Q'.$i, $item->email);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('R'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('S'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('T'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('U'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('V'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('W'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('X'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('Y'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('Z'.$i, '');
            switch ($item->type_liability_id) {
                case 112:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('AA1', 'S');
                    break;
                case 7:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('AA1', 'G');
                    break;
                default:
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('AA1', 'C');
                    break;
            }

            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AB'.$i, 'S');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AC'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AD'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AE'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AF'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AG'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AH'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AI'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AJ'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AK'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AL'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AM'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AN'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AO'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AP'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AQ'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AR'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AS'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AT'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AU'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AV'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AW'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AX'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AY'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('AZ'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BA'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BB'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BC'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BD'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BE'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BF'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BG'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BH'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BI'.$i, '0,00');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BJ'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BK'.$i, '');
            if($item->type_customer_id == 1) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BL'.$i, 'CLIENTES');
            }else if($item->type_customer_id == 2){
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BL'.$i, 'CLIENTES');
            }

            if($item->type_document_identifications_id == 6) {
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BM'.$i, '');
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BN'.$i, '');
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BO'.$i, '');
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BP'.$i, '');
            }else {
                $name = explode(' ', $item->name);
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BM'.$i, isset($name[2]) ? $name[2] : '');
                if(isset($name[3])) {
                    $nameTotal = '';
                    for($l = 3; $l < count($name); $l++){
                        $nameTotal.= $name[$l];
                    }
                    $spreadsheet->setActiveSheetIndex(0)->setCellValue('BN'.$i, $nameTotal);
                }
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BO'.$i, isset($name[0]) ? $name[0] : '');
                $spreadsheet->setActiveSheetIndex(0)->setCellValue('BP'.$i, isset($name[1]) ? $name[1] : '');


            }

            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BQ'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BR'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BS'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BT'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BU'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BV'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BW'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BX'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BY'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('BZ'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CA'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CB'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CC'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CD'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CE'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CF'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CG'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CH'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CI'.$i, '');
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('CJ'.$i, '');
            $i++;
        }



        $spreadsheet->getActiveSheet()->setTitle('Helisa Clientes');
        $spreadsheet->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Helisa_Clientes.xls"');
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
        $session->set('query_helisa_customer', []);
        $session->set('dateStart_helisa', null);
        $session->set('dateEnd_helisa', null);
        return redirect()->to(base_url() . '/report/helisa/invoice');
    }
}