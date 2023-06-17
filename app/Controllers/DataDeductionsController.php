<?php


namespace App\Controllers;

use App\Models\Deduction;
use App\Models\Cargue;
use App\Models\Payroll;


class DataDeductionsController extends BaseController
{
    public $cargue;
    public $workers;
    public $payroll;
    public $deductions;
    public $data_payroll_accrued;
    public $datainvoiceController;

    public function __construct()
    {
        $this->cargue = new Cargue();
        $this->payroll = new Payroll();
        $this->deductions = new Deduction();
        $this->data_payroll_accrued = new DataAccruedsController();
    }

    public function deductions_Asnomina($info)
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
            $id_payroll = $this->data_payroll_accrued->data_payroll($json->Numero_de_identificacion, $data->period_id);
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
                case 'DOMINICALES/FEST DIURNAS 175%':
                case 'LICENCIA DE MATERNIDAD':
                case 'RODAMIENTO':
                    $accrueds = 'si';
                    break;
                // DEDUCIONES
                case 'LIBRANZA COMPENSAR':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_libranza)) {
                        $type_deduction_id = 8;
                        $payment = $this->data_payroll_accrued->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
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
                        $payment = $this->data_payroll_accrued->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
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
                        $payment = $this->data_payroll_accrued->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
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
                        $payment = $this->data_payroll_accrued->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        $type_deduction_id = 14;
                        array_push($cedulas_afc, $json->Numero_de_identificacion);
                    } else {
                        $accrueds = 'si';
                    }
                    break;
                case 'DESCUENTO PENSIONES VOLUNTARIA':
                    if (!in_array($json->Numero_de_identificacion, $cedulas_pension_voluntaria)) {
                        $payment = $this->data_payroll_accrued->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
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
                        $payment = $this->data_payroll_accrued->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
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
            $this->deductions->insert($data_deductions);
        }
    }
    public function deductions_tyc($info)
    {
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_payroll = $this->data_payroll_accrued->data_payroll($json->Numero_de_Identificacion, $data->period_id);
            // eps
            $this->save_data(1,$json->Aporte_para_Salud,'Aporte para Salud',$id_payroll,'',3);
            // pension
            $this->save_data(2,$json->Aporte_para_Pension,'Aporte para Pensión',$id_payroll,'',5);
            // libranza
            if($json->Valor_libranza_2 > 0){
                $this->save_data(8,$json->Valor_libranza_2,$json->Descripcion_libranza_2,$id_payroll);
            }
            // otras deducciones
            if($json->Valor_1_2 > 0){
                $this->save_data(11,$json->Valor_1_2,$json->Concepto1_2,$id_payroll);
            }elseif($json->Valor_2_2 > 0){
            $this->save_data(11,$json->Valor_2_2,$json->Concepto2_2,$id_payroll);
            }elseif($json->Valor_3_2 > 0){
            $this->save_data(11,$json->Valor_3_2,$json->Concepto3_2,$id_payroll);
            }
            //pension voluntaria
            if($json->Valor_apv_2 > 0){
                $this->save_data(12,$json->Valor_apv_2,'Pension Voluntaria',$id_payroll);
            }
            // retefuente
            if($json->Base_Para_Rtefuente == 'SI'){
                $this->save_data(13,$json->Retencion_en_la_fuente,'Retención en la fuente',$id_payroll);
            }
            // afc
            if($json->Valor_afc_2 > 0){
                $this->save_data(14,$json->Valor_afc_2,'AFC',$id_payroll);
            }
        }
    }
    public function save_data($type_deduction_id, $payment, $description, $id_payroll, $percentage = '',  $type_law_deduction_id = null)
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
}

