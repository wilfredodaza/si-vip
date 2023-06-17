<?php


namespace App\Controllers;


use App\Controllers\Api\Auth;
use App\Controllers\companies\Functions_Payroll;
use App\Controllers\companies\Payroll_altara;
use App\Controllers\companies\Payroll_am;
use App\Controllers\companies\Payroll_andina_rodillos;
use App\Controllers\companies\Payroll_as_nomina;
use App\Controllers\companies\Payroll_biotech;
use App\Controllers\companies\Payroll_commure;
use App\Controllers\companies\Payroll_etipress;
use App\Controllers\companies\Payroll_fjm;
use App\Controllers\companies\Payroll_gelt;
use App\Controllers\companies\Payroll_giron;
use App\Controllers\companies\Payroll_grancolombiana;
use App\Controllers\companies\Payroll_grancolservig;
use App\Controllers\companies\Payroll_heal_room;
use App\Controllers\companies\Payroll_ingemol;
use App\Controllers\companies\Payroll_market;
use App\Controllers\companies\Payroll_master_energy;
use App\Controllers\companies\Payroll_melonn;
use App\Controllers\companies\Payroll_mubler;
use App\Controllers\companies\Payroll_niver;
use App\Controllers\companies\Payroll_onza;
use App\Controllers\companies\Payroll_polyuprotec;
use App\Controllers\companies\Payroll_punto_empresarial;
use App\Controllers\companies\Payroll_sanMarcos;
use App\Controllers\companies\Payroll_servimos;
use App\Controllers\companies\Payroll_serya;
use App\Controllers\companies\Payroll_simetrik;
use App\Controllers\companies\Payroll_sinergia;
use App\Controllers\companies\Payroll_suarez_leon;
use App\Controllers\companies\Payroll_sumer;
use App\Controllers\companies\Payroll_tampa;
use App\Controllers\companies\Payroll_transgalaxia;
use App\Controllers\companies\Payroll_tyc;
use App\Controllers\companies\Payroll_tyc_contadores;
use App\Controllers\companies\Payroll_ventas_institucionales;
use App\Controllers\companies\Payroll_yaraguies;
use App\Controllers\companies\Payroll_Z_seguridad;
use App\Models\Cargue;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\Period;
use App\Models\SubPeriod;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use CodeIgniter\Model;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ImportPayrollController extends BaseController
{
    protected $validations;
    protected $tyc;
    protected $etipress;
    protected $polyuprotec;
    protected $zona_de_seguridad;
    protected $transgalaxia;
    protected $servimos;
    protected $niver;
    protected $payroll_periods;
    protected $model_cargue;
    protected $model_period;
    protected $grancolservig;
    protected $grancolombiana;
    protected $sanmarcos;
    protected $masterenergy;
    protected $simetrik;
    protected $suarez_leon;
    protected $punto_empresarial;
    protected $ingemol;
    protected $subperiodo;
    protected $function_payroll;
    private $companies_as_nomina;
    private $companies_tyc;
    private $commure;
    private $sumer;
    private $mubler;
    private $fjm;
    private $melonn;
    private $heal_room;
    private $onza;
    private $tyc_contadores;
    private $am;
    private $serya;
    private $altara;
    private $andina_rodillos;
    private $ventas_institucionales;
    private $biotech;
    private $tampa;
    public $payroll;
    public $market;
    private $gelt;
    private $sinergia;
    private $giron;
    private $yaraguies;


    public function __construct()
    {
        //controlador validacion
        $this->validations = new DataValidationController();
        //controlador de empresas
        $this->tyc = new Payroll_tyc();
        $this->etipress = new Payroll_etipress();
        $this->polyuprotec = new Payroll_polyuprotec();
        $this->zona_de_seguridad = new Payroll_Z_seguridad();
        $this->transgalaxia = new Payroll_transgalaxia();
        $this->servimos = new Payroll_servimos();
        $this->niver = new Payroll_niver();
        $this->grancolservig = new Payroll_grancolservig();
        $this->grancolombiana = new Payroll_grancolombiana();
        $this->sanmarcos = new Payroll_sanMarcos();
        $this->masterenergy = new Payroll_master_energy();
        $this->simetrik = new Payroll_simetrik();
        $this->suarez_leon = new Payroll_suarez_leon();
        $this->ingemol = new Payroll_ingemol();
        $this->punto_empresarial = new Payroll_punto_empresarial();
        $this->commure = new Payroll_commure();
        $this->sumer = new Payroll_sumer();
        $this->mubler = new Payroll_mubler();
        $this->fjm = new Payroll_fjm();
        $this->melonn = new Payroll_melonn();
        $this->heal_room = new Payroll_heal_room();
        $this->onza = new Payroll_onza();
        $this->tyc_contadores = new Payroll_tyc_contadores();
        $this->am = new Payroll_am();
        $this->serya = new Payroll_serya();
        $this->altara = new Payroll_altara();
        $this->andina_rodillos = new Payroll_andina_rodillos();
        $this->ventas_institucionales = new Payroll_ventas_institucionales();
        $this->biotech = new Payroll_biotech();
        $this->tampa = new Payroll_tampa();
        $this->market = new Payroll_market();
        $this->gelt = new Payroll_gelt();
        $this->sinergia = new Payroll_sinergia();
        $this->giron = new Payroll_giron();
        $this->yaraguies = new Payroll_yaraguies();
        //modelos
        $this->payroll_periods = new PayrollPeriod();
        $this->model_cargue = new Cargue();
        $this->model_period = new Period();
        $this->subperiodo = new SubPeriod();
        $this->function_payroll = new Functions_Payroll();
        $this->payroll = new  Payroll();
        //nit companies
        $this->companies_as_nomina = [
            800174904, // etipress
            900306514, // zona de seguridad
            830015914, //polyuprotec
            800210669, // transgalaxia
            900739531, // servimos integral
            860000824, // Niver
            830065471, // grancolservig
            800188477, // grancolombiana
            830090360, // san marcos
            901131030, // master energy
            860054862, // ingemol
            900803958, // altara
            830056362, // andina rodilos
            800198348,  // ventas institucionales
            900444608, // iplanet
            901084243,  //market
            900804617, // giron
            901347237 //yaraguies
        ];
        $this->companies_tyc = [
            900782726, //tyc
            901030030, // simetrik
            901400629, // commure
            901441683, // sumer
            901233605, // mubler
            901515179, // fjm
            901427659, // melonn
            901433542, // heal room
            901465526, // onza
            901005608, // tyc contadores
            901112882, // biotech
            901525415 //gelt
        ];
    }

    public function index()
    {
        $model_periods = [];
        $model_periods_active = [];
        $payroll_period = $this->payroll_periods->get()->getResult();
        $periods_habilitados = $this->model_period->get()->getResult();
        //echo json_encode($periods_habilitados);die();
        $cargues = $this->model_cargue->where(['nit' => company()->identification_number])->get()->getResult();
        //meses cargados
        foreach ($cargues as $cargue) {
            //$date = strtotime($cargue->date);
            foreach ($periods_habilitados as $period_habilitado) {
                if ($cargue->year == $period_habilitado->year) {
                    //meses para validar
                    if ($cargue->status == 'Inactive') {
                        if (!in_array($cargue->month_payroll, $model_periods_active)) {
                            array_push($model_periods_active, $cargue->month_payroll);
                            array_push($model_periods,
                                [   'month' => $cargue->month_payroll,
                                    'year' => $cargue->year,
                                    'name_month' =>  $this->function_payroll->getType_month($cargue->month_payroll),
                                    'document' => $cargue->type_document_payroll
                                ]);
                        }
                    }
                }
            }
        }
        if (in_array(company()->identification_number, $this->companies_tyc)) {
            $vista = 'import/ImportPayrollTyc';
        } else {
            $vista = 'import/ImportPayroll';
        }
        return view($vista, ['payroll_periods' => $payroll_period, 'periods_active' => $model_periods]);
    }

    public function monthvalidation()
    {
        //header('Content-Type: application/json');
        $meses_activos = [];
        $meses_activos_any = [];
        $meses_habilitados = [];
        $meses_habilitados_ajustes = [];
        $meses_cargue = [];
        $cargues = $this->model_cargue
            ->select('month_payroll')
            ->where(['nit' => company()->identification_number, 'year' => $_POST['year']])
            ->groupBy('month_payroll')
            ->first();
        $periods_habilitados = $this->model_period->where(['year' => $_POST['year']])->get()->getResult();
        foreach ($periods_habilitados as $period_habilitado_1) {
            array_push($meses_activos, $this->function_payroll->getType_month_int($period_habilitado_1->month));
            array_push($meses_activos_any, ['id' => $this->function_payroll->getType_month_int($period_habilitado_1->month), 'name' => $period_habilitado_1->month]);
        }
        if ($_POST['document'] == 10) {
            $cargue_ajustes = $this->model_cargue->select(['month_payroll','period_id'])
                ->where(['nit' => company()->identification_number, 'year' => $_POST['year'], 'status' => 'active'])
                ->groupBy(['month_payroll'])
                ->get()
                ->getResult();
            //echo json_encode($cargue_ajustes);die();
            foreach ($cargue_ajustes as $cargue_ajuste) {
                $workers = $this->payroll->select('count(payrolls.id) as workers')
                    ->join('invoices', 'payrolls.invoice_id = invoices.id')
                    ->join('periods', 'periods.id = payrolls.period_id')
                    ->where([
                        'periods.id'                        => $cargue_ajuste->period_id,
                        'invoices.companies_id'             => Auth::querys()->companies_id,
                        'invoices.type_documents_id'        => 9
                    ])
                    ->asObject()
                    ->first();
                $emiter = $this->payroll->select('count(invoices.invoice_status_id) as emiter')
                    ->join('invoices', 'payrolls.invoice_id = invoices.id')
                    ->where([
                        'payrolls.period_id'                => $cargue_ajuste->period_id,
                        'invoices.invoice_status_id'        => 14,
                        'invoices.type_documents_id'        => 9,
                        'invoices.companies_id'             => Auth::querys()->companies_id
                    ])
                    ->asObject()
                    ->first();

                if(is_null($emiter)) {
                    $emiter = (Object) $emiter['emiter'] = 0;
                }
                if($emiter->emiter ==  $workers->workers){
                    array_push($meses_habilitados_ajustes, ['id' => $cargue_ajuste->month_payroll, 'name' => $this->function_payroll->getType_month($cargue_ajuste->month_payroll)]);
                }
            }
            echo json_encode($meses_habilitados_ajustes);
        } else {
            if (!is_null($cargues) && !in_array(company()->identification_number,$this->companies_tyc)) {
                foreach ($cargues as $cargue) {
                    array_push($meses_cargue, $cargue->month_payroll);
                }
                foreach ($meses_activos as $mes_activo){
                    if (!in_array($mes_activo,$meses_cargue)) {
                        array_push($meses_habilitados, ['id' => $mes_activo, 'name' => $this->function_payroll->getType_month($mes_activo)]);
                    }
                }
                echo json_encode($meses_habilitados);
            } else {
                echo json_encode($meses_activos_any);
            }
        }
    }

    public function Load(): \CodeIgniter\HTTP\RedirectResponse
    {
        $period = $_POST['period'];
        $response = 'Documentos cargados con exíto.';
        $id_period = $this->function_payroll->model_cargue(company()->identification_number, $_POST['month'], $_POST['year'], $_POST['document']);
        if (is_null($id_period)) {
            return redirect()->to(base_url() . '/import/payroll')->with('errors', 'El mes a liquidar ya se encuentra cargado.');
        }
        $documents = $this->request->getFiles();
        $number = count($documents['file']);
        $dates = $_POST['payment_dates'];

        $o = 0;
        $result = true;
        for ($i = 1; $i <= $number; $i++) {
            if($_POST['document'] == 10){
                $id_sub_periodo = $this->subperiodo->insert([
                    'name' => $this->function_payroll->getType_month($_POST['month']) . '_' . $_POST['year'] .'_ ajuste',
                    'company_id' => company()->id
                ]);
            }
            $result_cargue = $this->load_file_asnomina($documents['file'][$o], 1, $_POST['month'], $period, ($id_sub_periodo ?? $i), $dates, $id_period, $_POST['year'], $_POST['document']);
            if (!$result_cargue) {
                $result = false;
                break;
            }
            $o++;
        }
        if ($result) {
            return redirect()->to(base_url() . '/import/payroll')->with('success', $response);
        } else {
            return redirect()->to(base_url() . '/import/payroll')->with('errors', 'Inconveniente al cargar el archivo');
        }

    }

    public function uploadPayroll($id)
    {
        $result = $this->validations->validation($id, $_POST['month']);
        if ($result['status'] == 'error') {
            return redirect()->to(base_url() . '/import/payroll')->with('error', $result['data']);
        } elseif ($result['status'] == 'vacio') {
            return redirect()->to(base_url() . '/import/payroll')->with('vacio', 'No se encontraron datos para validar.');
        } elseif ($result['status'] == 'success') {
            switch ($id) {
                case '900782726':
                    $this->tyc->workers($id, $_POST['month']);
                    break;
                case '830015914':
                    $this->polyuprotec->init_polyuprotec($id, $_POST['month']);
                    break;
                case '800174904':
                    $this->etipress->init_etypress($id, $_POST['month']);
                    break;
                case '900306514':
                    $this->zona_de_seguridad->init_zona_de_seguridad($id, $_POST['month']);
                    break;
                case '800210669':
                    $this->transgalaxia->init_transgalaxia($id, $_POST['month']);
                    break;
                case '900739531':
                    $this->servimos->init_servimos($id, $_POST['month']);
                    break;
                case '860000824':
                    $this->niver->init_niver($id, $_POST['month']);
                    break;
                case '830065471':
                    $this->grancolservig->init_grancolservig($id, $_POST['month']);
                    break;
                case '800188477':
                    $this->grancolombiana->init_grancolombiana($id, $_POST['month']);
                    break;
                case '830090360':
                    $this->sanmarcos->init_sanMarcos($id, $_POST['month']);
                    break;
                case '901131030':
                    $this->masterenergy->init_master_energy($id, $_POST['month']);
                    break;
                case '901030030':
                    $this->simetrik->workers($id, $_POST['month']);
                    break;
                case '900515864':
                    $this->suarez_leon->workers($id, $_POST['month']);
                    break;
                case '860054862':
                    $this->ingemol->init_ingemol($id, $_POST['month']);
                    break;
                case '900082400':
                    $this->punto_empresarial->workers($id, $_POST['month']);
                    break;
                case '830010090':
                    $this->am->workers($id, $_POST['month']);
                    break;
                case '900061320':
                    $this->serya->workers($id, $_POST['month']);
                    break;
                case  '901400629':
                    $this->commure->workers($id, $_POST['month']);
                    break;
                case '901441683':
                    $this->sumer->workers($id, $_POST['month']);
                    break;
                case '901233605':
                    $this->mubler->workers($id, $_POST['month']);
                    break;
                case '901515179':
                    $this->fjm->workers($id, $_POST['month']);
                    break;
                case '901427659':
                    $this->melonn->workers($id, $_POST['month']);
                    break;
                case '901433542':
                    $this->heal_room->workers($id, $_POST['month']);
                    break;
                case '901465526':
                    $this->onza->workers($id, $_POST['month']);
                    break;
                case '901005608':
                    $this->tyc_contadores->workers($id, $_POST['month']);
                    break;
                case '900803958':
                    $this->altara->init_altara($id, $_POST['month']);
                    break;
                case '830056362':
                    $this->andina_rodillos->init_andina_rodillos($id, $_POST['month']);
                    break;
                case '800198348':
                    $this->ventas_institucionales->init_ventas_institucionales($id, $_POST['month']);
                    break;
                case '901112882':
                    $this->biotech->workers($id, $_POST['month']);
                    break;
                case '900127454':
                    $this->tampa->workers($id, $_POST['month']);
                    break;
                case '901084243':
                    $this->market->init_market($id, $_POST['month']);
                    break;
                case '901525415':
                    $this->gelt->workers($id, $_POST['month']);
                    break;
                case '900744829':
                    $this->sinergia->workers($id, $_POST['month']);
                    break;
                case '900804617':
                    $this->giron->init_giron($id, $_POST['month']);
                    break;
                case '901347237':
                    $this->yaraguies->init_yaraguies($id, $_POST['month']);
                    break;
            }
            return redirect()->to(base_url() . '/import/payroll')->with('success', 'Todos lo datos fueron validados con exíto.');
        }
    }

    public function delete_payroll($id): \CodeIgniter\HTTP\RedirectResponse
    {
        $data = $this->model_cargue->where(['nit' => $id, 'month_payroll' => $_POST['month'], 'status' => 'inactive'])->get()->getResult();
        if (count($data) > 0) {
            foreach ($data as $info) {
                $this->model_cargue->delete($info->id);
            }
            return redirect()->to(base_url() . '/import/payroll')->with('success', 'Todos lo datos fueron eliminados con exíto.');

        } else {
            return redirect()->to(base_url() . '/import/payroll')->with('vacio', 'No se encontraron datos para eliminar.');;
        }

    }

    private function load_file_asnomina($name, $encabezado, $month, $period, $load_number, $dates, $id_period, $year, $document): bool
    {
        $cargue = true;
        try {
            $extension = 'Xlsx';
            $readData = $encabezado + 1;
            if (!empty($name->getName())) {
                $pathinfo = pathinfo($name->getName());
                if (($pathinfo['extension'] == 'xls')
                    && $name->getSize() > 0) {
                    $extension = 'Xls';
                } elseif (($pathinfo['extension'] == 'csv')
                    && $name->getSize() > 0) {
                    $extension = 'Csv';
                }
                $inputFileName = $name->getTempName();
                // especifica la extención
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($extension);
                $spreedsheet = $reader->load($inputFileName);
                $totalhojas = $spreedsheet->getSheetCount();
                for ($i = 0; $i < $totalhojas; $i++) {
                    $sheet = $spreedsheet->getSheet($i);
                    $nombres = [];
                    $campos_vacios = [];
                    if ($i == 0) {
                        foreach ($sheet->getRowIterator($encabezado, $encabezado) as $rows) {
                            $cellIterator = $rows->getCellIterator();
                            $campo = 0;
                            foreach ($cellIterator as $row) {
                                // do stuff with the row
                                $cells = $row->getCalculatedValue();
                                if ($cells != '' && $cells != '#') {
                                    $name = str_replace([' ', '.', ',', '/'], ['_', '', '', '_'], trim($cells));
                                    array_push($nombres, $this->function_payroll->eliminar_tildes($name));
                                } else {
                                    array_push($campos_vacios, $campo);
                                }
                                $campo++;
                            }
                        }
                        $colum = $readData;
                        foreach ($sheet->getRowIterator($readData) as $rows) {
                            $cellIterator = $rows->getCellIterator();
                            $dato = 0;
                            foreach ($cellIterator as $row) {
                                $cells = $row->getFormattedValue();

                                if (in_array($dato, $campos_vacios, true)) {
                                    $dato++;
                                    // continue;
                                }
                                $data[$nombres[$dato]] = ltrim($cells);

                                $dato++;
                            }
                            $cellValue = $spreedsheet->getActiveSheet()->getCellByColumnAndRow(1, $colum)->getValue();
                            if ($cellValue != '' && $cellValue != null) {
                                $insert = [
                                    'nit' => company()->identification_number,
                                    'payroll_period' => $period,
                                    'month_payroll' => $month,
                                    'load_number' => $load_number,
                                    'data' => json_encode($data),
                                    'payment_dates' => json_encode($dates),
                                    'period_id' => $id_period,
                                    'year' => $year,
                                    'type_document_payroll' => $document
                                ];
                                $this->model_cargue->insert($insert);
                            }
                            $colum++;
                        }
                    }

                }

            }
            return $cargue;
        } catch (\Exception $e) {
            return false;
        }

    }

}