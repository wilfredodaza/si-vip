<?php


namespace App\Controllers\companies;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Database\Migrations\MunicipalitiesTable;
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

class Payroll_am extends BaseController
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
            $metodo_de_pago = null;
            $municipio = null;
            $contrato = null;
            $json = json_decode($data->data);
            $nombrecompleto = explode( ' ', $json->NOMBRE_COMPLETO);
            //echo json_encode($nombrecompleto[2]);die();

            $type_worker = $this->type_worker->like('name', $json->TIPO_TRABAJADOR, 'both')->asObject()->first();
            $sub_type_worker = $this->sub_type_worker->like('name', $json->subtipo_de_trabajo, 'both')->asObject()->first();
            $type_document = null;
            switch (strtolower($json->TIPO_DOCUMENTO)){
                case 'cc':
                    $type_document = 3;
                    break;
                case 'permiso':
                    $type_document = 11;
                    break;
            }
            switch (strtolower($this->functions_payroll->eliminar_tildes($json->MUNICIPIO))){
                case 'bogota':
                    $municipio = 149;
                    break;
            }
            switch (strtolower($this->functions_payroll->eliminar_tildes($json->TIPO_CONTRATO))){
                case 'obra labor':
                    $contrato = 3;
                    break;
                case 'indefinido':
                    $contrato = 2;
                    break;
                case 'fijo':
                    $contrato = 1;
                    break;
                case 'aprendizaje':
                    $contrato = 4;
                    break;
                case 'practicas':
                    $contrato = 5;
                    break;
            }
            if($json->METODO_DE_PAGO == 'TRANSFERENCIA'){
                $metodo_de_pago = 47;
            }
            if(in_array($metodo_de_pago, $this->code_method_payment)){
                $bank = $this->functions_payroll->banks($json->BANCO);
                if(strtolower($json->TIPO_CUENTA) == 'ahorros'){
                    $bank_account_type = 1;
                }
                if(strtolower($json->TIPO_CUENTA) == 'corriente'){
                    $bank_account_type = 2;
                }
            }

            $worker = $this->functions_payroll->worker_data($json->Identificacion);
            if (is_null($worker)) {
                // json datos del trabajador para realizar el guardado
                $data_customer = [
                    'name' => ($nombrecompleto[2] ?? $nombrecompleto[1]),
                    'type_document_identifications_id' => $type_document,//listo
                    'identification_number' => $json->Identificacion, // LISTO
                    'address' => $json->DIRECCION,
                    'email' => ($json->CORREO ?? ''),
                    'type_customer_id' => 3,
                    'municipality_id' => $municipio,
                    'companies_id' => Auth::querys()->companies_id,
                ];
                $data_customer_worker = [
                    'type_worker_id' => $type_worker->id,
                    'sub_type_worker_id' => $sub_type_worker->id,
                    'bank_id' => $bank,
                    'bank_account_type_id' => $bank_account_type,
                    'type_contract_id' => $contrato,
                    'payment_method_id' => $metodo_de_pago,
                    'account_number' => $json->NUMERO_DE_CUENTA,
                    'second_name' => ($nombrecompleto[3] ?? ''),
                    'surname' => $nombrecompleto[0],
                    'second_surname' => $nombrecompleto[1],
                    'high_risk_pension' => (strtolower($json->PENSION_DE_ALTO_RIESGO) == 'falso') ? 'false' : 'true',
                    'integral_salary' => (strtolower($json->SALARIO_INTEGRAL) == 'falso') ? 'false' : 'true',
                    'salary' => $json->SUELDO,
                    'admision_date' => $this->functions_payroll->split_date($json->FECHA_DE_ADMISION),
                    'retirement_date' => ($this->functions_payroll->split_date($json->FECHA_RETIRO) ?? null),
                    'payroll_period_id' => $data->payroll_period,
                    'worker_code' => $json->Identificacion,
                    //'work' => $json->Cargo
                ];
                $customer_id = $this->functions_payroll->add_customer($data_customer, $data_customer_worker);
                if (isset($json->CORREO) && $json->CORREO != '') {
                    $data_user = [
                        'name' => ($nombrecompleto[2] ?? $nombrecompleto[1]),
                        'username' => $json->CORREO,
                        'email' => $json->CORREO,
                        'password' => password_hash($json->Identificacion, PASSWORD_DEFAULT), // LISTO
                        'status' => 'active',
                        'role_id' => 7,
                        'companies_id' => Auth::querys()->companies_id
                    ];
                    $this->functions_payroll->add_user($data_user, $customer_id);
                }
            } else {
                //valido si trabajador tiene correo
                if (isset($json->CORREO) && $json->CORREO != '') {
                    // valida si el trabajador tiene usuario en el sistema
                    if (empty($worker->user_id) || is_null($worker->user_id)) {
                        $data_user = [
                            'name' => ($nombrecompleto[2] ?? $nombrecompleto[1]),
                            'username' => $json->CORREO,
                            'email' => $json->CORREO,
                            'password' => password_hash($json->Identificacion, PASSWORD_DEFAULT), // LISTO
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
                    'type_contract_id' => $contrato,
                    'bank' => $bank, // listo
                    'bank_account_type' => $bank_account_type,
                    'payment_method_id' => $metodo_de_pago,
                    'account_number' => $json->NUMERO_DE_CUENTA,
                    'retirement_date' => (isset($json->FECHA_RETIRO) && $json->FECHA_RETIRO != '0000-00-00' && $json->FECHA_RETIRO != '-   -') ?
                        $this->functions_payroll->split_date($json->FECHA_RETIRO) : null
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
            $id_customer = $this->functions_payroll->customer_id($json->Identificacion);
            if ($data->type_document_payroll == 10) {
                try {
                    $ajuste = $this->invoices
                        ->join('customers', 'customers.id = invoices.customers_id')
                        ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                        ->where(['invoices.companies_id' => company()->id,
                            'invoices.invoice_status_id' => 14,
                            'invoices.type_documents_id' => 9,
                            'customers.identification_number' => $json->Identificacion,
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
                'settlement_start_date' => $this->functions_payroll->split_date($json->FECHA_DE_INICIO_DE_LA_LIQUIDACION), // listo
                'settlement_end_date' => $this->functions_payroll->split_date($json->FECHA_FIN_DE_LA_LIQUIDACION), // LISTO
                'worked_time' => $json->Dias, //listo
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
            $tipo_incapacidad = 1;
            $json = json_decode($data->data);
            $id_payroll = $this->functions_payroll->data_payroll($json->Identificacion, $data->period_id, $data->type_document_payroll);
            // Salario LISTO
            $this->functions_payroll->save_data_accrueds(1, $json->SUELDO, null, null, null, null,
                'Salario', $id_payroll);
            //Auxilio de transporte LISTO
            if ($json->AUXILIO_DE_TRANSPORTE > 0) {
                $this->functions_payroll->save_data_accrueds(2, $json->AUXILIO_DE_TRANSPORTE, null, null, null,
                    null, 'Auxilio de transporte', $id_payroll);
            }
            // Vacaciones Comunes listo
            if ($json->VACACIONES_COMUNES_VALOR > 0) {
                $this->functions_payroll->save_data_accrueds(12, $json->VACACIONES_COMUNES_VALOR,
                    (empty($json->VACACIONES_COMUNES_FECHA_INICIO)?null:$json->VACACIONES_COMUNES_FECHA_INICIO),
                    (empty($json->VACACIONES_COMUNES_FECHA_FINAL)?null:$json->VACACIONES_COMUNES_FECHA_FINAL),
                    $json->VACACIONES_COMUNES_CANTIDAD, null, 'Vacaciones', $id_payroll);
            }
            // primas listo
            if ($json->PRIMAS_VALOR > 0) {
                $this->functions_payroll->save_data_accrueds(14, $json->PRIMAS_VALOR, null, null, $json->PRIMAS_CANTIDAD,
                    null, 'Prima', $id_payroll, null, null);
            }
            // cesantias listo
            if ($json->CESANTIAS_VALOR > 0) {
                $this->functions_payroll->save_data_accrueds(16, $json->CESANTIAS_VALOR, null, null, null,
                    $json->CESANTIAS_PORCENTAJE, 'Cesantias', $id_payroll, null, null,$json->VALOR_INTERESES);
            }
            // Incapacidades LISTO
            if ($json->VALOR_INCAPACIDAD > 0) {

                switch (strtolower($this->functions_payroll->eliminar_tildes($json->TIPO_DE_INCAPACIDAD))){
                    case 'comun':
                        $tipo_incapacidad = 1;
                        break;
                    case 'profesional':
                        $tipo_incapacidad = 2;
                        break;
                    case 'laboral':
                        $tipo_incapacidad = 3;
                        break;
                }
                $this->functions_payroll->save_data_accrueds(17, $json->VALOR_INCAPACIDAD, null,
                    null, $json->DIAS_INCAPACIDAD, null, 'Incapacidades', $id_payroll,
                    $tipo_incapacidad);
            }
            // Licencias Remuneradas LISTO
            if ($json->LICENCIAS_REMUNERADAS_VALOR > 0) {
                $this->functions_payroll->save_data_accrueds(19, $json->LICENCIAS_REMUNERADAS_VALOR, null, null,
                    $json->LICENCIAS_REMUNERADAS_CANTIDAD, null, 'Licencia Remunerada', $id_payroll);
            }
            // Licencias No Remunerada LISTO
            if ($json->LICENCIA_NO_RENUMERADA_CANTIDAD > 0) {
                $this->functions_payroll->save_data_accrueds(20, 0, null, null,
                    $json->LICENCIA_NO_RENUMERADA_CANTIDAD, null, 'Licencia No Remunerada', $id_payroll);
            }
            // otros conceptos no salariales LISTO
            if ((int)$json->OTROS_CONCEPTOS_SALARIALES > 0) {
                $this->functions_payroll->save_data_accrueds(26, (int)$json->OTROS_CONCEPTOS_SALARIALES, null, null, null, null,
                    'OTROS CONCEPTOS SALARIALES', $id_payroll);
            }
        }
    }

    public function deductions($info)
    {
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_payroll = $this->functions_payroll->data_payroll($json->Identificacion, $data->period_id, $data->type_document_payroll);
            // eps
            if($json->APORTE_SALUD > 0){
                $this->functions_payroll->save_data_deductions(1, $json->APORTE_SALUD, 'Aporte para Salud', $id_payroll,
                    null, 3);
            }
            // pension listo
            if($json->APORTE_PENSION){
                $this->functions_payroll->save_data_deductions(2, $json->APORTE_PENSION, 'Aporte para Pensión', $id_payroll,
                    null, 5);
            }
            // anticipo
            if ((int)$json->ANTICIPO_SALARIAL > 0) {
                $this->functions_payroll->save_data_deductions(10, (int)$json->ANTICIPO_SALARIAL, 'ANTICIPO SALARIAL', $id_payroll);
            }
            // libranza
            if ((int)$json->LIBRANZA > 0) {
                $this->functions_payroll->save_data_deductions(8, (int)$json->LIBRANZA, 'LIBRANZA', $id_payroll);
            }
            // otras deducciones
            if ((int)$json->PRESTAMO_COLSUBSIDIO > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->PRESTAMO_COLSUBSIDIO, 'PRESTAMO COLSUBSIDIO', $id_payroll);
            }
            if ((int)$json->PERDIDA_EN_CLIENTES > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->PERDIDA_EN_CLIENTES, 'PERDIDA EN CLIENTES', $id_payroll);
            }
            if ((int)$json->CAPACITACION > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->CAPACITACION, 'CAPACITACION', $id_payroll);
            }
            if ((int)$json->ROPA_Y_JUGUETES > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->ROPA_Y_JUGUETES, 'ROPA Y JUGUETES', $id_payroll);
            }
            if ((int)$json->VALORACIONES_MEDICAS > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->VALORACIONES_MEDICAS, 'VALORACIONES MEDICAS', $id_payroll);
            }
            if ((int)$json->SERVICIO_LAVANDERIA > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->SERVICIO_LAVANDERIA, 'SERVICIO LAVANDERIA', $id_payroll);
            }
            if ((int)$json->SEGURO_FUNERARIO > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->SEGURO_FUNERARIO, 'SEGURO FUNERARIO', $id_payroll);
            }
            if ((int)$json->SERCREDITO > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->SERCREDITO, 'SERCREDITO', $id_payroll);
            }
            if ((int)$json->PRESTAMO_EMPLEADO > 0) {
                $this->functions_payroll->save_data_deductions(11, (int)$json->PRESTAMO_EMPRESA, 'PRESTAMO EMPLEADO', $id_payroll);
            }
        }
    }

}
