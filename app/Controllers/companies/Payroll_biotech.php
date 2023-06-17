<?php


namespace App\Controllers\companies;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Accrued;
use App\Models\Bank;
use App\Models\Cargue;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OtherConcepts;
use App\Models\Payroll;
use App\Models\CustomerWorker;
use App\Models\PayrollDate;
use App\Models\TypeAccountBank;
use App\Models\TypeWorker;

class Payroll_biotech extends BaseController
{
    public $cargue;
    public $workers;
    public $payroll;
    public $accrueds;
    public $customers;
    public $invoices;
    public $other_concepts;
    // workers
    public $type_worker;
    public $banks;
    public $bank_account_types;
    public $payrollDate;
    //functions
    public $functions_payroll;
    public $info;

    private $code_method_payment;

    public function __construct()
    {
        $this->cargue = new Cargue();
        $this->workers = new CustomerWorker();
        $this->payroll = new Payroll();
        $this->accrueds = new Accrued();
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
        $this->code_method_payment = [2,3,4,5,6,7,21,22,30,31,42,45,46,47];

    }

    public function workers($nit, $month_payroll)
    {
        $info = $this->functions_payroll->data($nit, $month_payroll);
        foreach ($info as $data) {
            $bank = null;
            $bank_account_type = null;
            $documento = 0;
            $type_contract_id = 0;
            $json = json_decode($data->data);
            $Ti = utf8_encode($json->Tipo_de_identificacion);
            if (strtolower($this->functions_payroll->eliminar_tildes(utf8_decode($Ti))) == "cedula de ciudadania") {
                $documento = 3;
            }elseif (strtolower($this->functions_payroll->eliminar_tildes(utf8_decode($Ti))) == "cedula de extranjeria") {
                $documento = 5;
            }
            if( $json->Tipo_de_trabajador == 'Dependiente'){
                $type_worker = 1;
            }elseif($json->Tipo_de_trabajador == 'Aprendiz Sena Etapa Lectiva'){
                $type_worker = 5;
            }elseif($json->Tipo_de_trabajador == 'Aprendiz Sena Etapa Productiva'){
                $type_worker = 8;
            }
            if(in_array($json->Codigo_Metodo_de_pago, $this->code_method_payment)){
                $bank = $this->functions_payroll->banks($json->BANCO);
                if(strtolower($json->TIPO_DE_CUENTA) == 'cta ahorros'){
                    $bank_account_type = 1;
                }elseif (strtolower($json->TIPO_DE_CUENTA) == 'cta corriente'){
                    $bank_account_type = 2;
                }
            }
            if (strtolower($json->Subtipo_de_trabajador) == 'no aplica') {
                $subtype_worker = 1;
            }
            if ($json->Tipo_de_contrato == 'Termino Indefinido') {
                $type_contract_id = 2;
            } elseif ($json->Tipo_de_contrato == 'Termino fijo') {
                $type_contract_id = 1;
            } elseif ($json->Tipo_de_contrato == 'Obra o labor') {
                $type_contract_id = 3;
            } elseif ($json->Tipo_de_contrato == 'Practicante en etapa lectiva') {
                $type_contract_id = 4;
            } elseif ($json->Tipo_de_contrato == 'Practicante universitario' || $json->Tipo_de_contrato == 'Practicante en etapa productiva') {
                $type_contract_id = 5;
            }
            $worker = $this->functions_payroll->worker_data($json->Numero_de_Identificacion);
            if (is_null($worker)) {
                // json datos del trabajador para realizar el guardado
                $data_customer = [
                    'name' => $json->Primer_Nombre,
                    'type_document_identifications_id' => $documento,
                    'identification_number' => $json->Numero_de_Identificacion,
                    'address' => $json->Direccion,
                    'email' => ($json->Correo_electronico_laboral ?? ''),
                    'type_customer_id' => 3,
                    'municipality_id' => $json->Codigo_Municipio,
                    'companies_id' => Auth::querys()->companies_id, //cambiar por empresa
                ];
                $data_customer_worker = [
                    'type_worker_id' => $type_worker,
                    'sub_type_worker_id' => $subtype_worker,
                    'bank_id' => $bank,
                    'bank_account_type_id' => $bank_account_type,
                    'type_contract_id' => $type_contract_id,
                    'payment_method_id' => $json->Codigo_Metodo_de_pago,
                    'account_number' => $json->No_Cta_de_Destino,
                    'second_name' => $json->Segundo_Nombre,
                    'surname' => $json->Primer_Apellido,
                    'second_surname' => $json->Segundo_Apellido,
                    'high_risk_pension' => (strtolower($json->Pension_de_alto_riesgo) == 'no aplica') ? 'false' : 'true',
                    'integral_salary' => (strtolower($json->Tiene_salario_Integral) == 'no') ? 'false' : 'true',
                    'salary' => $json->Salario,
                    'admision_date' => $this->functions_payroll->split_date($json->Fecha_Ingreso),
                    'retirement_date' => ($this->functions_payroll->split_date($json->Fecha_Retiro) ?? null),
                    'payroll_period_id' => $data->payroll_period,
                    'worker_code' => $json->Codigo_Trabajador,
                    'work' => $json->Cargo
                ];
                $customer_id = $this->functions_payroll->add_customer($data_customer, $data_customer_worker);
                if (isset($json->Correo_electronico_laboral) && $json->Correo_electronico_laboral != '') {
                    $data_user = [
                        'name' => $json->Primer_Nombre,
                        'username' => $json->Correo_electronico_laboral,
                        'email' => $json->Correo_electronico_laboral,
                        'password' => password_hash($json->Numero_de_Identificacion, PASSWORD_DEFAULT),
                        'status' => 'active',
                        'role_id' => 7,
                        'companies_id' => Auth::querys()->companies_id
                    ];
                    $this->functions_payroll->add_user($data_user, $customer_id);
                }
            } else {
                //valido si trabajador tiene correo
                if (isset($json->Correo_electronico_laboral) && $json->Correo_electronico_laboral != '') {
                    // valida si el trabajador tiene usuario en el sistema
                    if (empty($worker->user_id) || is_null($worker->user_id)) {
                        $data_user = [
                            'name' => $json->Primer_Nombre,
                            'username' => $json->Correo_electronico_laboral,
                            'email' => $json->Correo_electronico_laboral,
                            'password' => password_hash($json->Numero_de_Identificacion, PASSWORD_DEFAULT),
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
                    'bank' => $bank,
                    'bank_account_type' => $bank_account_type,
                    'payment_method_id' => $json->Codigo_Metodo_de_pago,
                    'account_number' => $json->No_Cta_de_Destino,
                    'salary' => $json->Salario,
                    'retirement_date' => ($this->functions_payroll->split_date($json->Fecha_Retiro) ?? null)
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
            if($data->type_document_payroll ==  10){
                try {
                    $ajuste = $this->invoices
                        ->join('customers','customers.id = invoices.customers_id')
                        ->join('payrolls','invoices.id = payrolls.invoice_id')
                        ->where(['invoices.companies_id' => company()->id,
                            'invoices.invoice_status_id' => 14,
                            'invoices.type_documents_id' => 9,
                            'customers.identification_number' => $json->Numero_de_Identificacion,
                            'payrolls.period_id' => $data->period_id])
                        ->get()->getResult()[0];
                } catch (\Exception $e){
                    //return redirect()->to(base_url() . '/import/payroll')->with('errors', $e);
                    //die();
                }
            }
            $id_customer = $this->functions_payroll->customer_id($json->Numero_de_Identificacion);
            $id_invoice = $this->invoices->insert([
                'resolution_id' => null,
                'prefix' => null,
                'customers_id' => $id_customer,
                'invoice_status_id' => 13,
                'notes' => $json->notas,
                'type_documents_id' => $data->type_document_payroll,
                'companies_id' => Auth::querys()->companies_id,
                'uuid' => ($ajuste->uuid ?? null),
                'resolution_credit' => ($ajuste->resolution ?? null),
                'issue_date' => ($ajuste->created_at ?? null)
            ]);
            $this->payroll->insert([
                'settlement_start_date' => $this->functions_payroll->split_date($json->Fecha_inicio_de_liquidacion_de_nomina),
                'settlement_end_date' => $this->functions_payroll->split_date($json->Fecha_fin_liquidacion_de_nomina),
                'worked_time' => $json->Dias_Laborados_nomina,
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
        $this->accrueds($info);
        $this->deductions($info);
    }

    public function accrueds($info)
    {
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_payroll = $this->functions_payroll->data_payroll_tyc($json->Numero_de_Identificacion, $data->period_id, $data->load_number, $data->type_document_payroll );
            // Salario
            $this->functions_payroll->save_data_accrueds(1, $json->Salario, null, null, null, null,
                'Salario', $id_payroll);
            //Auxilio de transporte L
            if ($json->Auxilio_de_Transporte > 0) {
                $this->functions_payroll->save_data_accrueds(2, $json->Auxilio_de_Transporte, null, null, null,
                    null, 'Auxilio de transporte', $id_payroll);
            }
            // Horas extras Nocturnas l
            if ($json->Valor_HENO_2 > 0) {
                $this->functions_payroll->save_data_accrueds(4, $json->Valor_HENO_2, null, null, $json->Cantidad_HENO_2,
                    1.75, 'Horas Extras Nocturnas', $id_payroll, null, 2);
            }
            // Horas Extras Diurnas l
            if ($json->Valor_HEDO_2 > 0) {
                $this->functions_payroll->save_data_accrueds(3, $json->Valor_HEDO_2, null, null, $json->Cantidad_HEDO_2,
                    1.25, 'Horas Extras Diurnas', $id_payroll, null, 1);
            }
            // Recargo Dominical festivo Diurno l
            if ($json->Valor_RDF_2 > 0) {
                $this->functions_payroll->save_data_accrueds(7, $json->Valor_RDF_2, null, null, $json->Cantidad_RDF_2,
                    0.75, 'Recargo Dominical Festivo Diurno', $id_payroll, null, 5);
            }
            // Recargo Dominical festivo Diurno l
            if ($json->Valor_RDF_2 > 0) {
                $this->functions_payroll->save_data_accrueds(7, $json->Cantidad_RDF_2, null, null, $json->Valor_RDF_2,
                    0.75, 'Recargo Dominical Festivo Diurno', $id_payroll, null, 5);
            }
            // Recargo Dominical Festivo Nocturno l
            if ($json->Valor_RNDF_2 > 0) {
                $this->functions_payroll->save_data_accrueds(9, $json->Cantidad_RNDF_2, null, null, $json->Valor_RNDF_2,
                    1.10, 'Recargo Dominical Festivo Nocturno', $id_payroll, null, 7);
            }
            // Recargo  Nocturno l
            if ($json->Valor_RO_2 > 0) {
                $this->functions_payroll->save_data_accrueds(5,$json->Cantidad_RO_2 , null, null, $json->Valor_RO_2,
                    1.10, 'Recargo Nocturno Ordinario', $id_payroll, null, 3);
            }
            // Horas Extras Dominicales festivas Nocturnas l
            if ($json->Valor_HENF_2 > 0) {
                $this->functions_payroll->save_data_accrueds(8, $json->Valor_HENF_2, null, null, $json->Cantidad_HENF_2,
                    1.50, 'Horas Extras Dominicales festivas Nocturnas', $id_payroll, null, 6);
            }
            // Incapacidades
            if ($json->Total_Valor_Incapacidad > 0) {
                $type_disability_id = $this->functions_payroll->eliminar_tildes(strtolower($json->Tipo_de_Incapacidad));

                $this->functions_payroll->save_data_accrueds(17, $json->Total_Valor_Incapacidad, (!empty($json->Fecha_inicio_incapacidad))?$this->functions_payroll->split_date($json->Fecha_inicio_incapacidad):null,
                    (!empty($json->Fecha_fin_incapacidad))?$this->functions_payroll->split_date($json->Fecha_fin_incapacidad):null, $json->No_de_Dias_de_incapacidad, null, 'Incapacidades', $id_payroll,
                    $type_disability_id);
            }
            // Licencias Remuneradas
            if ($json->Valor_LM > 0) {
                $this->functions_payroll->save_data_accrueds(18, $json->Valor_LM, null, null,
                    $json->Cantidad_Dias_LM, null, 'Licencia de Maternidad', $id_payroll);
            }
            // Licencias Remuneradas
            if ($json->Valor_Licencia_remunerada > 0) {
                $this->functions_payroll->save_data_accrueds(19, $json->Valor_Licencia_remunerada, null,null ,
                    $json->Dias_de_LR, null, 'Licencia Remunerada', $id_payroll);
            }
            // Licencias No Remunerada
            if ($json->Dias_Licencia_no_remunerada > 0) {
                $this->functions_payroll->save_data_accrueds(20, 0,null,null,
                    $json->Dias_Licencia_no_remunerada, null, 'Licencia No Remunerada', $id_payroll);
            }
            // Vacaciones Comunes l
            if ($json->Valor_Vacaciones_Comunes > 0) {
                $this->functions_payroll->save_data_accrueds(12, $json->Valor_Vacaciones_Comunes,
                    null, null, $json->Dias_Habiles_de_Vacaciones, null,
                    'Vacaciones', $id_payroll);
            }
            // Vacaciones Compensadas l
            if ($json->Valor_vacaciones_compensadas > 0) {
                $this->functions_payroll->save_data_accrueds(13, $json->Valor_vacaciones_compensadas,
                    null,null, $json->Dias_Habiles_de_Vacaciones_compensadas, null, 'Vacaciones Compensadas',
                    $id_payroll);
            }
            // Bonificacion No salarial
            /*if ($json->Concepto_de_Bono_fuera_de_contrato > 0) {
                $this->functions_payroll->save_data_accrueds(22, $json->Bono_fuera_de_contrato_no_salarial, null, null,
                    '', '', 'Bonificación no Salarial', $id_payroll);
            }*/
            // Auxilios Salariales l
            if ((int)$json->Auxilio_Salarial_1 > 0) {
                $this->functions_payroll->save_data_accrueds(23, (int)$json->Auxilio_1, null, null, null, null,
                    'Auxilio 1', $id_payroll);
            }
            if ((int)$json->Auxilio_Salarial_2 > 0) {
                $this->functions_payroll->save_data_accrueds(23, (int)$json->Auxilio_2, null, null, null, null,
                    'Auxilio 2', $id_payroll);
            }
            // Auxilio No Salarial l
            if ((int)$json->Auxilio_No_Salarial_1 > 0) {
                $this->functions_payroll->save_data_accrueds(24, (int)$json->Auxilio_No_Salarial_1, null, null, null, null,
                    'Auxilio No Salarial 1', $id_payroll);
            }
            if ((int)$json->Auxilio_No_Salarial_2 > 0) {
                $this->functions_payroll->save_data_accrueds(24, (int)$json->Auxilio_No_Salarial_2, null, null, null, null,
                    'Auxilio No Salarial 2', $id_payroll);
            }
            // otros conceptos salariales l
            if ((int)$json->Pago_en_especie_Salarial_1 > 0) {
                $this->functions_payroll->save_data_accrueds(26, (int)$json->Pago_en_especie_Salarial_1, null, null, null, null,
                    'Pago en especie Salarial 1', $id_payroll);
            }
            if ((int)$json->Pago_en_especie_Salarial_2 > 0) {
                $this->functions_payroll->save_data_accrueds(26, (int)$json->Pago_en_especie_Salarial_2, null, null, null, null,
                    'Pago en especie Salarial 2', $id_payroll);
            }
            // otros conceptos no salariales l
            if ((int)$json->Pago_en_especie_No_Salarial_1 > 0) {
                $this->functions_payroll->save_data_accrueds(27, (int)$json->Pago_en_especie_No_Salarial_1, null, null, null, null,
                    'Pago en especie No Salarial 1', $id_payroll);
            }
            if ((int)$json->Pago_en_especie_No_Salarial_2 > 0) {
                $this->functions_payroll->save_data_accrueds(27, (int)$json->Pago_en_especie_No_Salarial_2, null, null, null, null,
                    'Pago en especie No Salarial 2', $id_payroll);
            }

            // bono de alimentacion salarial l
            if ($json->Bono_de_alimentacion_salarial > 0) {
                $this->functions_payroll->save_data_accrueds(32, $json->Bono_de_alimentacion_salarial, null, null, null,
                    null, 'Bono de alimentación Salarial', $id_payroll);
            }
            // bono de alimentacion no salarial l
            if ($json->Bono_de_alimentacion_No_salarial > 0) {
                $this->functions_payroll->save_data_accrueds(33, $json->Bono_de_alimentacion_No_salarial, null, null, null,
                    null, 'Bono de alimentación no Salarial', $id_payroll);
            }
            // bono salarial
            if ($json->Bono_salarial > 0) {
                $this->functions_payroll->save_data_accrueds(21, $json->Bono_salarial, null, null, null,
                    null, 'Bono Salarial', $id_payroll);
            }
            // bono no salarial
            if ($json->Bono_No_salarial > 0) {
                $this->functions_payroll->save_data_accrueds(22, $json->Bono_No_salarial, null, null, null,
                    null, 'Bono no Salarial', $id_payroll);
            }
            // viaticos salariales
            if ($json->Viatico_salarial> 0) {
                $this->functions_payroll->save_data_accrueds(10, $json->Viatico_salarial, null, null, null,
                    null, 'Viaticos Salariales', $id_payroll);
            }
            // viaticos no salariales
            if ($json->Viatico_no_salarial > 0) {
                $this->functions_payroll->save_data_accrueds(11, $json->Viatico_no_salarial, null, null, null,
                    null, 'Viaticos No Salariales', $id_payroll);
            }
            // Comisiones
            if ($json->Comisiones > 0) {
                $this->functions_payroll->save_data_accrueds(34, $json->Comisiones, null, null, null,
                    null, 'Comisiones', $id_payroll);
            }
            // primas
            if ($json->Valor_Prima_ExtralegalSalarial > 0 || $json->Valor_Prima_No_Salarial > 0) {
                $this->functions_payroll->save_data_accrueds(14, $json->Valor_Prima_ExtralegalSalarial, null, null, $json->No_de_dias_de_prima,
                    null, 'Prima', $id_payroll, null, null,$json->Valor_Prima_No_Salarial);
            }
            // cesantias
            if ($json->Valor_de_Cesantias > 0 || $json->Valor_Intereses_de_Cesantias > 0) {
                $this->functions_payroll->save_data_accrueds(16, $json->Valor_de_Cesantias, null, null, null,
                    $json->P_Interes_Cesantias, 'Cesantias', $id_payroll, null, null,$json->Valor_Intereses_de_Cesantias);
            }
            // pago a terceros
            if ($json->Para_pago_a_terceros > 0) {
                $this->functions_payroll->save_data_accrueds(35, $json->Para_pago_a_terceros, null, null, null,
                    null, 'Apoyo sostenimiento', $id_payroll);
            }
            // anticipo
            if ($json->Anticipo > 0) {
                $this->functions_payroll->save_data_accrueds(36, $json->Anticipo, null, null, null,
                    null, 'Apoyo sostenimiento', $id_payroll);
            }
            // dotacion
            if ($json->Dotacion > 0) {
                $this->functions_payroll->save_data_accrueds(37, $json->Dotacion, null, null, null,
                    null, 'Apoyo sostenimiento', $id_payroll);
            }
            // apoyo sostenimiento
            if ($json->Apoyo_sostenimiento > 0) {
                $this->functions_payroll->save_data_accrueds(38, $json->Apoyo_sostenimiento, null, null, null,
                    null, 'Apoyo sostenimiento', $id_payroll);
            }
            // indemnizacion
            if ($json->indemnizacion > 0) {
                $this->functions_payroll->save_data_accrueds(41, $json->indemnizacion, null, null, null,
                    null, 'Indemnización', $id_payroll);
            }
            // bonificacion retiro
            if ($json->Bonificacion_de_retiro > 0) {
                $this->functions_payroll->save_data_accrueds(40, $json->Bonificacion_de_retiro, null, null, null,
                    null, 'Bonificación Retiro', $id_payroll);
            }
            // reintegros
            if ($json->Reembolsos > 0) {
                $this->functions_payroll->save_data_accrueds(42, $json->Reembolsos, null, null, null,
                    null, 'Reintegro', $id_payroll);
            }
        }
    }

    public function deductions($info)
    {
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_payroll = $this->functions_payroll->data_payroll_tyc($json->Numero_de_Identificacion, $data->period_id, $data->load_number, $data->type_document_payroll );
            // eps
            if($json->Aporte_para_Salud > 0){
                $this->functions_payroll->save_data_deductions(1, $json->Aporte_para_Salud, 'Aporte para Salud', $id_payroll,
                    null, 3);
            }
            // pension
            if($json->Aporte_para_Pension){
                $this->functions_payroll->save_data_deductions(2, $json->Aporte_para_Pension, 'Aporte para Pensión', $id_payroll,
                    null, 5);
            }
            //fondo de solidaridad pensional y subfondo de subsistencia pensional
            if($json->Aporte_para_FSP){
                $this->functions_payroll->save_data_deductions(3, $json->Aporte_para_FSP, 'Fondo de Solidaridad Pensional',
                    $id_payroll, $json->P_Aporte_para_FSP,9);
                $this->functions_payroll->save_data_deductions(4, $json->Aporte_Subcuenta_de_subsistencia, 'Subfondo subsistencia Pensional',
                    $id_payroll, $json->P_Subcuenta_de_subsistencia,9);
            }
            // deuda
            if ($json->Descuento_por_prestamos > 0) {
                $this->functions_payroll->save_data_deductions(20, $json->Descuento_por_prestamos, 'Deuda', $id_payroll);
            }
            // libranza
            if ($json->Descuento_por_Libranzas > 0) {
                $this->functions_payroll->save_data_deductions(8, $json->Descuento_por_Libranzas, 'Libranza', $id_payroll);
            }
            // afc
            if ($json->Aportes_a_cuenta_AFC > 0) {
                $this->functions_payroll->save_data_deductions(14, $json->Aportes_a_cuenta_AFC, 'AFC', $id_payroll);
            }
            //pension voluntaria
            if ($json->Aportes_a_pension_voluntaria > 0) {
                $this->functions_payroll->save_data_deductions(12, $json->Aportes_a_pension_voluntaria, 'Pension Voluntaria',
                    $id_payroll);
            }
            // retefuente
            if ($json->Retencion_en_la_fuente > 0) {
                $this->functions_payroll->save_data_deductions(13, $json->Retencion_en_la_fuente, 'Retención en la fuente', $id_payroll);
            }
            // otras deducciones
            if ((int)$json->Otras_deducciones_1 > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->Otras_deducciones_1, 'Otras deducciones 1', $id_payroll);
            }
            // plan complementario
            if ($json->Plan_complementario > 0) {
                $this->functions_payroll->save_data_deductions(17, $json->Plan_complementario, 'Plan complementario', $id_payroll);
            }
            // anticipo
            if ($json->Anticipos_de_nomina > 0) {
                $this->functions_payroll->save_data_deductions(10, $json->Anticipos_de_nomina, 'Anticipo', $id_payroll);
            }
            // sancion publica
            if ($json->Sancion_publica > 0) {
                $this->functions_payroll->save_data_deductions(6, $json->Sancion_publica, 'Sanción Publica', $id_payroll);
            }
            // sancion privada
            if ($json->Sancion_privada > 0) {
                $this->functions_payroll->save_data_deductions(7, $json->Sancion_privada, 'Sanción Privada', $id_payroll);
            }
            // cooperativa
            if ($json->Aportes_a_cooperativa > 0) {
                $this->functions_payroll->save_data_deductions(15, $json->Aportes_a_cooperativa, 'Cooperativa', $id_payroll);
            }
            // embargo
            if ($json->Embargo_Fiscal > 0) {
                $this->functions_payroll->save_data_deductions(16, $json->Embargo_Fiscal, 'Embargos', $id_payroll);
            }
            // educacion
            if ($json->Educacion > 0) {
                $this->functions_payroll->save_data_deductions(18, $json->Educacion, 'Educación', $id_payroll);
            }
            // reintegro
            if ($json->Reembolso___Reintegro > 0) {
                $this->functions_payroll->save_data_deductions(19, $json->Reembolso___Reintegro, 'Reintegro', $id_payroll);
            }
            // sindicato
            if ($json->Aporte_a_Sindicato > 0) {
                $this->functions_payroll->save_data_deductions(5, $json->Aporte_a_Sindicato, 'Sindicato', $id_payroll);
            }
            // pago a terceros
            if ($json->Pago_a_terceros > 0) {
                $this->functions_payroll->save_data_deductions(9, $json->Pago_a_terceros, 'Pago a Terceros', $id_payroll);
            }
        }
    }

}

