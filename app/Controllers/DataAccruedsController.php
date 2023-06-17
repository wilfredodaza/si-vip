<?php


namespace App\Controllers;

use App\Models\Accrued;
use App\Models\Cargue;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\CustomerWorker;

class DataAccruedsController extends BaseController
{
    public $cargue;
    public $workers;
    public $payroll;
    public $accrueds;
    public $customers;
    public $invoices;
    public $datainvoiceController;

    public function __construct()
    {
        $this->cargue = new Cargue();
        $this->workers = new CustomerWorker();
        $this->payroll = new Payroll();
        $this->accrueds = new Accrued();
        $this->customers = new Customer();
        $this->invoices = new Invoice();
    }

    public function accrueds_Asnomina($info)
    {
        $cedulas_Payment = [];
        $cedulas_transport = [];
        $cedulas_refund = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $deduccion = 'no';
            $id_payroll = $this->data_payroll($json->Numero_de_identificacion, $data->period_id);
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
                        $payment = $this->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
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
                        $payment = $this->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
                        array_push($cedulas_Payment, $json->Numero_de_identificacion);
                    } else {
                        $deduccion = 'si';
                    }
                    break;
                case 'DOMINICAL SENCILLO':
                case 'DOMINICALES/FEST DIURNAS 175%':
                    $type_accrueds = 6;
                    $type_overtime_surcharge_id = 4;
                    $payment = $json->Valor;
                    $quantity = $json->Cantidad;
                    break;
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
                        $payment = $this->cumulative_data($info, $json->Numero_de_identificacion, $json->Conceptos_pagos);
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
            $this->accrueds->insert($data_accrueds);
        }
    }

    public function accrueds_tyc($info)
    {
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_payroll = $this->data_payroll($json->Numero_de_Identificacion, $data->period_id);
            // Salario
            $this->save_data(1, $json->Salario, null, null, '', '', 'Salario', $id_payroll);
            //Auxilio de transporte
            if($json->Auxilio_de_Transporte > 0){
                $this->save_data(2, $json->Auxilio_de_Transporte, null, null, '', '', 'Auxilio de transporte', $id_payroll);
            }
            // Recargo Nocturno
            if($json->Valor_RN_2 > 0){
                $this->save_data(5, $json->Valor_RN_2,null, null, $json->Cantidad_RN_2, 0.35, 'Recargo Nocturno', $id_payroll, '', 3);
            }
            // Horas extras Nocturnas
            if($json->Valor_HENO_2 > 0){
                $this->save_data(4, $json->Valor_HENO_2, null, null, $json->Cantidad_HENO_2, 1.75, 'Horas Extras Nocturnas', $id_payroll, '', 2);
            }
            // Horas Extras Diurnas
            if($json->Valor_HEDO_2 > 0){
                $this->save_data(3, $json->Valor_HEDO_2, null, null, $json->Cantidad_HEDO_2, 1.25, 'Horas Extras Diurnas', $id_payroll, '', 1);
            }
            // Recargo Dominical festivo Diurno
            if($json->Valor_RDF_2 > 0){
                $this->save_data(7, $json->Valor_RDF_2, null, null, $json->Cantidad_RDF_2, 0.75, 'Recargo Dominical Festivo Diurno', $id_payroll, '', 5);
            }
            // Recargo Dominical Festivo Nocturno
            if($json->Valor_RDF_2 > 0){
                $this->save_data(9, $json->Valor_RDF_2, null, null, $json->Cantidad_RDF_2, 1.10, 'Recargo Dominical Festivo Nocturno', $id_payroll, '', 7);
            }
            // Horas Extras Dominicales festivas
            if($json->Valor_HEDF_2 > 0){
                $this->save_data(6, $json->Valor_HEDF_2, null, null, $json->Cantidad_HEDF_2, 1.00, 'Horas Extras Dominicales festivas', $id_payroll, '', 4);
            }
            // Horas Extras Dominicales festivas Nocturnas
            if($json->Valor_HENF_2 > 0){
                $this->save_data(8, $json->Valor_HENF_2, null, null, $json->Cantidad_HENF_2, 1.50, 'Horas Extras Dominicales festivas Nocturnas', $id_payroll, '', 6);
            }
            // Incapacidades
            if($json->Incapacidades > 0){
                $this->save_data(17, $json->Incapacidades, $json->Fecha_inicio_incapacidades_2, $json->Fecha_fin_incapacidades_2, $json->Duracion_incapacidad_2, '', 'Incapacidades', $id_payroll, 1);
            }
            // Licencias Remuneradas
            if($json->Duracion_LR_2 > 0){
                $this->save_data(19, 0, $json->Inicial_LR_2, $json->Final_LR_2, $json->Duracion_LR_2, '', 'Licencia Remunerada', $id_payroll);
            }
            // Licencias No Remunerada
            if($json->Dias_Licencia_no_remunerada > 0){
                $this->save_data(20, 0, $json->Fecha_Inicio_2, $json->Fecha_Fin_2, $json->Dias_Licencia_no_remunerada, '', 'Licencia No Remunerada', $id_payroll);
            }
            // Vacaciones Comunes
            if($json->Vacaciones > 0){
                $this->save_data(12, $json->Vacaciones, $json->Fecha_Inicio_Vacaciones, $json->Fecha_Fin_Vacaciones, $json->Dias_Habiles_de_Vacaciones, '', 'Vacaciones', $id_payroll);
            }
            // Bonificacion No salarial
            if($json->No_Salarial_bonificaciones_2 > 0){
                $this->save_data(22, $json->No_Salarial_bonificaciones_2, null, null, '', '', $json->Descripcion_bonificaciones_2, $id_payroll);
            }
            // Bononificacioon salarial
            if($json->Salarial_bonificaciones_2 > 0){
                $this->save_data(21, $json->Salarial_bonificaciones_2, null, null, '', '', $json->Descripcion_bonificaciones_2, $id_payroll);
            }
            // Auxilio Salarial
            if($json->Total_Auxilios_Salariales > 0){
                $this->save_data(24, $json->Total_Auxilios_Salariales, null, null, '', '', 'Auxilio Salarial', $id_payroll);
            }
            // Auxilio No Salarial
            if($json->Total_Auxilios_NO_Salariales > 0){
                $this->save_data(25, $json->Total_Auxilios_NO_Salariales, null, null, '', '', 'Auxilio No Salarial', $id_payroll);
            }
            // Comision
            if($json->Valor_comisiones_2  > 0){
                $this->save_data(34, $json->Valor_comisiones_2, null, null, '', '', $json->Concepto_comisiones_2, $id_payroll);
            }
            //reembolso
            if($json->Valor_reembolsos_2 > 0){
                $this->save_data(42, $json->Valor_reembolsos_2, null, null, '', '', $json->Concepto_reembolsos_2, $id_payroll);
            }
            // otros conceptos salariales
            if($json->Salarial_PGC_2 > 0){
                $this->save_data(26, $json->Salarial_PGC_2, null, null, '', '', $json->Descripcion_PGC_2, $id_payroll);
            }
            //otros conceptos no salariales
            if($json->No_Salarial_PGC_2 > 0){
                $this->save_data(27, $json->No_Salarial_PGC_2, null, null, '', '', $json->Descripcion_PGC_2, $id_payroll);
            }
        }
    }

    public function data_payroll($identification, $period_id)
    {
        $customer = $this->customers->where(['identification_number' => $identification])->get()->getResult();
        $invoice = $this->invoices->join('payrolls', 'invoices.id = payrolls.invoice_id')
            ->select('*,payrolls.id as id_payroll')
            ->where([
                'invoices.customers_id' => $customer[0]->id,
                'payrolls.period_id' => $period_id])->get()->getResult();
        return $invoice[0]->id_payroll;
    }

    public function cumulative_data($info, $identification, $search)
    {
        $payment = 0;
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if ($json->Conceptos_pagos == $search) {
                if ($identification == $json->Numero_de_identificacion) {
                    $payment += $json->Valor;
                }
            }
        }
        return $payment;
    }

    public function save_data($type_accrueds, $payment, $start_time = null, $end_time = null, $quantity = '',
                              $percentage = '', $description, $id_payroll, $type_disability_id = null, $type_overtime_surcharge_id = null)
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
            'type_overtime_surcharge_id' => $type_overtime_surcharge_id
        ]);
    }
}
