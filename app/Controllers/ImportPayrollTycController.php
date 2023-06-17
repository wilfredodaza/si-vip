<?php


namespace App\Controllers;


use App\Controllers\companies\Functions_Payroll;
use App\Models\Cargue;
use App\Models\PayrollPeriod;
use App\Models\Period;
use App\Models\SubPeriod;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;
use CodeIgniter\Model;
use GroceryCrud\Core\Exceptions\Exception;
use PhpOffice\PhpSpreadsheet\Spreadsheet;


class ImportPayrollTycController extends BaseController
{
    protected $validations;
    protected $tyc;
    protected $payroll_periods;
    protected $model_cargue;
    protected $model_period;
    protected $subperiodo;
    protected $function_payroll;
    protected $companies_tyc;

    public function __construct()
    {
        //modelos
        $this->payroll_periods = new PayrollPeriod();
        $this->model_cargue = new Cargue();
        $this->model_period = new Period();
        $this->subperiodo = new SubPeriod();
        $this->function_payroll = new Functions_Payroll();
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
            901525415 // gelt

        ];
    }

    public function Load()
    {
        $mes = null;
        $p_subperiodo = '';
        $number = null;
        $dates = [];
        $load = null;

        $period = $_POST['period'];
        $response = 'Documentos cargados con exíto.';
        if (in_array(company()->identification_number, $this->companies_tyc)) {
            $encabezado = $_POST['titulo'];
            $encabezado2 = $_POST['titulo2'];
            $encabezado3 = $_POST['titulo3'];
            if ($period == 1) {
                $number = 1;
                $p_subperiodo = 'Semanal';
            } elseif ($period == 3) {
                $number = 1;
                $p_subperiodo = 'Decenal';
            } elseif ($period == 2) {
                $number = 1;
                $p_subperiodo = 'Catorcenal';
            } elseif ($period == 4) {
                $number = 1;
                $p_subperiodo = 'Quincenal';
            } else {
                $number = 1;
                $p_subperiodo = 'Mensual';
                $response = 'Documento cargado con exíto.';
            }
        }
        $id_period = $this->function_payroll->model_cargue(company()->identification_number, $_POST['month'], $_POST['year'], $_POST['document']);
        if(is_null($id_period)){
            return redirect()->to(base_url() . '/import/payroll')->with('errors', 'El mes a liquidar ya se encuentra cargado.');
        }
        foreach($_POST['fp1'] as $date){
            if($date != ''){
                array_push($dates,$date);
            }
        }
        for ($i = 1; $i <= $number; $i++) {
            if (in_array(company()->identification_number, $this->companies_tyc)) {
                if($_POST['document'] == 9 || $_POST['document'] == 110){
                    $id_sub_periodo = $this->subperiodo->insert([
                        'name' => $this->function_payroll->getType_month($_POST['month']) . '_' . $_POST['year'] . '_' . $p_subperiodo . '_' . $_POST['fi' . $i] . '_hasta_' . $_POST['ff' . $i],
                        'company_id' => company()->id
                    ]);
                }else{
                    $id_sub_periodo = $this->subperiodo->insert([
                        'name' => $this->function_payroll->getType_month($_POST['month']) . '_' . $_POST['year'] .'_ ajuste',
                        'company_id' => company()->id
                    ]);
                }
                if(company()->identification_number != 901112882){
                    $load = $this->load_file_tyc('file' . $i, $encabezado, $_POST['month'], $period, $id_sub_periodo, $dates, $id_period,
                        ($encabezado2 ?? 0), ($encabezado3 ?? 0),$_POST['year'], $_POST['document'],$_POST['fi' . $i],$_POST['ff' . $i]);
                }else{
                    $load = $this->load_file('file' . $i, $encabezado, $_POST['month'], $period, $id_sub_periodo, $dates, $id_period, ($encabezado2 ?? 0), ($encabezado3 ?? 0),$_POST['year'], $_POST['document']);
                }

            }
            if ($load == 'error') {
                $error = 'si';
                break;
            } elseif ($load == 'error repetido') {
                $error = 'repetido';
                break;
            }
        }
        if (isset($error) && $error == 'si') {
            return redirect()->to(base_url() . '/import/payroll')->with('errors', 'La extension del archivo no esta permitida, (Extenciones permitidas "XLSX").');
        } elseif (isset($error) && $error == 'repetido') {
            return redirect()->to(base_url() . '/import/payroll')->with('errors', 'La información no se pudo subir correctamente ya que se encuentran empleados repetidos');
        } else {
            return redirect()->to(base_url() . '/import/payroll')->with('success', $response);
        }

    }

    private function load_file_tyc($name, $encabezado, $month, $period, $load_number, $dates, $id_period, $encabezado2, $encabezado3, $year, $document, $fi, $ff)
    {
        $prueba = [];
        $conceptos = [];
        $otros = [];

        if (!empty($_FILES[$name]['name'])) {
            $pathinfo = pathinfo($_FILES[$name]['name']);
            if (($pathinfo['extension'] == 'xlsx')
                && $_FILES[$name]['size'] > 0) {
                $inputFileName = $_FILES[$name]['tmp_name'];
                // prueba
                $reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                $reader->setShouldFormatDates(true);
                $reader->open($inputFileName);
                $hojas = 1;
                $nombres = [];
                $campos_vacios = [];
                foreach ($reader->getSheetIterator() as $sheet) {
                    if ($hojas < 2) {
                        $count = 1;
                        foreach ($sheet->getRowIterator() as $row) {
                            // do stuff with the row
                            $cells = $row->getCells();
                            if ($count == $encabezado) {
                                $campo = 0;
                                foreach ($cells as $titulo) {
                                    if (trim($titulo) != '' && trim($titulo) != '#') {
                                        $titles = str_replace([' ', '.', ',', '(', ')', '/', '-', '%'], ['_', '', '', '', '', '_', '', 'P'], preg_replace("[\n|\r|\n\r]", "", trim($titulo)));
                                        array_push($nombres, $this->function_payroll->eliminar_tildes(trim($titles)));
                                    } else {
                                        array_push($campos_vacios, $campo);
                                    }
                                    $campo++;
                                }
                            }

                            if ($count > $encabezado) {
                                $dato = 0;
                                foreach ($nombres as $titulo) {
                                    if (in_array($dato, $campos_vacios, true)) {
                                        $dato++;
                                        // continue;
                                    }
                                    $data[$titulo] = trim($cells[$dato]);
                                    $dato++;
                                }
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
                                $insert['data'] = $data;
                                $prueba[trim($cells[44])] = $insert;
                                $prueba[trim($cells[44])]['data']['FechaLiquidacionInicial'] = $fi;
                                $prueba[trim($cells[44])]['data']['FechaLiquidacionFinal'] = $ff;
                                $conceptos[trim($cells[44])] = [];
                                $otros[trim($cells[44])] = [];

                            }
                            $count++;
                        }
                        //echo json_encode($prueba);die();
                    } elseif ($hojas == 2) {
                        $count = 1;
                        $nombres = [];
                        $campos_vacios = [];
                        foreach ($sheet->getRowIterator() as $row) {
                            // do stuff with the row
                            $cells = $row->getCells();
                            if ($count == $encabezado2) {
                                $campo = 0;
                                foreach ($cells as $titulo) {
                                    if (trim($titulo) != '' && trim($titulo) != '#') {
                                        $name = str_replace(' ', '_', trim($titulo));
                                        array_push($nombres, $this->function_payroll->eliminar_tildes($name).'_2');
                                    } else {
                                        array_push($campos_vacios, $campo);
                                    }
                                    $campo++;
                                }
                            }
                            if ($count > $encabezado2) {
                                $dato = 0;
                                foreach ($nombres as $titulo) {
                                    if (in_array($dato, $campos_vacios, true)) {
                                        $dato++;
                                        // continue;
                                    }
                                    $total[$titulo] = trim($cells[$dato]);
                                    //$prueba[trim($cells[2])]['data']['conceptos'][$titulo] = trim($cells[$dato]);
                                    $dato++;
                                }
                                //$prueba[trim($cells[2])]['data'] = json_encode($prueba[trim($cells[2])]['data']);
                                //$this->model_cargue->insert($prueba[trim($cells[1])]);
                                array_push($conceptos[trim($cells[2])], $total);
                            }
                            $count++;
                        }
                        // echo json_encode($conceptos);die();
                    } elseif ($hojas == 3) {
                        $count = 1;
                        $nombres = [];
                        $campos_vacios = [];
                        foreach ($sheet->getRowIterator() as $row) {
                            // do stuff with the row
                            $cells = $row->getCells();
                            if ($count == $encabezado3) {
                                $campo = 0;
                                foreach ($cells as $titulo) {
                                    if (trim($titulo) != '' && trim($titulo) != '#') {
                                        $name = str_replace(' ', '_', trim($titulo));
                                        array_push($nombres, $this->function_payroll->eliminar_tildes($name) . '_3');
                                    } else {
                                        array_push($campos_vacios, $campo);
                                    }
                                    $campo++;
                                }
                            }
                            if ($count > $encabezado3) {
                                $dato = 0;
                                foreach ($nombres as $titulo) {
                                    if (in_array($dato, $campos_vacios, true)) {
                                        $dato++;
                                        // continue;
                                    }
                                    $total[$titulo] = trim($cells[$dato]);
                                    //$prueba[trim($cells[3])]['data'][$titulo] = trim($cells[$dato]);
                                    $dato++;
                                }
                                array_push($otros[trim($cells[1])], $total);
                                //$valor = json_encode($prueba[trim($cells[3])]['data']);
                                //$prueba[trim($cells[3])]['data'] = $valor;
                                //echo json_encode($prueba[trim($cells[3])]);die();
                            }
                            $count++;
                        }
                    }
                    $hojas++;
                }
                //echo json_encode(count($prueba));die();
                foreach ($prueba as $data){
                    $prueba[$data['data']['IDENTIFICACION']]['data']['conceptosPagos'] = $conceptos[$data['data']['IDENTIFICACION']];
                    $prueba[$data['data']['IDENTIFICACION']]['data']['otrosPagos'] = $otros[$data['data']['IDENTIFICACION']];
                    $prueba[$data['data']['IDENTIFICACION']]['data'] = json_encode($prueba[$data['data']['IDENTIFICACION']]['data']);
                    try{
                        $this->model_cargue->insert($prueba[$data['data']['IDENTIFICACION']]);
                    }catch (\Exception $e){
                        echo json_encode('Error al guardar informacion');
                    }
                }
            } else {
                return 'error';
            }
            $reader->close();
        }

    }

    private function load_file($name, $encabezado, $month, $period, $load_number, $dates, $id_period, $encabezado2 = 0, $encabezado3 = 0, $year, $document)
    {
        $prueba = [];

        if (!empty($_FILES[$name]['name'])) {
            $pathinfo = pathinfo($_FILES[$name]['name']);
            if (($pathinfo['extension'] == 'xlsx')
                && $_FILES[$name]['size'] > 0) {
                $inputFileName = $_FILES[$name]['tmp_name'];
                // prueba
                $reader = ReaderEntityFactory::createReaderFromFile('/path/to/file.xlsx');
                $reader->setShouldFormatDates(true);
                $reader->open($inputFileName);
                $hojas = 1;
                $nombres = [];
                $campos_vacios = [];
                foreach ($reader->getSheetIterator() as $sheet) {
                    if ($hojas < 2) {
                        $count = 1;
                        foreach ($sheet->getRowIterator() as $row) {
                            // do stuff with the row
                            $cells = $row->getCells();
                            if ($count == $encabezado) {
                                $campo = 0;
                                foreach ($cells as $titulo) {
                                    if (trim($titulo) != '' && trim($titulo) != '#') {
                                        $titles = str_replace([' ', '.', ',', '(', ')', '/', '-', '%'], ['_', '', '', '', '', '_', '', 'P'], preg_replace("[\n|\r|\n\r]", "", trim($titulo)));
                                        array_push($nombres, $this->function_payroll->eliminar_tildes(trim($titles)));
                                    } else {
                                        array_push($campos_vacios, $campo);
                                    }
                                    $campo++;
                                }
                            }

                            if ($count > $encabezado) {
                                $dato = 0;
                                foreach ($nombres as $titulo) {
                                    if (in_array($dato, $campos_vacios, true)) {
                                        $dato++;
                                        // continue;
                                    }
                                    $data[$titulo] = trim($cells[$dato]);
                                    $dato++;
                                }
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
                                $insert['data'] = $data;
                                $prueba[trim($cells[1])] = $insert;

                            }
                            $count++;
                        }
                    } elseif ($hojas == 2) {
                        $count = 1;
                        $nombres = [];
                        $campos_vacios = [];
                        foreach ($sheet->getRowIterator() as $row) {
                            // do stuff with the row
                            $cells = $row->getCells();
                            if ($count == $encabezado2) {
                                $campo = 0;
                                foreach ($cells as $titulo) {
                                    if (trim($titulo) != '' && trim($titulo) != '#') {
                                        $name = str_replace(' ', '_', trim($titulo));
                                        array_push($nombres, $this->function_payroll->eliminar_tildes($name) . '_2');
                                    } else {
                                        array_push($campos_vacios, $campo);
                                    }
                                    $campo++;
                                }
                            }
                            if ($count > $encabezado2) {
                                $dato = 0;
                                foreach ($nombres as $titulo) {
                                    if (in_array($dato, $campos_vacios, true)) {
                                        $dato++;
                                        // continue;
                                    }

                                    $prueba[trim($cells[1])]['data'][$titulo] = trim($cells[$dato]);


                                    $dato++;
                                }

                                $prueba[trim($cells[1])]['data'] = json_encode($prueba[trim($cells[1])]['data']);
                                $this->model_cargue->insert($prueba[trim($cells[1])]);

                            }
                            $count++;
                        }
                        //echo json_encode($prueba);die();
                    }
                    $hojas++;
                }
            } else {
                return 'error';
            }
            $reader->close();
        }

    }
}