<?php


namespace App\Controllers\companies;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Accrued;
use App\Models\Bank;
use App\Models\Cargue;
use App\Models\Customer;
use App\Models\CustomerWorker;
use App\Models\Deduction;
use App\Models\Invoice;
use App\Models\OtherBank;
use App\Models\OtherConcepts;
use App\Models\Payroll;
use App\Models\Period;
use App\Models\SubTypeWorker;
use App\Models\TypeWorker;
use App\Models\User;
use mysql_xdevapi\Exception;

class Functions_Payroll extends BaseController
{
    public $customer;
    public $customer_worker;
    public $user;
    public $cargue;
    public $invoices;
    public $accrueds;
    public $deductions;
    public $type_workers;
    public $subtype_workers;
    public $other_concepts;
    public $bank;
    public $other_bank;
    public $model_period;
    public $companies_tyc;
    private $payroll;


    public function __construct()
    {
        $this->customer = new Customer();
        $this->customer_worker = new CustomerWorker();
        $this->user = new User();
        $this->cargue = new Cargue();
        $this->invoices = new Invoice();
        $this->accrueds = new Accrued();
        $this->deductions = new Deduction();
        $this->type_workers = new TypeWorker();
        $this->subtype_workers = new SubTypeWorker();
        $this->other_concepts = new OtherConcepts();
        $this->bank = new Bank();
        $this->other_bank = new OtherBank();
        $this->model_period = new Period();
        $this->payroll = new Payroll();
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

    public function data($nit, $month_payroll): array
    {
        return $this->cargue->where(['nit' => $nit, 'month_payroll' => $month_payroll, 'status' => 'Inactive'])->get()->getResult();
    }

    public function model_cargue($id, $month, $year, $document)
    {
        $model_periods = [];
        $cargues = $this->cargue->where(['nit' => $id, 'year' => $year])->get()->getResult();
        foreach ($cargues as $cargue) {
            if (!in_array($cargue->month_payroll, $model_periods)) {
                array_push($model_periods, $cargue->month_payroll);
            }
        }
        if (in_array($_POST['month'], $model_periods) && !in_array(company()->identification_number, $this->companies_tyc)) {
            if($document == 10){
                $id = $this->model_period->where(['year' => $year, 'month' => $this->getType_month($month)])->first();
                return $id['id'];
            }else{
                return null;
            }
        } else {
            $id = $this->model_period->where(['year' => $year, 'month' => $this->getType_month($month)])->first();
            return $id['id'];
        }

    }

    public function worker_data($identification)
    {
        return $this->customer
            ->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->select(['*','customers.id as id_customers', 'customer_worker.id as id_customer_worker'])
            ->where(['customers.identification_number' => $identification,
                     'customers.type_customer_id' => 3,
                     'customers.companies_id' => company()->id])
            ->asObject()
            ->first();
    }

    public function eliminar_tildes($cadena)
    {
        //Reemplazamos la A y a
        $cadena = str_replace(
            array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
            array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
            $cadena
        );

        //Reemplazamos la E y e
        $cadena = str_replace(
            array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
            array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
            $cadena);

        //Reemplazamos la I y i
        $cadena = str_replace(
            array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
            array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
            $cadena);

        //Reemplazamos la O y o
        $cadena = str_replace(
            array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
            array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
            $cadena);

        //Reemplazamos la U y u
        $cadena = str_replace(
            array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
            array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
            $cadena);

        //Reemplazamos la N, n, C y c
        $cadena = str_replace(
            array('Ñ', 'ñ', 'Ç', 'ç'),
            array('N', 'n', 'C', 'c'),
            $cadena
        );

        return $cadena;
    }

    public function add_customer($data_customer, $data_customer_worker)
    {
        try {
            $id_customer = $this->customer->insert($data_customer);
            $data_customer_worker['customer_id'] = $id_customer;
            $this->customer_worker->save($data_customer_worker);
        } catch (\Exception $e){
        }
        return $id_customer;
    }

    public function add_user($data, $worker)
    {
        $id = $this->user->insert($data);
        $this->customer->set(['user_id' => $id])->where(['id' => $worker])->update();
    }

    public function update_customer($worker, $data)
    {
        if ($data['type_contract_id'] != $worker->type_contract_id) {
            $update['type_contract_id'] = $data['type_contract_id'];
        }
        if (isset($data['bank'])) {
            if ($data['bank'] != $worker->bank_id) {
                $update['bank_id'] = $data['bank'];
            }
        }
        if (isset($data['bank_account_type'])) {
            if ($data['bank_account_type'] != $worker->bank_account_type_id) {
                $update['bank_account_type_id'] = $data['bank_account_type'];
            }
        }
        if ($data['payment_method_id'] != $worker->payment_method_id) {
            $update['payment_method_id'] = $data['payment_method_id'];
        }
        if (isset($data['salary'])) {
            if ($data['salary'] != $worker->salary) {
                $update['salary'] = $data['salary'];
            }
        }
        if (isset($data['retirement_date'])) {
            if ($data['retirement_date'] != $worker->retirement_date) {
                $update['retirement_date'] = $data['retirement_date'];
            }
        }
        if (isset($data['account_number'])) {
            if ($data['account_number'] != $worker->account_number) {
                $update['account_number'] = $data['account_number'];
            }
        }
        if (isset($update)) {
            $this->customer_worker->set($update)->where(['id' => $worker->id_customer_worker])->update();
        }
    }

    public function customer_id($identification)
    {
        $customer = $this->customer
            ->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->select(['customers.id'])
            ->where(['customers.identification_number' => $identification,
                'customers.type_customer_id' => 3,
                'customers.companies_id' => company()->id])
            ->asObject()
            ->first();
        return $customer->id;
    }

    public function data_payroll($identification, $period_id, $document)
    {
        $customer = $this->customer
            ->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->select(['customers.id'])
            ->where(['customers.identification_number' => $identification,
                'customers.type_customer_id' => 3,
                'customers.companies_id' => company()->id])
            ->asObject()
            ->first();
        $invoice = $this->invoices->join('payrolls', 'invoices.id = payrolls.invoice_id')
            ->selectMax('payrolls.id')
            ->where([
                'invoices.customers_id' => $customer->id,
                'payrolls.period_id' => $period_id,
                'invoices.type_documents_id' => $document
            ])->asObject()
            ->first();
        return $invoice->id;
    }

    public function data_payroll_tyc($identification, $period_id, $id_sub_period, $document)
    {
        $customer = $this->customer->where(['identification_number' => $identification, 'type_customer_id' => 3])->get()->getResult();
        $invoice = $this->invoices->join('payrolls', 'invoices.id = payrolls.invoice_id')
            ->selectMax('payrolls.id')
            ->where([
                'invoices.customers_id' => $customer[0]->id,
                'payrolls.period_id' => $period_id,
                'sub_period_id' => $id_sub_period,
                'invoices.type_documents_id' => $document
            ])->get()->getResult();
        return (int)$invoice[0]->id;
    }

    public function save_data_accrueds($type_accrueds, $payment, $start_time = null, $end_time = null, $quantity = '',
                                       $percentage = '', $description, $id_payroll, $type_disability_id = null, $type_overtime_surcharge_id = null, $other_payment = null)
    {
        $this->accrueds->insert([
            'type_accrued_id' => $type_accrueds,
            'payment' => $payment,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'quantity' => $quantity,
            'percentage' => $percentage,
            'description' => $description,
            'payroll_id' => $id_payroll,
            'type_disability_id' => $type_disability_id,
            'type_overtime_surcharge_id' => $type_overtime_surcharge_id,
            'other_payments' => $other_payment
        ]);
    }

    public function save_data_deductions($type_deduction_id, $payment, $description, $id_payroll, $percentage = '', $type_law_deduction_id = null)
    {
        $this->deductions->insert([
            'type_deduction_id' => $type_deduction_id,
            'payment' => $payment,
            'description' => $description,
            'payroll_id' => $id_payroll,
            'percentage' => $percentage,
            'type_law_deduction_id' => $type_law_deduction_id

        ]);
    }

    public function banks($bank)
    {
        $validation = $this->other_bank->where(['companies_id' => company()->id, 'name' => $this->eliminar_tildes($bank), 'status !=' => 'Inactive'])->get()->getResultObject();
        return $validation[0]->bank_id;
    }

    public function validation_banks($bank): bool
    {
        $result = false;

        $validation = $this->other_bank->where(['companies_id' => company()->id, 'name' => $bank, 'status !=' => 'Inactive'])->countAllResults();
        if ($validation == 0) {
            $result = true;
        }
        return $result;
    }

    public function validation_banks_tyc($bank): bool
    {
        $result = false;
        $validation = $this->bank->where(['code' => $bank])->countAllResults();
        if ($validation == 0) {
            $result = true;
        }
        return $result;
    }

    public function split_date($fecha)
    {
        return date("Y-m-d", strtotime($fecha));
    }

    public function settlement_dates($info): array
    {
        $settlement_start_date = [];
        $settlement_end_date = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            array_push($settlement_start_date, $this->split_date($json->Fecha_inicio_liquidacion));
            array_push($settlement_end_date, $this->split_date($json->fecha_fin_liquidacion));
        }
        $response = [
            'settlement_start_date' => min($settlement_start_date),
            'settlement_end_date' => max($settlement_end_date)
        ];
        return $response;

    }

    public function worker_days($info, $identification): int
    {
        $days = 0;
        $periodo = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            //if (!in_array($data->load_number, $periodo)) {
            if ($json->Numero_de_identificacion == $identification) {
                $days += $json->dias_trabajados;
                array_push($periodo, $data->load_number);
            }
            //}
        }
        return $days;
    }

    public function cumulative_data($info, $identification, $search, $quantity = false): int
    {
        $result = 0;
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if ($this->eliminar_tildes($json->Conceptos_pagos) == $this->eliminar_tildes($search)) {
                if ($identification == $json->Numero_de_identificacion) {
                    if ($quantity) {
                        $result += $json->Cantidad;
                    } else {
                        $result += $json->Valor;
                    }
                }
            }
        }
        return $result;
    }
    public function cumulative_data_tyc($info, $identification, $search): int
    {
        $result = 0;
        foreach ($info as $data) {
            $json = json_decode($data->data);
            foreach($json->conceptosPagos as $conceptosPago){
                if ($this->eliminar_tildes($conceptosPago->CONCEPTO_2) == $this->eliminar_tildes($search)) {
                    if ($identification == $json->IDENTIFICACION) {
                            $result += abs($conceptosPago->ValorPago_2);
                    }
                }
            }
        }
        return $result;
    }

    public function cumulative_data_id($info, $identification, $id, $type, $quantity = false): int
    {
        $result = 0;
        $conceptos = $this->other_concepts->where(['companies_id' => company()->id, 'type_concept' => $type, 'status' => 'Active', 'concept_dian' => $id])->get()->getResult();
        foreach ($conceptos as $concepto) {
            foreach ($info as $data) {
                $json = json_decode($data->data);
                if ($this->eliminar_tildes($json->Conceptos_pagos) == $this->eliminar_tildes($concepto->name)) {
                    if ($identification == $json->Numero_de_identificacion) {
                        if ($quantity) {
                            $result += $json->Cantidad;
                        } else {
                            $result += $json->Valor;
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function cumulative_data_fsp_fs($info, $identification, $search): int
    {
        $payment = 0;
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if ($identification == $json->Numero_de_identificacion) {
                if ($search = 'fps') {
                    $payment += $json->Valor_FSP;
                } else {
                    $payment += $json->Valor_Fondo_Subsistencia;
                }
            }
        }
        return $payment;
    }

    public function id_type_and_subtype_worker($code, $sub = false)
    {
        switch ($code) {
            case 0:
                $code = 00;
                break;
            case 1:
                $code = 01;
                break;
            case 2:
                $code = 02;
                break;
            case 3:
                $code = 03;
                break;
            case 4:
                $code = 04;
                break;
        }
        if ($sub == false) {
            $id = $this->type_workers->where(['code' => $code])->get()->getResult()[0];
        } else {
            $id = $this->subtype_workers->where(['code' => $code])->get()->getResult()[0];
        }
        return $id->id;
    }

    public function update_status_upload($nit, $month_payroll)
    {

        $this->cargue->set(['status' => 'Active'])->where(['nit' => $nit, 'month_payroll' => $month_payroll])->update();

    }

    public function Validation_fsp_fssp($identificacion, $info): bool
    {
        $validation = false;
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if ($json->Numero_de_identificacion == $identificacion && $json->Conceptos_pagos == 'FONDO DE SOLIDARIDAD Y SUBSIST') {
                $validation = true;
            }
        }
        return $validation;
    }

    public function validation_concepts($concept, $inactive = true): bool
    {
        $result = false;
        if ($inactive) {
            $validation = $this->other_concepts->where(['companies_id' => company()->id, 'name' => $concept, 'status' => 'Inactive'])->countAllResults();
            if ($validation > 0) {
                $result = true;
            }
        } else {
            $validation = $this->other_concepts->where(['companies_id' => company()->id, 'name' => $concept])->countAllResults();
            if ($validation == 0) {
                $result = true;
            }
        }
        return $result;
    }

    public function save_data_complete($info)
    {
        $concept_hours = [3, 4, 5, 6, 7, 8, 9];
        $concept_licencias = [18, 19, 20];
        $deducciones = [];
        $devengados = [];
        $devengados_sueltos = [];
        $deducciones_sueltos = [];
        $cedulas_cesantias = [];


        $concepts = $this->other_concepts->where(['companies_id' => company()->id, 'status' => 'Active'])->get()->getResult();
        foreach ($concepts as $concept) {
            foreach ($info as $data) {
                $json = json_decode($data->data);
                $id_payroll = $this->data_payroll($json->Numero_de_identificacion, $data->period_id, $data->type_document_payroll); // opcional de mejora
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
                if ($this->eliminar_tildes($concept->name) == $this->eliminar_tildes($json->Conceptos_pagos)) {
                    if ($concept->type_concept == 'Devengado') {
                        $data_save['type_accrued_id'] = $concept->concept_dian;
                        $data_save['description'] = $concept->name;
                        $data_save['payment'] = (isset($devengados[$json->Numero_de_identificacion][$concept->concept_dian]['payment'])) ? $devengados[$json->Numero_de_identificacion][$concept->concept_dian]['payment'] + $json->Valor : $json->Valor;
                        if ($concept->concept_dian == 1) {
                            // sueldo
                            $devengados[$json->Numero_de_identificacion][1] = $data_save;
                        } elseif ($concept->concept_dian == 2) {
                            //auxilio de transporte
                            $devengados[$json->Numero_de_identificacion][2] = $data_save;
                        } elseif (in_array($concept->concept_dian, $concept_hours)) {
                            //horas extras
                            $type_overtime_surcharge_id = $this->getType_overtime_surcharge_id($concept);
                            $data_save['payment'] = $json->Valor;
                            $data_save['quantity'] = (isset($devengados[$json->Numero_de_identificacion]['quantity'])) ? $devengados[$json->Numero_de_identificacion]['quantity'] + $json->Cantidad : $json->Cantidad;
                            $data_save['type_overtime_surcharge_id'] = $type_overtime_surcharge_id;
                            array_push($devengados_sueltos, $data_save);
                        } elseif ($concept->concept_dian == 10) {
                            //viaticos salariales
                            $devengados[$json->Numero_de_identificacion][10] = $data_save;
                        } elseif ($concept->concept_dian == 11) {
                            //viaticos salariales
                            $devengados[$json->Numero_de_identificacion][11] = $data_save;
                        } elseif ($concept->concept_dian == 12 || $concept->concept_dian == 13) {
                            //Vacaciones comunes y pagadas;
                            $data_save['quantity'] = (isset($devengados[$json->Numero_de_identificacion]['quantity'])) ? $devengados[$json->Numero_de_identificacion]['quantity'] + $json->Cantidad : $json->Cantidad;
                            $devengados[$json->Numero_de_identificacion][$concept->concept_dian] = $data_save;
                        } elseif ($concept->concept_dian == 14) {
                            //prima
                            $data_save['quantity'] = (isset($devengados[$json->Numero_de_identificacion]['quantity'])) ? $devengados[$json->Numero_de_identificacion]['quantity'] + $json->Cantidad : $json->Cantidad;
                            $devengados[$json->Numero_de_identificacion][14] = $data_save;
                        } elseif ($concept->concept_dian == 16) {
                            //cesantias
                            if($json->Conceptos_pagos != 'INTERES / CESANTIAS'){
                                if (!in_array($json->Numero_de_identificacion, $cedulas_cesantias)) {
                                    $data_save['quantity'] = (isset($devengados[$json->Numero_de_identificacion]['quantity'])) ? $devengados[$json->Numero_de_identificacion]['quantity'] + $json->Cantidad : $json->Cantidad;
                                    $data_save['other_payments'] = $this->cumulative_data($info, $json->Numero_de_identificacion, 'INTERES / CESANTIAS');
                                    $data_save['percentage'] = 12;
                                    $devengados[$json->Numero_de_identificacion][16] = $data_save;
                                    array_push($cedulas_cesantias, $json->Numero_de_identificacion);
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
                            $data_save['quantity'] = (isset($devengados[$json->Numero_de_identificacion]['quantity'])) ? $devengados[$json->Numero_de_identificacion]['quantity'] + $json->Cantidad : $json->Cantidad;
                            $data_save['type_disability_id'] = $type_disability_id;
                            $data_save['payment'] = $json->Valor;
                            array_push($devengados_sueltos, $data_save);

                        } elseif (in_array($concept->concept_dian, $concept_licencias)) {
                            //licencias
                            $data_save['quantity'] = (isset($devengados[$json->Numero_de_identificacion]['quantity'])) ? $devengados[$json->Numero_de_identificacion]['quantity'] + $json->Cantidad : $json->Cantidad;
                            $devengados[$json->Numero_de_identificacion][$concept->concept_dian] = $data_save;
                        } elseif ($concept->concept_dian == 21) {
                            // bonificacion salarial
                            $devengados[$json->Numero_de_identificacion][21] = $data_save;
                        } elseif ($concept->concept_dian == 22) {
                            // bonificacion no salarial
                            $devengados[$json->Numero_de_identificacion][22] = $data_save;
                        } elseif ($concept->concept_dian == 23) {
                            //auxilio salarial
                            $data_save['payment'] = $json->Valor;
                            array_push($devengados_sueltos, $data_save);
                        } elseif ($concept->concept_dian == 24) {
                            // auxilio no salarial
                            $data_save['payment'] = $json->Valor;
                            array_push($devengados_sueltos, $data_save);
                        } elseif ($concept->concept_dian == 25) {
                            //huelgas legales
                            $data_save['quantity'] = (isset($devengados[$json->Numero_de_identificacion]['quantity'])) ? $devengados[$json->Numero_de_identificacion]['quantity'] + $json->Cantidad : $json->Cantidad;
                            $devengados[$json->Numero_de_identificacion][25] = $data_save;
                        } elseif ($concept->concept_dian == 26 || $concept->concept_dian == 27) {
                            //otros conceptos salariales o no salariales
                            $data_save['payment'] = $json->Valor;
                            array_push($devengados_sueltos, $data_save);
                        } elseif ($concept->concept_dian == 28) {
                            //compensacion Ordinaria
                            $devengados[$json->Numero_de_identificacion][28] = $data_save;
                        } elseif ($concept->concept_dian == 29) {
                            // compensacion extraordinario
                            $devengados[$json->Numero_de_identificacion][29] = $data_save;
                        } elseif ($concept->concept_dian == 30) {
                            $devengados[$json->Numero_de_identificacion][30] = $data_save;
                        } elseif ($concept->concept_dian == 31) {
                            //BONO no salarial
                            $devengados[$json->Numero_de_identificacion][31] = $data_save;
                        } elseif ($concept->concept_dian == 32) {
                            //Bono de alimentacion salarial
                            $devengados[$json->Numero_de_identificacion][32] = $data_save;
                        } elseif ($concept->concept_dian == 33) {
                            //Bono de alimentacion no salarial
                            $devengados[$json->Numero_de_identificacion][33] = $data_save;
                        } elseif ($concept->concept_dian == 34) {
                            //comisiones
                            $devengados[$json->Numero_de_identificacion][34] = $data_save;
                        } elseif ($concept->concept_dian == 35) {
                            //pago a terceros
                            $devengados[$json->Numero_de_identificacion][35] = $data_save;
                        } elseif ($concept->concept_dian == 36) {
                            //anticipo
                            $devengados[$json->Numero_de_identificacion][36] = $data_save;
                        } elseif ($concept->concept_dian == 37) {
                            //dotacion
                            $devengados[$json->Numero_de_identificacion][37] = $data_save;
                        } elseif ($concept->concept_dian == 38) {
                            //apoyo_sotenimiento
                            $devengados[$json->Numero_de_identificacion][38] = $data_save;
                        } elseif ($concept->concept_dian == 39) {
                            //teletrabajo
                            $devengados[$json->Numero_de_identificacion][39] = $data_save;
                        } elseif ($concept->concept_dian == 40) {
                            //bonificacion de retiro
                            $devengados[$json->Numero_de_identificacion][40] = $data_save;
                        } elseif ($concept->concept_dian == 41) {
                            //indemnizacion
                            $devengados[$json->Numero_de_identificacion][41] = $data_save;
                        } elseif ($concept->concept_dian == 42) {
                            //reintegros
                            $devengados[$json->Numero_de_identificacion][42] = $data_save;
                        }
                    } elseif ($concept->type_concept == 'Deduccion') {
                        $data_deductions['type_deduction_id'] = $concept->concept_dian;
                        $data_deductions['description'] = $concept->name;
                        $data_deductions['payment'] = (isset($deducciones[$json->Numero_de_identificacion][$concept->concept_dian]['payment'])) ? $deducciones[$json->Numero_de_identificacion][$concept->concept_dian]['payment'] + $json->Valor : $json->Valor;
                        if ($concept->concept_dian == 1) {
                            //eps
                            $data_deductions['percentage'] = $json->Porcentaje_deducion_salud;
                            if ($json->Porcentaje_deducion_salud == 4) {
                                $data_deductions['type_law_deduction_id'] = 3;
                            }
                            $deducciones[$json->Numero_de_identificacion][1] = $data_deductions;
                        } elseif ($concept->concept_dian == 2) {
                            //eps
                            $data_deductions['percentage'] = $json->Porcentaje_deducion_pension;
                            $data_deductions['type_law_deduction_id'] = 5;
                            $deducciones[$json->Numero_de_identificacion][2] = $data_deductions;
                        } elseif ($concept->concept_dian == 3) {
                            //fondo de solidaridad pensional y subfondo de subsistencia pensional
                            $data_deductions['percentage'] = $json->Porcentaje_Fs;
                            $data_deductions['type_law_deduction_id'] = 9;
                            $data_deductions['payment'] = $json->Valor_FSP;
                            $deducciones[$json->Numero_de_identificacion][3] = $data_deductions;
                            $data_fs = [
                                'percentage' => $json->Porcentaje_Fs,
                                'payment' => $this->cumulative_data_fsp_fs($info, $json->Numero_de_identificacion, 'fs'),
                                'type_deduction_id' => 4,
                                'payroll_id' => $id_payroll,
                                'type_law_deduction_id' => 9,
                                'description' => 'Valor Fondo Subsistencia'
                            ];
                            $this->deductions->insert($data_fs);
                        } elseif ($concept->concept_dian == 5) {
                            //sindicato
                            $deducciones[$json->Numero_de_identificacion][5] = $data_deductions;
                        } elseif ($concept->concept_dian == 6) {
                            //sancion publica
                            $deducciones[$json->Numero_de_identificacion][6] = $data_deductions;
                        } elseif ($concept->concept_dian == 7) {
                            //sancion privada
                            $deducciones[$json->Numero_de_identificacion][7] = $data_deductions;
                        } elseif ($concept->concept_dian == 8) {
                            //libranza
                            $deducciones[$json->Numero_de_identificacion][8] = $data_deductions;
                        } elseif ($concept->concept_dian == 9) {
                            //pago a terceros
                            $deducciones[$json->Numero_de_identificacion][9] = $data_deductions;
                        } elseif ($concept->concept_dian == 10) {
                            //anticipos
                            $deducciones[$json->Numero_de_identificacion][10] = $data_deductions;
                        } elseif ($concept->concept_dian == 11) {
                            //otras deducciones
                            $data_deductions['payment'] = $json->Valor;
                            array_push($deducciones_sueltos, $data_deductions);
                        } elseif ($concept->concept_dian == 12) {
                            //pension voluntaria
                            $deducciones[$json->Numero_de_identificacion][12] = $data_deductions;
                        } elseif ($concept->concept_dian == 13) {
                            //retencion de fuente
                            $deducciones[$json->Numero_de_identificacion][13] = $data_deductions;
                        } elseif ($concept->concept_dian == 14) {
                            //afc
                            $deducciones[$json->Numero_de_identificacion][14] = $data_deductions;
                        } elseif ($concept->concept_dian == 15) {
                            //cooperativa
                            $deducciones[$json->Numero_de_identificacion][15] = $data_deductions;
                        } elseif ($concept->concept_dian == 16) {
                            //embargos
                            $deducciones[$json->Numero_de_identificacion][16] = $data_deductions;
                        } elseif ($concept->concept_dian == 17) {
                            //plan complemetario
                            $deducciones[$json->Numero_de_identificacion][17] = $data_deductions;
                        } elseif ($concept->concept_dian == 18) {
                            //educacion
                            $deducciones[$json->Numero_de_identificacion][18] = $data_deductions;
                        } elseif ($concept->concept_dian == 19) {
                            //reintegro
                            $deducciones[$json->Numero_de_identificacion][19] = $data_deductions;
                        } elseif ($concept->concept_dian == 20) {
                            //deuda
                            $deducciones[$json->Numero_de_identificacion][20] = $data_deductions;
                        }
                    }
                }
            }
        }
        //echo json_encode($devengados);die();
        $total_devengados = array_merge(array_reduce($devengados, 'array_merge', array()), $devengados_sueltos);
        $total_deducciones = array_merge(array_reduce($deducciones, 'array_merge', array()), $deducciones_sueltos);
        //echo json_encode($total_devengados);die();
        try {
            $this->accrueds->insertBatch($total_devengados);
            $this->deductions->insertBatch($total_deducciones);
        }catch (\Exception $e){
            //return redirect()->to(base_url() . '/import/payroll')->with('errors', $e->getMessage());
            //die();
        }
    }

    public function getType_overtime_surcharge_id($concept): int
    {
        $type_overtime_surcharge_id = null;
        switch ($concept->concept_dian) {
            case '3':
                $type_overtime_surcharge_id = 1;
                break;
            case '4':
                $type_overtime_surcharge_id = 2;
                break;
            case '5':
                $type_overtime_surcharge_id = 3;
                break;
            case '6':
                $type_overtime_surcharge_id = 4;
                break;
            case '7':
                $type_overtime_surcharge_id = 5;
                break;
            case '8':
                $type_overtime_surcharge_id = 6;
                break;
            case '9':
                $type_overtime_surcharge_id = 7;
                break;
        }
        return $type_overtime_surcharge_id;
    }

    public function getType_month($month): ?string
    {
        $mes = null;
        switch ($month) {
            case 1:
                $mes = 'Enero';
                break;
            case 2:
                $mes = 'Febrero';
                break;
            case 3:
                $mes = 'Marzo';
                break;
            case 4:
                $mes = 'Abril';
                break;
            case 5:
                $mes = 'Mayo';
                break;
            case 6:
                $mes = 'Junio';
                break;
            case 7:
                $mes = 'Julio';
                break;
            case 8:
                $mes = 'Agosto';
                break;
            case 9:
                $mes = 'Septiembre';
                break;
            case 10:
                $mes = 'Octubre';
                break;
            case 11:
                $mes = 'Noviembre';
                break;
            case 12:
                $mes = 'Diciembre';
                break;
        }
        return $mes;
    }

    public function getType_month_int($month): ?int
    {
        $mes = null;
        switch ($month) {
            case 'Enero':
                $mes = 1;
                break;
            case 'Febrero':
                $mes = 2;
                break;
            case 'Marzo':
                $mes = 3;
                break;
            case 'Abril':
                $mes = 4;
                break;
            case 'Mayo':
                $mes = 5;
                break;
            case 'Junio':
                $mes = 6;
                break;
            case 'Julio':
                $mes = 7;
                break;
            case 'Agosto':
                $mes = 8;
                break;
            case 'Septiembre':
                $mes = 9;
                break;
            case 'Octubre':
                $mes = 10;
                break;
            case 'Noviembre':
                $mes = 11;
                break;
            case 'Diciembre':
                $mes = 12;
                break;
        }
        return $mes;
    }

    public function quantity_workers($period_id){
        $workers = $this->payroll->select('count(payrolls.id) as workers')
            ->join('invoices', 'payrolls.invoice_id = invoices.id')
            ->join('periods', 'periods.id = payrolls.period_id')
            ->where([
                'periods.id'                        => $period_id,
                'invoices.companies_id'             => Auth::querys()->companies_id,
                'invoices.type_documents_id'        => 9
            ])
            ->asObject()
            ->first();
        return $workers->workers;
    }
}