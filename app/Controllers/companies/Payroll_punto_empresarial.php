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
use App\Models\SubTypeWorker;
use App\Models\TypeAccountBank;
use App\Models\TypeContract;
use App\Models\TypeDocumentIdentifications;
use App\Models\TypeWorker;

class Payroll_punto_empresarial extends BaseController
{
    protected $cargue;
    protected $workers;
    protected $payroll;
    protected $accrueds;
    protected $customers;
    protected $invoices;
    protected $other_concepts;
    // workers
    protected $type_worker;
    protected $banks;
    protected $bank_account_types;
    protected $payrollDate;
    protected $sub_type_worker;
    protected $type_contract;
    protected $type_document;
    //functions
    protected $functions_payroll;
    protected $info;
    protected $code_method_payment;

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
        $this->sub_type_worker = new SubTypeWorker();
        $this->type_contract =  new TypeContract();
        $this->type_document = new TypeDocumentIdentifications();
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
            $json = json_decode($data->data);

            $type_worker = $this->type_worker->like('code', $json->TIPO_DE_EMPLEADO, 'both')->get()->getResult()[0];
            $sub_type_worker = $this->sub_type_worker->like('code', $json->SUBTIPO_DE_TRABAJADOR, 'both')->get()->getResult()[0];
            $type_contract = $this->type_contract->where(['id' => $json->TIPO_DE_CONTRATO])->get()->getResult()[0];
            $type_document = $this->type_document->where(['code' => $json->TIPO_DE_DOCUMENTO])->get()->getResult()[0];
            if(in_array($json->METODO_DE_PAGO, $this->code_method_payment)){
                $bank = $this->functions_payroll->banks($json->BANCO);
                if(strtolower($json->TIPO_DE_CUENTA) == 'ahorros'){
                    $bank_account_type = 1;
                }
            }


            $worker = $this->functions_payroll->worker_data($json->NUMERO_DE_IDENTIFICACION);
            if (is_null($worker)) {
                // json datos del trabajador para realizar el guardado
                $data_customer = [
                    'name' => $json->PRIMER_NOMBRE, // Listo
                    'type_document_identifications_id' => $type_document->id,
                    'identification_number' => $json->NUMERO_DE_IDENTIFICACION,
                    'address' => $json->DIRECCION,
                    'email' => ($json->Correo_electronico_laboral ?? ''),
                    'type_customer_id' => 3,
                    'municipality_id' => $json->MUNICIPIO,
                    'companies_id' => Auth::querys()->companies_id,
                ];
                $data_customer_worker = [
                    'type_worker_id' => $type_worker->id,
                    'sub_type_worker_id' => $sub_type_worker->id,
                    'bank_id' => $bank,
                    'bank_account_type_id' => $bank_account_type,
                    'type_contract_id' => $type_contract->id,
                    'payment_method_id' => $json->METODO_DE_PAGO,
                    'account_number' => $json->NUMERO_DE_CUENTA,
                    'second_name' => $json->SEGUNDO_NOMBRE,
                    'surname' => $json->PRIMER_APELLIDO,
                    'second_surname' => $json->SEGUNDO_APELLIDO,
                    'high_risk_pension' => (strtolower($json->PENSION_DE_ALTO_RIESGO) == 'no') ? 'false' : 'true',
                    'integral_salary' => false,
                    'salary' => $json->SUELDO,
                    'admision_date' => $this->functions_payroll->split_date($json->FECHA_DE_ADMISION),
                    'retirement_date' => ($this->functions_payroll->split_date($json->FECHA_DE_RETIRO) ?? null),
                    'payroll_period_id' => $data->payroll_period,
                    'worker_code' => $json->NUMERO_DE_IDENTIFICACION
                ];
                $customer_id = $this->functions_payroll->add_customer($data_customer, $data_customer_worker);
                if (isset($json->Correo_electronico_laboral) && $json->Correo_electronico_laboral != '') {
                    $data_user = [
                        'name' => $json->PRIMER_NOMBRE, //  LISTO
                        'username' => $json->PRIMER_NOMBRE, // LISTO
                        'email' => $json->Correo_electronico_laboral,
                        'password' => password_hash($json->NUMERO_DE_IDENTIFICACION, PASSWORD_DEFAULT),
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
                            'name' => $json->PRIMER_NOMBRE, // lISTO
                            'username' => $json->PRIMER_NOMBRE, // lISTO
                            'email' => $json->Correo_electronico_laboral,
                            'password' => password_hash($json->NUMERO_DE_IDENTIFICACION, PASSWORD_DEFAULT),
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
                    'type_contract_id' => $type_contract->id, // listo
                    'bank' => $bank, // listo
                    'bank_account_type' => $bank_account_type, // listo
                    'payment_method_id' => $json->METODO_DE_PAGO, // listo
                    'account_number' => $json->NUMERO_DE_CUENTA, // listo
                    'retirement_date' => (isset($json->FECHA_DE_RETIRO) && $json->FECHA_DE_RETIRO != '0000-00-00' && $json->FECHA_DE_RETIRO != '-   -') ? $this->functions_payroll->split_date($json->FECHA_DE_RETIRO) : null
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
            $id_customer = $this->functions_payroll->customer_id($json->NUMERO_DE_IDENTIFICACION);
            if ($data->type_document_payroll == 10) {
                try {
                    $ajuste = $this->invoices
                        ->join('customers', 'customers.id = invoices.customers_id')
                        ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                        ->where(['invoices.companies_id' => company()->id,
                            'invoices.invoice_status_id' => 14,
                            'invoices.type_documents_id' => 9,
                            'customers.identification_number' => $json->NUMERO_DE_IDENTIFICACION,
                            'payrolls.period_id' => $data->period_id])
                        ->get()->getResult()[0];
                } catch (\Exception $e) {
                    //return redirect()->to(base_url() . '/import/payroll')->with('errors', $e->getMessage());
                }
            }
            $id_invoice = $this->invoices->insert([
                'resolution_id' => null,
                'prefix' => null,
                'customers_id' => $id_customer,
                'invoice_status_id' => 13,
                'notes' => '',
                'type_documents_id' => $data->type_document_payroll,
                'companies_id' => Auth::querys()->companies_id,
                'uuid' => ($ajuste->uuid ?? null),
                'resolution_credit' => ($ajuste->resolution ?? null),
                'issue_date' => ($ajuste->created_at ?? null)
            ]);
            $this->payroll->insert([
                'settlement_start_date' => $this->functions_payroll->split_date($json->FECHA_DE_INICIO_DE_LIQUIDACION), // listo
                'settlement_end_date' => $this->functions_payroll->split_date($json->FECHA_DE_FINAL_DE_LIQUIDACION), // LISTO
                'worked_time' => $json->DIAS_TRABAJADOS, //listo
                'invoice_id' => $id_invoice,
                'period_id' => $data->period_id,
                'sub_period_id' => ($data->type_document_payroll == 10) ? $data->load_number : null,
                'type_payroll_adjust_note_id' => ($data->type_document_payroll == 10) ? 1 : null
            ]);
            $json_dates = json_decode($data->payment_dates);
            $dates_payment = explode(',',$json_dates[0]);
            foreach ($dates_payment as $json_date) {
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
            $id_payroll = $this->functions_payroll->data_payroll($json->NUMERO_DE_IDENTIFICACION, $data->period_id, $data->type_document_payroll);
            // Salario
            $this->functions_payroll->save_data_accrueds(1, $json->SUELDO, null, null, null, null,
                'Salario', $id_payroll);
            //Auxilio de transporte
            if ($json->AUXILIO_DE_TRANSPORTE > 0) {
                $this->functions_payroll->save_data_accrueds(2, $json->AUXILIO_DE_TRANSPORTE, null, null, null,
                    null, 'Auxilio de transporte', $id_payroll);
            }
            // bono de alimentacion no salarial
            if ($json->BONO_DE_ALIMENTACION_NO_SALARIAL > 0) {
                $this->functions_payroll->save_data_accrueds(33, $json->BONO_DE_ALIMENTACION_NO_SALARIAL, null, null, null,
                    null, 'Bono de alimentación no Salarial', $id_payroll);
            }
            // bono salarial
            if ($json->BONO_SALARIAL > 0) {
                $this->functions_payroll->save_data_accrueds(30, $json->BONO_SALARIAL, null, null, null,
                    null, 'Bono de alimentación Salarial', $id_payroll);
            }
            // Vacaciones Comunes
            if ($json->VACACIONES_COMUNES > 0) {
                $this->functions_payroll->save_data_accrueds(12, $json->VACACIONES_COMUNES, null,
                    null, $json->DIAS_VACACIONES_COMUNES, null,
                    'Vacaciones', $id_payroll);
            }
            // Vacaciones compensadas
            if ($json->VACACIONES_COMPENSADAS > 0) {
                $this->functions_payroll->save_data_accrueds(13, $json->VACACIONES_COMPENSADAS,
                    null,null, $json->DIAS_VACACIONES_COMPENSADAS, null, 'Vacaciones Compensadas',
                    $id_payroll);
            }
            // primas
            if ($json->PRIMAS > 0) {
                $this->functions_payroll->save_data_accrueds(14, $json->PRIMAS, null, null, $json->DIAS_PRIMA,
                    null, 'Prima', $id_payroll, null, null,$json->VALOR_PRIMA_NO_SALARIAL);
            }
            // cesantias
            if ($json->CESANTIAS_V_R > 0) {
                $this->functions_payroll->save_data_accrueds(16, $json->CESANTIAS_V_R, null, null, null,
                    $json->PORCENTAJE_CESANTIAS, 'Cesantias', $id_payroll, null, null,$json->INTERESES_CESANTIAS);
            }
            // Incapacidades
            if ($json->INCAPACIDADES > 0) {
                $this->functions_payroll->save_data_accrueds(17, $json->INCAPACIDADES, null,
                    null, $json->DIAS_INCAPACIDAD, null, 'Incapacidades', $id_payroll,
                    $json->TIPO_INCAPACIDAD);
            }
            // Licencias maternidad
            if ($json->LICENCIA_DE_MATERNIDAD_V_R > 0) {
                $this->functions_payroll->save_data_accrueds(18, $json->LICENCIA_DE_MATERNIDAD_V_R, null, null,
                    $json->LICENCIA_DE_MATERNIDAD_DIAS, null, 'Licencia de Maternidad', $id_payroll);
            }
            // Licencias Remuneradas
            if ($json->LICENCIA_REMUNERADA_V_R > 0) {
                $this->functions_payroll->save_data_accrueds(19, $json->LICENCIA_REMUNERADA_V_R, null, null,
                    $json->LICENCIA_REMUNERADA_DIAS , null, 'Licencia Remunerada', $id_payroll);
            }
            // Licencias No Remunerada
            if ($json->LICENCIA_NO_REMIUNERADA_DIAS > 0) {
                $this->functions_payroll->save_data_accrueds(20, 0, null, null,
                    $json->LICENCIA_NO_REMIUNERADA_DIAS, null, 'Licencia No Remunerada', $id_payroll);
            }
            // otros conceptos no salariales
            if ((int)$json->OTRO_DEVENGADO_NO_SALARIAL > 0) {
                $this->functions_payroll->save_data_accrueds(27, (int)$json->OTRO_DEVENGADO_NO_SALARIAL, null, null, null, null,
                    'Otro devengado no salarial', $id_payroll);
            }
        }
    }

    public function deductions($info)
    {
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_payroll = $this->functions_payroll->data_payroll($json->NUMERO_DE_IDENTIFICACION, $data->period_id, $data->type_document_payroll);
            // eps
            if($json->EPS > 0){
                $this->functions_payroll->save_data_deductions(1, $json->EPS, 'Aporte para Salud', $id_payroll,
                    null, 3);
            }
            // pension
            if($json->PENSION){
                $this->functions_payroll->save_data_deductions(2, $json->PENSION, 'Aporte para Pensión', $id_payroll,
                    null, 5);
            }
            // otras deducciones
            if ((int)$json->OTRAS_DEDUCCIONES > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->OTRAS_DEDUCCIONES, 'Otras deducciones', $id_payroll);
            }

        }
    }

}

