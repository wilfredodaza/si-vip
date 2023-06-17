<?php


namespace App\Controllers;

use App\Controllers\Api\Auth;
use App\Controllers\Api\BankAccountType;
use App\Controllers\Api\TypeWorker;
use App\Models\Bank;
use App\Models\Cargue;
use App\Models\Customer;
use App\Models\CustomerWorker;
use App\Models\TypeAccountBank;
use App\Models\User;


class DataWorkersController extends BaseController
{
    public $cargue;
    public $banks;
    public $bank_account_types;
    public $user;
    public $customer;
    public $customer_worker;
    public $invoice_Payroll;
    public $type_worker;
    public $payrrol_controller;


    public function __construct()
    {
        $this->cargue = new Cargue();
        $this->banks = new Bank();
        $this->user = new User();
        $this->customer = new Customer();
        $this->customer_worker = new CustomerWorker();
        $this->type_worker = new \App\Models\TypeWorker();
        $this->bank_account_types = new TypeAccountBank();
        $this->invoice_Payroll = new DataInvoicesController();
        $this->payrrol_controller = new ImportPayrollController();


    }

    public function data_workers($nit, $period, $month_payroll)
    {
        $info = $this->new_data($nit, $period, $month_payroll);
        if(company()->identification_number == 900782726){
            $data = $this->Asnomina($info);
        }elseif(company()->identification_number == 900444608){
            $data = $this->tyc_nomina($info);
        }
        $this->update_status_upload($info);
        return $data;
    }

    private function Asnomina($info)
    {
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
            $bank = $this->banks($json->Nombre_banco);
            // se realiza transformacion de subtipo de trabajador
            if ($json->Subtipo_trabajador == 0) {
                $subtype_worker = 1;
            }
            if (!in_array($json->Numero_de_identificacion, $empleados)) {
                //valido si el trabajador existe
                $worker = $this->worker_data($json->Numero_de_identificacion);
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
                        'admision_date' => $this->invoice_Payroll->split_date($json->Fecha_ingreso),
                        'retirement_date' => (isset($json->fecha_retiro) && $json->fecha_retiro != '0000-00-00' && $json->fecha_retiro != '-   -') ? $this->invoice_Payroll->split_date($json->fecha_retiro) : null,
                        'payroll_period_id' => $data->payroll_period
                    ];
                    $customer_id = $this->add_customer($data_customer, $data_customer_worker);
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
                        $this->add_user($data_user, $customer_id);
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
                            $this->add_user($data_user, $worker);
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
                    $this->update_customer($worker, $data_update);
                }
                array_push($empleados, $json->Numero_de_identificacion);
            }
        }
        $data_invoice_payroll = $this->invoice_Payroll->Asnomina($info);
    }

    private function tyc_nomina($info)
    {
        foreach ($info as $data){
            $json = json_decode($data->data);
            $Ti = utf8_encode($json->Tipo_de_identificacion);
            if($this->payrrol_controller->eliminar_tildes(utf8_decode($Ti)) == "Cedula de ciudadania"){
                $documento = 11;
            }
            $type_worker = $this->type_worker->where(['name' => $json->Tipo_de_trabajador])->get()->getResult()[0];
            $bank = $this->banks->where(['code' => $json->Codigo_del_banco])->get()->getResult()[0];
            $bank_account_type = $this->bank_account_types->where(['code' => $json->Tipo_de_producto])->get()->getResult()[0];
            if($json->Subtipo_de_trabajador == 'No aplica'){
                $subtype_worker = 1;
            }
            if($json->Tipo_de_contrato == 'Termino Indefinido'){
                $type_contract_id = 1;
            }elseif($json->Tipo_de_contrato == 'Termino fijo'){
                $type_contract_id =  2;
            }elseif ($json->Tipo_de_contrato == 'Obra o labor'){
                $type_contract_id = 3;
            }elseif ($json->Tipo_de_contrato == 'Practicante en etapa lectiva'){
                $type_contract_id =  4;
            }elseif($json->Tipo_de_contrato == 'Practicante universitario' || $json->Tipo_de_contrato == 'Practicante en etapa productiva'){
                $type_contract_id = 5;
            }
            $worker = $this->worker_data($json->Numero_de_Identificacion);
            if (count($worker) < 1) {
                // json datos del trabajador para realizar el guardado
                $data_customer = [
                    'name' => $json->Primer_Nombre,
                    'type_document_identifications_id' => $documento,
                    'identification_number' => $json->Numero_de_Identificacion,
                    'address' => $json->Direccion,
                    'email' => ($json->Correo_electronico_laboral ?? ''),
                    'type_customer_id' => 3,
                    'municipality_id' => $json->Municipio,
                    'companies_id' => Auth::querys()->companies_id, //cambiar por empresa
                ];
                $data_customer_worker = [
                    'type_worker_id' => $type_worker->id,
                    'sub_type_worker_id' => $subtype_worker,
                    'bank_id' => $bank->id,
                    'bank_account_type_id' => $bank_account_type->id,
                    'type_contract_id' => $type_contract_id,
                    'payment_method_id' => 47,
                    'account_number' => $json->No_Cta_de_Destino,
                    'surname' => $json->Primer_Apellido,
                    'second_surname' => $json->Segundo_Apellido,
                    'high_risk_pension' => ($json->Pension_de_alto_riesgo == 'No aplica') ? 'false': 'true',
                    'integral_salary' => ($json->Tipo_de_remuneracion == 'Salario Integral') ? 'true' : 'false',
                    'salary' => $json->Salario,
                    'admision_date' => $json->Fecha_Ingreso,
                    'retirement_date' => ($json->Fecha_retiros ?? null),
                    'payroll_period_id' => $data->payroll_period
                ];
                $customer_id = $this->add_customer($data_customer, $data_customer_worker);
                if (isset($json->Correo_electronico_laboral)) {
                    $data_user = [
                        'name' => $json->Primer_Nombre,
                        'username' => $json->Primer_Nombre,
                        'email' => $json->Correo_electronico_laboral,
                        'password' => password_hash($json->Numero_de_Identificacion, PASSWORD_DEFAULT),
                        'status' => 'active',
                        'role_id' => 7,
                        'companies_id' => Auth::querys()->companies_id
                    ];
                    $this->add_user($data_user, $customer_id);
                }
            } else {
                //valido si trabajador tiene correo
                if (isset($json->Correo_electronico_laboral)) {
                    // valida si el trabajador tiene usuario en el sistema
                    if (empty($worker->user_id) || $worker->user_id == null) {
                        $data_user = [
                            'name' => $json->Primer_Nombre,
                            'username' => $json->Primer_Nombre,
                            'email' => $json->Correo_electronico_laboral,
                            'password' => password_hash($json->Numero_de_Identificacion, PASSWORD_DEFAULT),
                            'status' => 'active',
                            'role_id' => 7,
                            'companies_id' => Auth::querys()->companies_id
                        ];
                        // crea el usuario y actualiza el user id en la tabla customer
                        $this->add_user($data_user, $worker);
                    }
                }
                // valido actualizaciones
                $data_update = [
                    'type_contract_id' => $type_contract_id,
                    'bank' => $bank->id,
                    'bank_account_type' => $bank_account_type->id,
                    'payment_method_id' => 47,
                    'account_number' => $json->No_Cta_de_Destino,
                ];
                $this->update_customer($worker, $data_update);
            }
        }
        $data_invoice_payroll = $this->invoice_Payroll->tycnomina($info);
    }

    private function new_data($nit, $period, $month_payroll)
    {
        $info = $this->cargue->where(['nit' => $nit, 'month_payroll' => $month_payroll, 'payroll_period' => $period, 'status' => 'Inactive'])->get()->getResult();
        return $info;
    }

    public function worker_data($identification)
    {
        $worker = $this->customer->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->select('*,customers.id as id_customers, customer_worker.id as id_customer_worker')
            ->where(['customers.identification_number' => $identification])->get()->getResult();
        return $worker;
    }

    private function add_customer($data_customer, $data_customer_worker)
    {
        $data_customer_worker['customer_id'] = $this->customer->insert($data_customer);
        $this->customer_worker->save($data_customer_worker);

    }

    private function add_user($data, $worker)
    {
        $id = $this->user->insert($data);
        if (isset($worker[0]->id)) {
            $this->customer->set(['user_id' => $id])->where(['id' => $worker[0]->id])->update();
        } else {
            $this->customer->set(['user_id' => $id])->where(['id' => $worker])->update();
        }

    }

    private function update_customer($worker, $data)
    {
        if ($data['type_contract_id'] != $worker[0]->type_contract_id) {
            $update['type_contract_id'] = $data['type_contract_id'];
        }
        if ($data['bank'] != $worker[0]->bank_id) {
            $update['bank_id'] = $data['bank'];
        }
        if ($data['bank_account_type'] != $worker[0]->bank_account_type_id) {
            $update['bank_account_type_id'] = $data['bank_account_type'];
        }
        if ($data['payment_method_id'] != $worker[0]->payment_method_id) {
            $update['payment_method_id'] = $data['payment_method_id'];
        }
        if ($data['account_number'] != $worker[0]->account_number) {
            $update['account_number'] = $data['account_number'];
        }
        if (isset($update)) {
            $this->customer_worker->set($update)->where(['id' => $worker[0]->id_customer_worker])->update();
        }
    }

    private function update_status_upload($info)
    {
        foreach ($info as $data) {
            $this->cargue->set(['status' => 'Active'])->where(['id' => $data->id])->update();
        }
    }

    private function banks($bank)
    {
        switch ($bank) {
            case 'Banco Caja Social':
                $id = 7;
                break;
            case 'AV VILLAS':
                $id = 2;
                break;
            case 'BANCOLOMBIA':
                $id = 3;
                break;
        }

        return $id;
    }

}
