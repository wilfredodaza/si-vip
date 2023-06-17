<?php


namespace App\Controllers\companies;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Accrued;
use App\Models\Bank;
use App\Models\Cargue;
use App\Models\Customer;
use App\Models\Deduction;
use App\Models\Invoice;
use App\Models\OtherConcepts;
use App\Models\Payroll;
use App\Models\CustomerWorker;
use App\Models\PayrollDate;
use App\Models\TypeAccountBank;
use App\Models\TypeWorker;

class Payroll_gelt extends BaseController
{
    public $cargue;
    public $workers;
    public $payroll;
    public $accrueds;
    public $customers;
    public $invoices;
    public $other_concepts;
    public $deduction;

    // workers
    public $type_worker;
    public $banks;
    public $bank_account_types;
    public $payrollDate;
    //functions
    public $functions_payroll;
    public $info;

    public function __construct()
    {
        $this->cargue = new Cargue();
        $this->workers = new CustomerWorker();
        $this->payroll = new Payroll();
        $this->accrueds = new Accrued();
        $this->deduction = new Deduction();
        $this->customers = new Customer();
        $this->invoices = new Invoice();
        $this->other_concepts = new OtherConcepts();
        //workers
        $this->type_worker = new TypeWorker();
        $this->banks = new Bank();
        $this->bank_account_types = new TypeAccountBank();
        $this->payrollDate = new PayrollDate();
        //funciones
        $this->functions_payroll = new Functions_Payroll();

    }

    public function workers($nit, $month_payroll)
    {
        $info = $this->functions_payroll->data($nit, $month_payroll);
        foreach ($info as $data) {
            $documento = 0;
            $json = json_decode($data->data);
            $Ti = utf8_encode($json->TIPO_DE_DOCUMENTO);
            if (strtolower($this->functions_payroll->eliminar_tildes(utf8_decode($Ti))) == "cedula de ciudadania") {
                $documento = 3;
            }elseif (strtolower($this->functions_payroll->eliminar_tildes(utf8_decode($Ti))) == "cedula de extranjeria") {
                $documento = 5;
            }
            $type_worker = 1;
            if( $json->CONTRATO == 'Aprendiz Productivo'){
                $type_worker = 19;
            }
            $municipio = 149;
            if(strtolower($json->CIUDAD_DONDE_VIVE) == 'barranquilla'){
                $municipio = 126;
            }
            if(!empty($json->BANCO)){
                $bank = $this->functions_payroll->banks($json->BANCO);
            }else{
                $bank = null;
            }

            $bank_account_type = (strtolower($json->TIPO_DE_CUENTA) == 'ahorros')?1:2;
            $subtype_worker = 1;
            $type_contract_id = 2;
            $nombres = explode( ' ', $json->NOMBRES);
            $worker = $this->functions_payroll->worker_data($json->IDENTIFICACION);
            if (is_null($worker)) {
                // json datos del trabajador para realizar el guardado
                $data_customer = [
                    'name' => $nombres[0],
                    'type_document_identifications_id' => $documento,
                    'identification_number' => $json->IDENTIFICACION,
                    'address' => $json->DIRECCION,
                    'email' => ($json->CORREO_ELECTRONICO ?? ''),
                    'type_customer_id' => 3,
                    'municipality_id' => $municipio,
                    'companies_id' => Auth::querys()->companies_id, //cambiar por empresa
                ];
                $data_customer_worker = [
                    'type_worker_id' => $type_worker,
                    'sub_type_worker_id' => $subtype_worker,
                    'bank_id' => ($bank != '') ? $bank : null,
                    'bank_account_type_id' => $bank_account_type,
                    'type_contract_id' => $type_contract_id,
                    'payment_method_id' => 47,
                    'account_number' => $json->CUENTA_BANCARIA,
                    'second_name' => (count($nombres) == 2)?$nombres[1]:(count($nombres) > 2)?$nombres[1].' '.($nombres[2] ?? ''):'',//FALTA
                    'surname' => $json->PRIMER_APELLIDO,
                    'second_surname' => $json->SEGUNDO_APELLIDO,
                    'high_risk_pension' => false,
                    'integral_salary' => (strtolower($json->CONTRATO) != 'Integral') ? 'false' : 'true',
                    'salary' => $json->SUELDO,
                    'admision_date' => $this->functions_payroll->split_date($json->FECHA_INGRESO),
                    'retirement_date' => (!empty($json->FECHA_RETIRO))?$this->functions_payroll->split_date($json->FECHA_RETIRO) : null,
                    'payroll_period_id' => $data->payroll_period,
                    'worker_code' => $json->CODIGO_INGRESO,
                    'work' => $json->CARGO
                ];
                $customer_id = $this->functions_payroll->add_customer($data_customer, $data_customer_worker);
                if (isset($json->CORREO_ELECTRONICO) && $json->CORREO_ELECTRONICO != '') {
                    $data_user = [
                        'name' => $nombres[0],
                        'username' => $json->CORREO_ELECTRONICO,
                        'email' => $json->CORREO_ELECTRONICO,
                        'password' => password_hash($json->IDENTIFICACION, PASSWORD_DEFAULT),
                        'status' => 'active',
                        'role_id' => 7,
                        'companies_id' => Auth::querys()->companies_id
                    ];
                    $this->functions_payroll->add_user($data_user, $customer_id);
                }
            } else {
                //valido si trabajador tiene correo
                if (isset($json->CORREO_ELECTRONICO) && $json->CORREO_ELECTRONICO != '') {
                    // valida si el trabajador tiene usuario en el sistema
                    if (empty($worker->user_id) || is_null($worker->user_id)) {
                        $data_user = [
                            'name' =>  $nombres[0],
                            'username' => $json->CORREO_ELECTRONICO,
                            'email' => $json->CORREO_ELECTRONICO,
                            'password' => password_hash($json->IDENTIFICACION, PASSWORD_DEFAULT),
                            'status' => 'active',
                            'role_id' => 7,
                            'companies_id' => Auth::querys()->companies_id
                        ];
                        // crea el usuario y actualiza el user id en la tabla customer
                        $this->functions_payroll->add_user($data_user, $worker->id);
                    }
                }
                // valido actualizaciones
                $data_update = [
                    'type_contract_id' => $type_contract_id,
                    'bank' => ($bank != '') ? $bank : null,
                    'bank_account_type' => $bank_account_type,
                    'payment_method_id' => 47,
                    'account_number' => $json->CUENTA_BANCARIA,
                    'salary' => $json->SUELDO,
                    'retirement_date' => (!empty($json->FECHA_RETIRO))?$this->functions_payroll->split_date($json->FECHA_RETIRO) : null,
                ];
                $this->functions_payroll->update_customer($worker, $data_update);
            }
        }
        $this->invoice($info);
        $this->functions_payroll->update_status_upload($nit,$month_payroll);
    }

    public function invoice($info)
    {
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_customer = $this->functions_payroll->customer_id($json->IDENTIFICACION);
            if($data->type_document_payroll ==  10){
                try {
                    $ajuste = $this->invoices
                        ->join('customers','customers.id = invoices.customers_id')
                        ->join('payrolls','invoices.id = payrolls.invoice_id')
                        ->where(['invoices.companies_id' => company()->id,
                            'invoices.invoice_status_id' => 14,
                            'invoices.type_documents_id' => 9,
                            'customers.identification_number' => $json->IDENTIFICACION,
                            'payrolls.period_id' => $data->period_id])
                        ->get()->getResult()[0];
                } catch (\Exception $e){
                    //return redirect()->to(base_url() . '/import/payroll')->with('errors', $e);
                    //die();
                }
            }
            $id_invoice = $this->invoices->insert([
                'resolution_id' => null,
                'prefix' => null,
                'customers_id' => $id_customer,
                'invoice_status_id' => ($data->type_document_payroll == 10)?13:17,
                'notes' => ($json->notas ?? ''),//falta
                'type_documents_id' => ($data->type_document_payroll == 10)?$data->type_document_payroll:109,
                'companies_id' => Auth::querys()->companies_id,
                'uuid' => ($ajuste->uuid ?? null),
                'resolution_credit' => ($ajuste->resolution ?? null),
                'issue_date' => ($ajuste->created_at ?? null)
            ]);
            $diasLaborados = 0;
            foreach($json->conceptosPagos as $conceptosPago){
                if($conceptosPago->CODIGOCONCEPTO_2 == 1 || $conceptosPago->CODIGOCONCEPTO_2 == 29 || $conceptosPago->CODIGOCONCEPTO_2 == 12 ){
                    $diasLaborados += $conceptosPago->CANTIDAD_2;
                }
            }
            $this->payroll->insert([
                'settlement_start_date' => $this->functions_payroll->split_date($json->FechaLiquidacionInicial),
                'settlement_end_date' => $this->functions_payroll->split_date($json->FechaLiquidacionFinal),
                'worked_time' => $diasLaborados,
                'invoice_id' => $id_invoice,
                'period_id' => $data->period_id,
                'sub_period_id' => $data->load_number,
                'type_payroll_adjust_note_id' => ($data->type_document_payroll == 10) ? 1 : null
            ]);

            $json_dates = json_decode($data->payment_dates);
            foreach ($json_dates as $json_date) {
                $this->payrollDate->insert([
                    'invoice_id' => $id_invoice,
                    'payroll_date' => $json_date
                ]);
            }
        }
        $this->saveDataComplete($info);
    }

    public function saveDataComplete($info)
    {
        $concept_hours = [3, 4, 5, 6, 7, 8, 9];
        $concept_licencias = [18, 19, 20];
        $deducciones = [];
        $devengados = [];
        $devengados_sueltos = [];
        $deducciones_sueltos = [];
        $cedulas_cesantias = [];
        $fss = [];

        $concepts = $this->other_concepts->where(['companies_id' => company()->id, 'status' => 'Active'])->get()->getResult();
        foreach ($info as $data) {
            $json = json_decode($data->data);
            foreach ($concepts as $concept) {
                foreach ($json->conceptosPagos as $conceptosPago) {
                    $valorFsp = 0;
                    $valorFssp = 0;
                    $descuento = 0;
                    $documentPayroll = ($data->type_document_payroll == 10)?$data->type_document_payroll:109;
                    $id_payroll = $this->functions_payroll->data_payroll($json->IDENTIFICACION, $data->period_id, $documentPayroll); // opcional de mejora
                    if(empty($id_payroll) || $id_payroll == null && $data->type_document_payroll == 10){
                        continue;
                    }
                    $data_save = [
                        'type_accrued_id' => 0,
                        'payment' => null,
                        'start_time' => null,
                        'end_time' => null,
                        'quantity' => null,
                        'percentage' => null,
                        'description' => '',
                        'payroll_id' => $id_payroll,
                        'type_disability_id' => null,
                        'type_overtime_surcharge_id' => null,
                        'other_payments' => null
                    ];
                    $data_deductions = [
                        'percentage' => null,
                        'payment' => null,
                        'type_deduction_id' => null,
                        'payroll_id' => $id_payroll,
                        'type_law_deduction_id' => null,
                        'description' => null
                    ];
                    // $other_payments = null;
                    $type_disability_id = null;
                    $type_overtime_surcharge_id = null;
                    // $quantity = 0;
                    // $payment = 0;
                    if ($concept->id_concept_helisa == $conceptosPago->CODIGOCONCEPTO_2) {
                        if ($concept->type_concept == 'Devengado') {
                            $data_save['type_accrued_id'] = $concept->concept_dian;
                            $data_save['description'] = $concept->name;
                            $data_save['payment'] = (isset($devengados[$json->IDENTIFICACION][$concept->concept_dian]['payment'])) ? $devengados[$json->IDENTIFICACION][$concept->concept_dian]['payment'] + abs($conceptosPago->ValorPago_2) : abs($conceptosPago->ValorPago_2);
                            if ($concept->concept_dian == 1) {
                                // sueldo
                                $devengados[$json->IDENTIFICACION][1] = $data_save;
                            } elseif ($concept->concept_dian == 2) {
                                //auxilio de transporte
                                $devengados[$json->IDENTIFICACION][2] = $data_save;
                            } elseif (in_array($concept->concept_dian, $concept_hours)) {
                                //horas extras
                                $type_overtime_surcharge_id = $this->functions_payroll->getType_overtime_surcharge_id($concept);
                                $data_save['payment'] = abs($conceptosPago->ValorPago_2);
                                $data_save['quantity'] = (isset($devengados[$json->IDENTIFICACION]['quantity'])) ? $devengados[$json->IDENTIFICACION]['quantity'] + $conceptosPago->CANTIDAD_2 : $conceptosPago->CANTIDAD_2;
                                $data_save['type_overtime_surcharge_id'] = $type_overtime_surcharge_id;
                                array_push($devengados_sueltos, $data_save);
                            } elseif ($concept->concept_dian == 10) {
                                //viaticos salariales
                                $devengados[$json->IDENTIFICACION][10] = $data_save;
                            } elseif ($concept->concept_dian == 11) {
                                //viaticos salariales
                                $devengados[$json->IDENTIFICACION][11] = $data_save;
                            } elseif ($concept->concept_dian == 12 || $concept->concept_dian == 13) {
                                //Vacaciones comunes y pagadas;
                                $data_save['quantity'] = (isset($devengados[$json->IDENTIFICACION]['quantity'])) ? $devengados[$json->IDENTIFICACION]['quantity'] + $conceptosPago->CANTIDAD_2 : $conceptosPago->CANTIDAD_2;
                                $devengados[$json->IDENTIFICACION][$concept->concept_dian] = $data_save;
                            } elseif ($concept->concept_dian == 14) {
                                //prima
                                $data_save['quantity'] = (isset($devengados[$json->IDENTIFICACION]['quantity'])) ? $devengados[$json->IDENTIFICACION]['quantity'] + $conceptosPago->CANTIDAD_2 : $conceptosPago->CANTIDAD_2;
                                $devengados[$json->IDENTIFICACION][14] = $data_save;
                            } elseif ($concept->concept_dian == 16) {
                                //cesantias
                                if($conceptosPago->CONCEPTO_2 == 'INTERESES DE CESANTIAS'){
                                    if (!in_array($json->IDENTIFICACION, $cedulas_cesantias)) {
                                        $data_save['payment'] =  $this->functions_payroll->cumulative_data_tyc($info, $json->IDENTIFICACION, 'CESANTIAS');
                                        $data_save['description'] = 'CESANTIAS';
                                        $data_save['other_payments'] = $this->functions_payroll->cumulative_data_tyc($info, $json->IDENTIFICACION, 'INTERESES DE CESANTIAS');
                                        $data_save['percentage'] = 12;
                                        $devengados[$json->IDENTIFICACION][16] = $data_save;
                                        array_push($cedulas_cesantias, $json->IDENTIFICACION);
                                    }
                                }
                            } elseif ($concept->concept_dian == 17) {
                                //incapacidades
                                switch ($concept->type_other) {
                                    case 'Comun':
                                        $type_disability_id = 1;
                                        break;
                                    case 'Profesional':
                                        $type_disability_id = 2;
                                        break;
                                    case 'Laboral':
                                        $type_disability_id = 3;
                                        break;
                                }
                                $data_save['quantity'] = (isset($devengados[$json->IDENTIFICACION]['quantity'])) ? $devengados[$json->IDENTIFICACION]['quantity'] + $conceptosPago->CANTIDAD_2 : $conceptosPago->CANTIDAD_2;
                                $data_save['type_disability_id'] = $type_disability_id;
                                $data_save['payment'] = abs($conceptosPago->ValorPago_2);
                                array_push($devengados_sueltos, $data_save);

                            } elseif (in_array($concept->concept_dian, $concept_licencias)) {
                                //licencias
                                $data_save['quantity'] = (isset($devengados[$json->IDENTIFICACION]['quantity'])) ? $devengados[$json->IDENTIFICACION]['quantity'] + $conceptosPago->CANTIDAD_2 : $conceptosPago->CANTIDAD_2;
                                $devengados[$json->IDENTIFICACION][$concept->concept_dian] = $data_save;
                            } elseif ($concept->concept_dian == 21) {
                                // bonificacion salarial
                                $devengados[$json->IDENTIFICACION][21] = $data_save;
                            } elseif ($concept->concept_dian == 22) {
                                // bonificacion no salarial
                                $devengados[$json->IDENTIFICACION][22] = $data_save;
                            } elseif ($concept->concept_dian == 23) {
                                //auxilio salarial
                                $data_save['payment'] = abs($conceptosPago->ValorPago_2);
                                array_push($devengados_sueltos, $data_save);
                            } elseif ($concept->concept_dian == 24) {
                                // auxilio no salarial
                                $data_save['payment'] = abs($conceptosPago->ValorPago_2);
                                array_push($devengados_sueltos, $data_save);
                            } elseif ($concept->concept_dian == 25) {
                                //huelgas legales
                                $data_save['quantity'] = (isset($devengados[$json->iDENTIFICACION]['quantity'])) ? $devengados[$json->iDENTIFICACION]['quantity'] + $conceptosPago->CANTIDAD_2 : $conceptosPago->CANTIDAD_2;
                                $devengados[$json->IDENTIFICACION][25] = $data_save;
                            } elseif ($concept->concept_dian == 26 || $concept->concept_dian == 27) {
                                //otros conceptos salariales o no salariales
                                $data_save['payment'] = abs($conceptosPago->ValorPago_2);
                                array_push($devengados_sueltos, $data_save);
                            } elseif ($concept->concept_dian == 28) {
                                //compensacion Ordinaria
                                $devengados[$json->IDENTIFICACION][28] = $data_save;
                            } elseif ($concept->concept_dian == 29) {
                                // compensacion extraordinario
                                $devengados[$json->IDENTIFICACION][29] = $data_save;
                            } elseif ($concept->concept_dian == 30) {
                                $devengados[$json->IDENTIFICACION][30] = $data_save;
                            } elseif ($concept->concept_dian == 31) {
                                //BONO no salarial
                                $devengados[$json->IDENTIFICACION][31] = $data_save;
                            } elseif ($concept->concept_dian == 32) {
                                //Bono de alimentacion salarial
                                $devengados[$json->IDENTIFICACION][32] = $data_save;
                            } elseif ($concept->concept_dian == 33) {
                                //Bono de alimentacion no salarial
                                $devengados[$json->IDENTIFICACION][33] = $data_save;
                            } elseif ($concept->concept_dian == 34) {
                                //comisiones
                                $devengados[$json->IDENTIFICACION][34] = $data_save;
                            } elseif ($concept->concept_dian == 35) {
                                //pago a terceros
                                $devengados[$json->IDENTIFICACION][35] = $data_save;
                            } elseif ($concept->concept_dian == 36) {
                                //anticipo
                                $devengados[$json->IDENTIFICACION][36] = $data_save;
                            } elseif ($concept->concept_dian == 37) {
                                //dotacion
                                $devengados[$json->IDENTIFICACION][37] = $data_save;
                            } elseif ($concept->concept_dian == 38) {
                                //apoyo_sotenimiento
                                $devengados[$json->IDENTIFICACION][38] = $data_save;
                            } elseif ($concept->concept_dian == 39) {
                                //teletrabajo
                                $devengados[$json->IDENTIFICACION][39] = $data_save;
                            } elseif ($concept->concept_dian == 40) {
                                //bonificacion de retiro
                                $devengados[$json->IDENTIFICACION][40] = $data_save;
                            } elseif ($concept->concept_dian == 41) {
                                //indemnizacion
                                $devengados[$json->IDENTIFICACION][41] = $data_save;
                            } elseif ($concept->concept_dian == 42) {
                                //reintegros
                                $devengados[$json->IDENTIFICACION][42] = $data_save;
                            }
                        } elseif ($concept->type_concept == 'Deduccion') {
                            $data_deductions['type_deduction_id'] = $concept->concept_dian;
                            $data_deductions['description'] = $concept->name;
                            $data_deductions['payment'] = (isset($deducciones[$json->IDENTIFICACION][$concept->concept_dian]['payment'])) ? $deducciones[$json->IDENTIFICACION][$concept->concept_dian]['payment'] + abs($conceptosPago->ValorPago_2): abs($conceptosPago->ValorPago_2);
                            if ($concept->concept_dian == 1) {
                                //eps
                                if($conceptosPago->CODIGOCONCEPTO_2 == 6){
                                    $data_deductions['percentage'] = 4;
                                    $data_deductions['type_law_deduction_id'] = 3;
                                }
                                $deducciones[$json->IDENTIFICACION][1] = $data_deductions;
                            } elseif ($concept->concept_dian == 2) {
                                //PENSION
                                if($conceptosPago->CODIGOCONCEPTO_2 == 7){
                                    $data_deductions['percentage'] = 4;
                                    $data_deductions['type_law_deduction_id'] = 5;
                                }
                                $deducciones[$json->IDENTIFICACION][2] = $data_deductions;
                            } elseif ($concept->concept_dian == 3) {
                                if (!in_array($json->IDENTIFICACION, $fss)) {
                                    foreach($json->otrosPagos as $otrosPago){
                                        $valorFsp += abs($otrosPago->SUBCUENTA_3);
                                        $valorFssp += abs($otrosPago->SUBSISTENCIA_3);
                                    }
                                    $valorTotal = $valorFsp + $valorFssp;
                                    if(abs($this->functions_payroll->cumulative_data_tyc($info, $json->IDENTIFICACION, 'FONDO DE SOLIDARIDAD')) < $valorTotal){
                                        $p = abs($valorTotal) - abs($this->functions_payroll->cumulative_data_tyc($info, $json->IDENTIFICACION, 'FONDO DE SOLIDARIDAD'));
                                        $descuento = $p/2;
                                    }
                                    $porcentajeFssp = (0.5 * $valorFssp)/$valorFsp;
                                    //fondo de solidaridad pensional y subfondo de subsistencia pensional
                                    $data_deductions['percentage'] = 0.5;
                                    $data_deductions['type_law_deduction_id'] = 9;
                                    $data_deductions['payment'] = abs($valorFsp) - abs($descuento);
                                    $deducciones[$json->IDENTIFICACION][3] = $data_deductions;
                                    $data_fs = [
                                        'percentage' => $porcentajeFssp,
                                        'payment' => abs($valorFssp) - abs($descuento),
                                        'type_deduction_id' => 4,
                                        'payroll_id' => $id_payroll,
                                        'type_law_deduction_id' => 9,
                                        'description' => 'Valor Fondo Subsistencia'
                                    ];
                                    $this->deduction->insert($data_fs);
                                    array_push($fss, $json->IDENTIFICACION);
                                }
                            } elseif ($concept->concept_dian == 5) {
                                //sindicato
                                $deducciones[$json->IDENTIFICACION][5] = $data_deductions;
                            } elseif ($concept->concept_dian == 6) {
                                //sancion publica
                                $deducciones[$json->IDENTIFICACION][6] = $data_deductions;
                            } elseif ($concept->concept_dian == 7) {
                                //sancion privada
                                $deducciones[$json->IDENTIFICACION][7] = $data_deductions;
                            } elseif ($concept->concept_dian == 8) {
                                //libranza
                                $deducciones[$json->IDENTIFICACION][8] = $data_deductions;
                            } elseif ($concept->concept_dian == 9) {
                                //pago a terceros
                                $data_deductions['payment'] = abs($conceptosPago->ValorPago_2);
                                array_push($deducciones_sueltos, $data_deductions);
                            } elseif ($concept->concept_dian == 10) {
                                //anticipos
                                $deducciones[$json->IDENTIFICACION][10] = $data_deductions;
                            } elseif ($concept->concept_dian == 11) {
                                //otras deducciones
                                $data_deductions['payment'] = abs($conceptosPago->ValorPago_2);
                                array_push($deducciones_sueltos, $data_deductions);
                            } elseif ($concept->concept_dian == 12) {
                                //pension voluntaria
                                $deducciones[$json->IDENTIFICACION][12] = $data_deductions;
                            } elseif ($concept->concept_dian == 13) {
                                //retencion de fuente
                                $deducciones[$json->IDENTIFICACION][13] = $data_deductions;
                            } elseif ($concept->concept_dian == 14) {
                                //afc
                                $deducciones[$json->IDENTIFICACION][14] = $data_deductions;
                            } elseif ($concept->concept_dian == 15) {
                                //cooperativa
                                $deducciones[$json->IDENTIFICACION][15] = $data_deductions;
                            } elseif ($concept->concept_dian == 16) {
                                //embargos
                                $deducciones[$json->IDENTIFICACION][16] = $data_deductions;
                            } elseif ($concept->concept_dian == 17) {
                                //plan complemetario
                                $deducciones[$json->IDENTIFICACION][17] = $data_deductions;
                            } elseif ($concept->concept_dian == 18) {
                                //educacion
                                $deducciones[$json->IDENTIFICACION][18] = $data_deductions;
                            } elseif ($concept->concept_dian == 19) {
                                //reintegro
                                $deducciones[$json->IDENTIFICACION][19] = $data_deductions;
                            } elseif ($concept->concept_dian == 20) {
                                //deuda
                                $deducciones[$json->IDENTIFICACION][20] = $data_deductions;
                            }
                        }
                    }
                }
            }
        }


        $total_devengados = array_merge(array_reduce($devengados, 'array_merge', array()), $devengados_sueltos);
        $total_deducciones = array_merge(array_reduce($deducciones, 'array_merge', array()), $deducciones_sueltos);

        //echo json_encode($total_devengados);
        //echo json_encode($total_deducciones);die();

        try {
            $this->accrueds->insertBatch($total_devengados);
            $this->deduction->insertBatch($total_deducciones);
        }catch (\Exception $e){
            //return redirect()->to(base_url() . '/import/payroll')->with('errors', $e->getMessage());
            //die();
        }
    }

}




