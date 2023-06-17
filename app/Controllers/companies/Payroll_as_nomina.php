<?php


namespace App\Controllers\companies;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\PayrollDate;


class Payroll_as_nomina extends BaseController
{
    public $functions_payroll;
    public $invoices;
    public $payroll;
    public $payrollDate;
    public $accrued;
    public $deduction;

    public function __construct()
    {
        $this->functions_payroll = new Functions_Payroll();
        $this->invoices = new Invoice();
        $this->payroll = new Payroll();
        $this->payrollDate = new PayrollDate();
        $this->accrued = new Accrued();
        $this->deduction = new Deduction();
    }

    public function workers($nit, $period, $month_payroll)
    {
        $info = $this->functions_payroll->data($nit, $period, $month_payroll);
        $empleados = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            // se realiza transformacion de datos para Documento de Identificación
            if ($json->Tipo_de_documento == 1) {
                $documento = 11;
            }
            if ($json->Tipo_de_documento == 5) {
                $documento = 12;
            }
            // se realiza transformacion de datos para Tipo de contrato
            if ($json->Tipo_de_contrato == 1) {
                $type_contract_id = 2; // Contrato indefinido
            } elseif ($json->Tipo_de_contrato == 2) {
                $type_contract_id = 1; // fijo
            } elseif ($json->Tipo_de_contrato == 3){
                $type_contract_id = 3; // obra labor
            }
            // se realiza transformacion de datos para periodo de nomina
            if ($json->periodo_de_nomina == 2) {
                $payroll_period = 4; // quincenal
            }
            // se realiza transformacion de datos para Tipo de cuenta bancaria
            if ($json->Tipo_de_cuenta == 2) {
                $bank_account_type = 1; //Ahorros
            }
            $bank = $this->functions_payroll->banks($json->Nombre_banco);
            // se realiza transformacion de subtipo de trabajador
            if ($json->Subtipo_trabajador == 0) {
                $subtype_worker = 1;
            }
            if (!in_array($json->Numero_de_identificacion, $empleados)) {
                //valido si el trabajador existe
                $worker = $this->functions_payroll->worker_data($json->Numero_de_identificacion);
                if (count($worker) < 1) {
                    // json datos del trabajador para realizar el guardado
                    $data_customer = [
                        'name' => $json->Primer_nombre,
                        'type_document_identifications_id' => $documento,
                        'identification_number' => $json->Numero_de_identificacion,
                        'address' => $json->Direccion,
                        'email' => ($json->email ?? ''),
                        'type_customer_id' => 3,
                        'municipality_id' => 149,
                        'companies_id' => Auth::querys()->companies_id, //cambiar por empresa
                        'status' => 'Activo'
                    ];
                    $data_customer_worker = [
                        'type_worker_id' => $json->Tipo_trabajador,
                        'sub_type_worker_id' => $subtype_worker,
                        'bank_id' => $bank,
                        'bank_account_type_id' => $bank_account_type,
                        'type_contract_id' => $type_contract_id,
                        'payment_method_id' => $json->metodo_de_pago,
                        'account_number' => $json->Numero_de_cuenta,
                        'surname' => $json->Apellido,
                        'second_surname' => $json->Segundo_apellido,
                        'high_risk_pension' => ($json->pension_de_alto_riesgo ?? 'false'),
                        'integral_salary' => ($json->Salario_integral == 'FALSO') ? 'false' : 'true',
                        'salary' => $json->Salario,
                        'admision_date' => $this->functions_payroll->split_date($json->Fecha_ingreso),
                        'retirement_date' => (isset($json->fecha_retiro) && $json->fecha_retiro != '0000-00-00' && $json->fecha_retiro != '-   -') ? $this->functions_payroll->split_date($json->fecha_retiro) : null,
                        'payroll_period_id' => $data->payroll_period
                    ];
                    $customer_id = $this->functions_payroll->add_customer($data_customer, $data_customer_worker);
                    if (isset($json->email)) {
                        $data_user = [
                            'name' => $json->Primer_nombre,
                            'username' => $json->Primer_nombre,
                            'email' => $json->email,
                            'password' => password_hash($json->Numero_de_identificacion, PASSWORD_DEFAULT),
                            'status' => 'active',
                            'role_id' => 7,
                            'companies_id' => Auth::querys()->companies_id
                        ];
                        $this->functions_payroll->add_user($data_user, $customer_id);
                    }
                } else {
                    //valido si trabajador tiene correo
                    if (isset($json->email)) {
                        // valida si el trabajador tiene usuario en el sistema
                        if (empty($worker->user_id) || $worker->user_id == null) {
                            $data_user = [
                                'name' => $json->Primer_nombre,
                                'username' => $json->Primer_nombre,
                                'email' => $json->email,
                                'password' => password_hash($json->Numero_de_identificacion, PASSWORD_DEFAULT),
                                'status' => 'active',
                                'role_id' => 7,
                                'companies_id' => Auth::querys()->companies_id
                            ];
                            // crea el usuario y actualiza el user id en la tabla customer
                            $this->functions_payroll->add_user($data_user, $worker);
                        }
                    }
                    // valido actualizaciones
                    $data_update = [
                        'type_contract_id' => $type_contract_id,
                        'bank' => $bank,
                        'bank_account_type' => $bank_account_type,
                        'payment_method_id' => $json->metodo_de_pago,
                        'account_number' => $json->Numero_de_cuenta,
                    ];
                    $this->functions_payroll->update_customer($worker, $data_update);
                }
                array_push($empleados, $json->Numero_de_identificacion);
            }
        }
        $this->invoice($info);
    }
    public function invoice($info)
    {
        $cedulas = [];
        $dates = $this->functions_payroll->settlement_dates($info);
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if (!in_array($json->Numero_de_identificacion, $cedulas)) {
                $id_customer = $this->functions_payroll->customer_id($json->Numero_de_identificacion);
                $id_invoice = $this->invoices->insert([
                    'resolution_id' => null,
                    'prefix' => null,
                    'customers_id' => $id_customer,
                    'invoice_status_id' => 13,
                    'notes' => '',
                    'type_documents_id' => 9,
                    'companies_id' => Auth::querys()->companies_id,
                ]);
                $this->payroll->insert([
                    'settlement_start_date' => $dates['settlement_start_date'],
                    'settlement_end_date' => $dates['settlement_end_date'],
                    'worked_time' => $this->functions_payroll->worker_days($info, $json->Numero_de_identificacion),
                    'invoice_id' => $id_invoice,
                    'period_id' => $data->period_id
                ]);
                $json_dates = json_decode($data->payment_dates);
                foreach ($json_dates as $json_date) {
                    $this->payrollDate->insert([
                        'invoice_id' => $id_invoice,
                        'payroll_date' => $json_date
                    ]);
                }
                array_push($cedulas, $json->Numero_de_identificacion);
            }
        }
        $this->accrueds($info);
        $this->deductions($info);
    }
    public function accrueds($info)
    {
        $cedulas_Payment = [];
        $cedulas_transport = [];
        $cedulas_refund = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $deduccion = 'no';
            $id_payroll = $this->functions_payroll->data_payroll($json->Numero_de_identificacion, $data->period_id);
            $type_disability_id = null;
            switch ($json->Conceptos_pagos) {
                case 'RECARGO NOCTURNO ORDIN. 0.35%':
                    $type_accrueds = 5;
                    $quantity = $json->Cantidad;
                    $percentage = 3;
                    $payment = $json->Valor;
                    $type_overtime_surcharge_id = 3;
                    break;
                case 'HORAS EXTRAS NOCTURNAS 175%':
                    $type_accrueds = 4;
                    $quantity = $json->Cantidad;
                    $percentage = 2;
                    $payment = $json->Valor;
                    $type_overtime_surcharge_id = 2;
                    break;
                case 'HORAS EXTRAS DIURNAS 125%':
                case 'HORAS EXTRAS DIURNAS':
                    $type_accrueds = 3;
                    $quantity = $json->Cantidad;
                    $percentage = 1;
                    $payment = $json->Valor;
                    $type_overtime_surcharge_id = 1;
                    break;
                case 'DOMINICALES/FEST DIURNAS 175%':
                    $type_accrueds = 6;
                    $quantity = $json->Cantidad;
                    $percentage = 1;
                    $payment = $json->Valor;
                    $type_overtime_surcharge_id = 4;
                    break;
                case 'INCAPACIDAD  100% ACCIDTE. TRA':
                    $type_disability_id = 3;
                    $type_accrueds = 17;
                    $quantity = $json->Cantidad;
                    $payment = $json->Valor;
                    $fecha_inicio = '00-00-00 00:00:00';
                    $fecha_fin = '00-00-00 00:00:00';
                    break;
                case 'INCAPACIDAD EGM X EPS':
                case 'INCAPACIDAD EMPLEADOR':
                    $type_disability_id = 1;
                    $type_accrueds = 17;
                    $quantity = $json->Cantidad;
                    $payment = $json->Valor;
                    $fecha_inicio = '00-00-00 00:00:00';
                    $fecha_fin = '00-00-00 00:00:00';
                    break;
                case 'SUBSIDIO DE TRANSPORTE':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_transport)) {
                        $type_accrueds = 2;
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        array_push($cedulas_transport, $json->Numero_de_identificacion);
                    } else {
                        $deduccion = 'si';
                    }
                    break;
                case 'BONO VAR. PRODUCT NO SALARIAL':
                    $type_accrueds = 31;
                    $payment = $json->Valor;
                    break;
                case 'SALARIO BASICO':
                case 'SALARIO INTEGRAL':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_Payment)) {
                        $type_accrueds = 1;
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        array_push($cedulas_Payment, $json->Numero_de_identificacion);
                    } else {
                        $deduccion = 'si';
                    }
                    break;
                case 'DOMINICAL SENCILLO':
                case 'AUXILIO DE INCAPACIDAD 2/3':
                case 'AUXILIO DE INCAPACIDAD 2/3 EMP':
                case 'AUXILIO INCAPACIDAD EMPLEADOR':
                case 'AUXILIO INCAPACIDAD EPS':
                    $type_accrueds = 23;
                    $payment = $json->Valor;
                    break;
                case 'AJUSTE INCAPACIDAD S. INTEGRAL':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_refund)) {
                        $type_accrueds = 42;
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        array_push($cedulas_refund, $json->Numero_de_identificacion);
                    } else {
                        $deduccion = 'si';
                    }
                    break;
                case 'BONIFICACIONES':
                    $type_accrueds = 21;
                    $payment = $json->Valor;
                    break;
                case 'LICENCIA DE MATERNIDAD':
                    $type_accrueds = 18;
                    $quantity = $json->Cantidad;
                    $payment = $json->Valor;
                    break;
                case 'RODAMIENTO':
                    $type_accrueds = 27;
                    $payment = $json->Valor;
                    break;
                //DEDUCCIONES
                case 'LIBRANZA COMPENSAR':
                case 'PENSION-[COLPENSIONES]':
                case 'PENSION-[COLFONDOS PENSIONES]':
                case 'PENSION-[PORVENIR PENSIONES]':
                case 'PENSION-[COLFONDOS]':
                case 'PENSION-[PORVENIR]':
                case 'PENSION-[PROTECCION S.A]':
                case 'SALUD-[SALUD TOTAL]':
                case 'SALUD-[COMPENSAR EPS]':
                case 'SALUD-[COMPENSAR - EPS]':
                case 'SALUD-[NUEVA EPS]':
                case 'SALUD-[MUTUAL SER]':
                case 'SALUD-[AMBUQ]':
                case 'SALUD-[CAJACOPI ATLANTICO]':
                case 'SALUD-[MEDIMAS EPS]':
                case 'SALUD-[EPS SURA]':
                case 'SALUD-[EPS - FAMISANAR LTDA]':
                case 'SALUD-[COOSALUD]':
                case 'SALUD-[COOMEVA  E.P.S. S.A.]':
                case 'SALUD-[EPS SANITAS]':
                case 'SALUD-[SANITAS EPS  S.A.]':
                case 'SALUD-[ALIANSALUD EPS]':
                case 'DESCUENTO AFC':
                case 'DESCUENTO PENSIONES VOLUNTARIA':
                case 'DESCUENTO COMISION OTROS BANCO':
                case 'DESCUENTO LIBERTY SEGUROS S.A.':
                case 'CUOTA AFILIACION POLYFONDO':
                case 'APORTES POLYFONDO':
                case 'APORTES EXTRAORDINARIOS':
                case 'INT. PRESTAMO POLYFONDO':
                case 'PRESTAMO POLYFONDO':
                case 'RETARDOS/PERMISOS':
                case 'INTERESES POLYFONDO':
                case 'DESCUENTO FACT CELULAR':
                case 'DESCUENTO RTE FUENTE':
                case 'FONDO SOLIDARIDAD PENSIONAL':
                case 'EMBARGO CIVIL':
                    $deduccion = 'si';
                    break;
            }
            if ($deduccion == 'si') {
                continue;
            }
            $data_accrueds = [
                'type_accrued_id' => $type_accrueds,
                'payment' => $payment,
                'start_time' => null,
                'end_time' => null,
                'quantity' => ($quantity ?? ''),
                'percentage' => ($percentage ?? ''),
                'description' => $json->Conceptos_pagos,
                'payroll_id' => $id_payroll,
                'type_disability_id' => ($type_disability_id ?? null),
                'type_overtime_surcharge_id' => ($type_overtime_surcharge_id ?? null)
            ];
            $this->accrued->insert($data_accrueds);
        }
    }
    public function deductions($info)
    {
        $cedulas_pension = [];
        $cedulas_eps = [];
        $cedulas_libranza = [];
        $cedulas_afc = [];
        $cedulas_pension_voluntaria = [];
        $cedulas_spension = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $accrueds = 'no';
            $id_payroll = $this->functions_payroll->data_payroll($json->Numero_de_identificacion, $data->period_id);
            $type_law_deduction_id = null;
            switch ($json->Conceptos_pagos) {
                //DEVENGADOS
                case 'SUBSIDIO DE TRANSPORTE':
                case 'BONO VAR. PRODUCT NO SALARIAL':
                case 'SALARIO BASICO':
                case 'RECARGO NOCTURNO ORDIN. 0.35%':
                case 'HORAS EXTRAS NOCTURNAS 175%':
                case 'HORAS EXTRAS DIURNAS 125%':
                case 'INCAPACIDAD  100% ACCIDTE. TRA':
                case 'INCAPACIDAD EGM X EPS':
                case 'INCAPACIDAD EMPLEADOR':
                case 'HORAS EXTRAS DIURNAS':
                case 'SALARIO INTEGRAL':
                case 'DOMINICAL SENCILLO':
                case 'DOMINICALES/FEST DIURNAS 175%':
                case 'AUXILIO DE INCAPACIDAD 2/3':
                case 'AUXILIO DE INCAPACIDAD 2/3 EMP':
                case 'AUXILIO INCAPACIDAD EPS':
                case 'AUXILIO INCAPACIDAD EMPLEADOR':
                case 'AJUSTE INCAPACIDAD S. INTEGRAL':
                case 'BONIFICACIONES':
                case 'LICENCIA DE MATERNIDAD':
                case 'RODAMIENTO':
                    $accrueds = 'si';
                    break;
                // DEDUCIONES
                case 'LIBRANZA COMPENSAR':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_libranza)) {
                        $type_deduction_id = 8;
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        array_push($cedulas_libranza, $json->Numero_de_identificacion);
                    } else {
                        $accrueds = 'si';
                    }
                    break;
                case 'PENSION-[COLPENSIONES]':
                case 'PENSION-[COLFONDOS PENSIONES]':
                case 'PENSION-[COLFONDOS]':
                case 'PENSION-[PORVENIR PENSIONES]':
                case 'PENSION-[PORVENIR]':
                case 'PENSION-[PROTECCION S.A]':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_pension)) {
                        $type_deduction_id = 2;
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        $percentage = $json->Porcentaje_deducion_pension;
                        if ($json->Porcentaje_deducion_pension == 4) {
                            $type_law_deduction_id = 5;
                        }
                        array_push($cedulas_pension, $json->Numero_de_identificacion);
                    } else {
                        $accrueds = 'si';
                    }
                    break;
                case 'SALUD-[SALUD TOTAL]':
                case 'SALUD-[COMPENSAR EPS]':
                case 'SALUD-[COMPENSAR - EPS]':
                case 'SALUD-[NUEVA EPS]':
                case 'SALUD-[MUTUAL SER]':
                case 'SALUD-[MEDIMAS EPS]':
                case 'SALUD-[EPS SURA]':
                case 'SALUD-[EPS - FAMISANAR LTDA]':
                case 'SALUD-[COOSALUD]':
                case 'SALUD-[COOMEVA  E.P.S. S.A.]':
                case 'SALUD-[EPS SANITAS]':
                case 'SALUD-[AMBUQ]':
                case 'SALUD-[CAJACOPI ATLANTICO]':
                case 'SALUD-[SANITAS EPS  S.A.]':
                case 'SALUD-[ALIANSALUD EPS]':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_eps)) {
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        $type_deduction_id = 1;
                        $percentage = $json->Porcentaje_deducion_salud;
                        if ($json->Porcentaje_deducion_salud == 4) {
                            $type_law_deduction_id = 3;
                        }
                        array_push($cedulas_eps, $json->Numero_de_identificacion);
                    } else {
                        $accrueds = 'si';
                    }
                    break;
                case 'DESCUENTO AFC':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_afc)) {
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        $type_deduction_id = 14;
                        array_push($cedulas_afc, $json->Numero_de_identificacion);
                    } else {
                        $accrueds = 'si';
                    }
                    break;
                case 'DESCUENTO PENSIONES VOLUNTARIA':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_pension_voluntaria)) {
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        $type_deduction_id = 12;
                        array_push($cedulas_pension_voluntaria, $json->Numero_de_identificacion);
                    } else {
                        $accrueds = 'si';
                    }
                    break;
                case 'DESCUENTO RTE FUENTE':
                    $type_deduction_id = 13;
                    $payment = $json->Valor;
                    break;
                case 'SUSPENSION':
                    $type_deduction_id = 6;
                    $payment = $json->Valor;
                    break;
                case 'DESCUENTO COMISION OTROS BANCO':
                case 'DESCUENTO LIBERTY SEGUROS S.A.':
                case 'CUOTA AFILIACION POLYFONDO':
                case 'APORTES POLYFONDO':
                case 'APORTES EXTRAORDINARIOS':
                case 'INT. PRESTAMO POLYFONDO':
                case 'PRESTAMO POLYFONDO':
                case 'RETARDOS/PERMISOS':
                case 'INTERESES POLYFONDO':
                case 'DESCUENTO FACT CELULAR':
                case 'DESCUENTO PARQUEADERO':
                    $type_deduction_id = 11;
                    $payment = $json->Valor;
                    break;
                case 'FONDO SOLIDARIDAD PENSIONAL':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_spension)) {
                        $type_deduction_id = 3;
                        $payment = $this->functions_payroll->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        $type_law_deduction_id = 9;
                        array_push($cedulas_spension, $json->Numero_de_identificacion);
                    } else {
                        $accrueds = 'si';
                    }
                    break;
                case 'EMBARGO CIVIL':
                    $type_deduction_id = 16;
                    $payment = $json->Valor;
                    break;
            }
            if ($accrueds == 'si') {
                continue;
            }
            $data_deductions = [
                'percentage' => ($percentage ?? ''),
                'payment' => $payment,
                'type_deduction_id' => $type_deduction_id,
                'payroll_id' => $id_payroll,
                'type_law_deduction_id' => ($type_law_deduction_id ?? null),
                'description' => $json->Conceptos_pagos
            ];
            $this->deduction->insert($data_deductions);
        }
    }

}

