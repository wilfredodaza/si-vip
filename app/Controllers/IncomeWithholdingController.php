<?php



/***
 * 
 * Esta clase es encargada de relizar la parte de ingresos
 * y retenciones de los empleados
 * @author Wilson Andres Bachiller Ortiz <wilson@mawii.com.co>
 * @version 1.0.0
 * 
 */

namespace App\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Payroll;
use App\Controllers\Api\Auth;
use App\Models\Accrued;
use App\Models\TypeDocumentIdentifications;
use App\Traits\ExcelValidationTrait;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class IncomeWithholdingController extends BaseController
{


    
    use ExcelValidationTrait;

    /**
     * Metodo encargado de la vista de la tabla
     * de retenciones e ingresos
     * @return string
     */

    public function index()
    {
        $model = new Invoice();
        $model = $model->select([
            'invoices.id',
            'customers.identification_number',
            'customers.name',
            'customer_worker.second_name',
            'customer_worker.surname',
            'customer_worker.second_surname',
            'invoices.resolution',
            'invoices.issue_date',
            'type_document_identifications.name as type_document_name'
        ])  ->join('customers', 'customers.id = invoices.customers_id')
            ->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->join('type_document_identifications', 'type_document_identifications.id = customers.type_document_identifications_id')
            ->where([
                'invoices.companies_id'          => Auth::querys()->companies_id,
                'type_documents_id'              => 111,
                'customers.companies_id'         => Auth::querys()->companies_id,
        ]);
        if(count($this->searchShow()) != 0) {
            $model =  $model->where($this->searchShow());
        }
        $documents = $model->asObject();


        $model = new TypeDocumentIdentifications();
        $typeDocumentIdentifications = $model
            ->asObject()
            ->get()
            ->getResult();


        return view('income_withholding/index', [
            'documents'                     => $documents->paginate(10),
            'pager'                         => $documents->pager,
            'typeDocumentIdentifications'   => $typeDocumentIdentifications,
            'searchShow'                    => $this->searchShow()
        ]);
    }

    /**
     * Metodo encargado de la vista de cargue
     * de retenciones e ingresos
     * @return string
     */

    public function create()
    {
        return view('income_withholding/import');
    }



    /**
     * @param $id
     * @return void
     * @throws \Mpdf\MpdfException
     */

    public function pdf($id = null) : void
    {
        $model  = new Invoice();
        $invoice = $model->select([
            'payrolls.settlement_start_date',
            'payrolls.settlement_end_date',
            'invoices.issue_date',
            'invoices.id',
            'invoices.resolution',
            'payrolls.id as payroll_id',
            'companies.identification_number as nit',
            'companies.dv',
            'companies.company',
            'customers.name',
            'customer_worker.second_name',
            'customer_worker.surname',
            'customer_worker.second_surname',
            'customers.identification_number',
            'type_document_identifications.code as identification_code',
            'municipalities.name as municipality_name',
            'municipalities.code as municipality_code',
            'departments.code as department_code'
            ])
        ->where([
            'invoices.id'               => $id,
            'invoices.companies_id'     => Auth::querys()->companies_id,
            'customers.companies_id'     => Auth::querys()->companies_id,
        ])
        ->join('customers', 'customers.id = invoices.customers_id')
        ->join('payrolls', 'payrolls.invoice_id = invoices.id')
        ->join('companies', 'companies.id = invoices.companies_id')
        ->join('municipalities', 'customers.municipality_id = municipalities.id')
        ->join('departments', 'departments.id = municipalities.department_id')
        ->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
        ->asObject()
        ->first();

        if(is_null($invoice)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $model = new Accrued();
        $accrueds = $model->where(['payroll_id' => $invoice->payroll_id])
        ->asObject()
        ->get()
        ->getResult();


        $stylesheet = file_get_contents(base_url() . '/assets/css/income_withholding.css');
        $mpdf  = new \Mpdf\Mpdf([
            'default_font_size'             => 9,
            'default_font'                  => 'Roboto',
            'margin_left'                   => 7,
            'margin_right'                  => 7,
            'margin_top'                    => 8.3,
            'margin_bottom'                 => 3,
            'margin_header'                 => 0,
            'margin_footer'                 => 0
        ]);


        $mpdf->WriteHtml($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLHeader(view('pdfs/income_withholding/header', []));
        $mpdf->WriteHtml(view('pdfs/income_withholding/body', ['invoice' => $invoice, 'accrueds' => $accrueds]), \Mpdf\HTMLParserMode::HTML_BODY);
        $mpdf->Output();

        die();
    }

    /**
     * @return void
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \ReflectionException
     */

    public function importExcel()
    {
        $validation = service('validation');
        $validation->setRules([
            'file' => 'uploaded[file]|mime_in[file,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel]|ext_in[file,xlsx,xls]',
        ], [
            'file' => [
                'uploaded'      => 'El archivo no es un archivo cargado válido.',
                'mime_in'       => 'El archivo debe tener extension xlsx o xls.'
            ]
        ]);

        if(!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors',implode('<br/>',(Array)$validation->getErrors()));
        }


        if($file = $this->request->getFile('file')) {
            $excel = IOFactory::load($file->getTempName());
            $documents = [];
            $sheet = $excel->getSheet(0);
            $largestRowNumber = $sheet->getHighestRow();
            $count = 1;
            for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
                $this->required($sheet->getCell('A' . $rowIndex)->getValue(), 'ID', 'A' . $rowIndex);
                $this->required($sheet->getCell('F' . $rowIndex)->getValue(), 'Número de identificación', 'F' . $rowIndex);
                $this->required($sheet->getCell('K' . $rowIndex)->getValue(), 'Fecha: Periodo certificado desde', 'K' . $rowIndex);
                $this->required($sheet->getCell('L' . $rowIndex)->getValue(), 'Fecha: Periodo certificado hasta', 'L' . $rowIndex);
                $this->required($sheet->getCell('M' . $rowIndex)->getValue(), 'Fecha: De emisión de documento', 'M' . $rowIndex);
                $this->required($sheet->getCell('Q' . $rowIndex)->getValue(), 'Pagos por salarios o emolumentos eclesiásticos', 'Q' . $rowIndex);
                $this->required($sheet->getCell('R' . $rowIndex)->getValue(), 'Pagos realizados con bonos electrónicos o de papel de servicio, cheques, tarjetas, vales, etc.', 'R' . $rowIndex);
                $this->required($sheet->getCell('S' . $rowIndex)->getValue(), 'Pagos por honorarios', 'S' . $rowIndex);
                $this->required($sheet->getCell('T' . $rowIndex)->getValue(), 'Pagos por servicios', 'T' . $rowIndex);
                $this->required($sheet->getCell('U' . $rowIndex)->getValue(), 'Pagos por comisiones', 'U' . $rowIndex);
                $this->required($sheet->getCell('V' . $rowIndex)->getValue(), 'Pagos por prestaciones sociales', 'V' . $rowIndex);
                $this->required($sheet->getCell('W' . $rowIndex)->getValue(), 'Pagos por viáticos', 'W' . $rowIndex);
                $this->required($sheet->getCell('X' . $rowIndex)->getValue(), 'Pagos por gastos de representación', 'X' . $rowIndex);
                $this->required($sheet->getCell('Y' . $rowIndex)->getValue(), 'Pagos por compensaciones por el trabajo asociado cooperativo', 'Y' . $rowIndex);
                $this->required($sheet->getCell('Z' . $rowIndex)->getValue(), 'Otros pagos', 'Z' . $rowIndex);
                $this->required($sheet->getCell('AA' . $rowIndex)->getValue(), 'Cesantías e intereses de cesantías efectivamente pagadas en el periodo', 'AA' . $rowIndex);
                $this->required($sheet->getCell('AB' . $rowIndex)->getValue(), 'Cesantías consignadas al fondo de cesantías', 'AB' . $rowIndex);
                $this->required($sheet->getCell('AC' . $rowIndex)->getValue(), 'Pensiones de jubilación, vejez o invalidez', 'AC' . $rowIndex);
                $this->required($sheet->getCell('AD' . $rowIndex)->getValue(), 'Total de ingresos brutos (Sume 36 a 48)', 'AD' . $rowIndex);
                $this->required($sheet->getCell('AE' . $rowIndex)->getValue(), 'Aportes obligatorios por salud a cargo del trabajador', 'AE' . $rowIndex);
                $this->required($sheet->getCell('AF' . $rowIndex)->getValue(), 'Aportes obligatorios a fondos de pensiones y solidaridad pensional a cargo del trabajador', 'AF' . $rowIndex);
                $this->required($sheet->getCell('AG' . $rowIndex)->getValue(), 'Cotizaciones voluntarias al régimen de ahorro individual con solidaridad - RAIS', 'AG' . $rowIndex);
                $this->required($sheet->getCell('AH' . $rowIndex)->getValue(), 'Aportes voluntarios a fondos de pensiones', 'AH' . $rowIndex);
                $this->required($sheet->getCell('AI' . $rowIndex)->getValue(), 'Aportes a cuentas AFC', 'AI' . $rowIndex);
                $this->required($sheet->getCell('AJ' . $rowIndex)->getValue(), 'Valor de la retención en la fuente por rentas de trabajo y pensiones', 'AJ' . $rowIndex);
                $this->validExistDB($sheet->getCell('F'.$rowIndex)->getValue(),'Número de identificación', 'F'.$rowIndex,'customers', 'identification_number', true);
            }

            if (count($this->getErrors()) > 0) {
                return redirect()->back()->with('errors', implode('<br>', $this->getErrors()));
            }


            for ($rowIndex = 2; $rowIndex <= $largestRowNumber; $rowIndex++) {
                $model = new Customer();
                $customerId = $model->select(['id'])
                    ->where([
                        'identification_number'     => $sheet->getCell('F' . $rowIndex)->getValue(),
                        'companies_id'              => Auth::querys()->companies_id
                    ])
                    ->asObject()
                    ->first();

                $model = new Invoice();
                $invoiceId = $model->insert([
                    'type_documents_id'     => 111,
                    'customers_id'          => $customerId->id,
                    'companies_id'          => Auth::querys()->companies_id,
                    'issue_date'            => $this->tranformDate($sheet->getCell('M' . $rowIndex)->getValue()),
                    'resolution'            => $sheet->getCell('A' . $rowIndex)->getCalculatedValue()
                ]);

                $model = new Payroll();
                $payrollId = $model->insert([
                    'invoice_id'                => $invoiceId,
                    'settlement_start_date'     => $this->tranformDate($sheet->getCell('K' . $rowIndex)->getValue()),
                    'settlement_end_date'       => $this->tranformDate($sheet->getCell('L' . $rowIndex)->getValue())
                ]);

                $this->accrued($payrollId, 1, $sheet->getCell('Q' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 30, $sheet->getCell('R' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 43, $sheet->getCell('S' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 44, $sheet->getCell('T' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 34, $sheet->getCell('U' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 45, $sheet->getCell('V' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 10, $sheet->getCell('W' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 46, $sheet->getCell('X' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 47, $sheet->getCell('Y' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 26, $sheet->getCell('Z' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 16, $sheet->getCell('AA' . $rowIndex)->getCalculatedValue(), $sheet->getCell('AB' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 48, $sheet->getCell('AC' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 55, $sheet->getCell('AD' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 49, $sheet->getCell('AE' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 50, $sheet->getCell('AF' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 51, $sheet->getCell('AG' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 52, $sheet->getCell('AH' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 53, $sheet->getCell('AI' . $rowIndex)->getCalculatedValue());
                $this->accrued($payrollId, 54, $sheet->getCell('AJ' . $rowIndex)->getCalculatedValue());
            }
        }
        return redirect()->to(base_url('income_withholding'))->with('success', 'El archivo fue cargado correctamente.');
    }

    /**
     * @param $payrollId
     * @param $typeAccrued
     * @param $payment
     * @param $otherPayment
     * @return void
     * @throws \ReflectionException
     */

    public function accrued($payrollId,  $typeAccrued, $payment, $otherPayment = null)
    {
            $model = new Accrued();
            $model->insert([
                'payroll_id'        => $payrollId,
                'payment'           => $payment,
                'type_accrued_id'   => $typeAccrued,
                'other_payments'    => $otherPayment
            ]);
    }


    /**
     *
     * @return array
     */
    public function searchShow()
    {
        $data = [];
        if(!empty($this->request->getGet('first_name'))) {
            $data['customers.name'] = $this->request->getGet('first_name');
        }

        if(!empty($this->request->getGet('second_name'))) {
            $data['customer_worker.second_name'] = $this->request->getGet('second_name');
        }
        if(!empty($this->request->getGet('surname'))) {
            $data['customer_worker.surname'] = $this->request->getGet('surname');
        }
        if(!empty($this->request->getGet('second_surname'))) {
            $data['customer_worker.second_surname'] = $this->request->getGet('second_surname');
        }

        if(!empty($this->request->getGet('type_document_id'))) {
            $data['customers.type_document_identifications_id'] = $this->request->getGet('type_document_id');
        }

        if(!empty($this->request->getGet('identification_number'))) {
            $data['customers.identification_number'] = $this->request->getGet('identification_number');
        }


        return $data;
    }
}