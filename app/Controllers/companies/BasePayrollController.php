<?php


namespace App\Controllers\companies;

use App\Controllers\Api\Auth;
use App\Controllers\Api\Municipality;
use App\Controllers\BaseController;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Invoice;
use App\Models\Municipalities;
use App\Models\Payroll;
use App\Models\PayrollDate;


class BasePayrollController extends BaseController
{
    public $functions_payroll;
    public $invoices;
    public $payroll;
    public $payrollDate;
    public $accrued;
    public $deduction;
    public $municipalities;
    public $code_method_payment;

    public function __construct()
    {
        $this->functions_payroll = new Functions_Payroll();
        $this->invoices = new Invoice();
        $this->payroll = new Payroll();
        $this->payrollDate = new PayrollDate();
        $this->accrued = new Accrued();
        $this->deduction = new Deduction();
        $this->municipalities = new Municipalities();
        $this->code_method_payment = [2, 3, 4, 5, 6, 7, 21, 22, 30, 31, 42, 45, 46, 47];
    }

    public function base_workers($info)
    {
        $empleados = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $bank = '';
            $type_contract_id = 0;
            $bank_account_type = 0;
            $payroll_period = 0;
            $documento = 0;
            $metodo_pago = 0;
            // se realiza transformacion de datos para Documento de Identificación

            switch ($json->Tipo_de_documento) {
                case 1:
                    $documento = 3; // cedula
                    break;
                case 2:
                    $documento = 2; // tarjeta identidad
                    break;
                case 3:
                    $documento = 5; // cedula extrajera
                    break;
                case 4:
                    $documento = 6; // nit
                    break;
                case 5:
                    $documento = 7; // pasaporte
                    break;
                case 6:
                    $documento = 11; // pep
                    break;
            }
            // se realiza transformacion de datos para Tipo de contrato
            if ($json->Tipo_de_contrato == 1) {
                $type_contract_id = 2; // Contrato indefinido
            } elseif ($json->Tipo_de_contrato == 2) {
                $type_contract_id = 1; // fijo
            } elseif ($json->Tipo_de_contrato == 3) {
                $type_contract_id = 3; // obra labor
            }
            // se realiza transformacion de datos para periodo de nomina
            if ($json->periodo_de_nomina == 2) {
                $payroll_period = 4; // quincenal
            }
            if ($json->periodo_de_nomina == 3) {
                $payroll_period = 5; // mensual
            }
            // se realiza transformacion de datos para Tipo de cuenta bancaria
            if ($json->Tipo_de_cuenta == 2) {
                $bank_account_type = 1; //Ahorros
            }
            if ($json->Tipo_de_cuenta == 1) {
                $bank_account_type = 2; //corriente
            }
            // se realiza transformacion de bancos
            if (!empty($json->Nombre_banco) && $json->Nombre_banco != 'CHEQUE' && $json->Nombre_banco != 'EFECTIVO' && strtolower($this->functions_payroll->eliminar_tildes($json->Nombre_banco)) != 'pagos efectivo') {
                $bank = $this->functions_payroll->banks($json->Nombre_banco);
            }
            // niver
            if ($json->Nombre_banco == 'CHEQUE') {
                $metodo_pago = 20;
            }
            // grancolservig e ingemol
            if ($json->Nombre_banco == 'EFECTIVO' || strtolower($this->functions_payroll->eliminar_tildes($json->Nombre_banco)) == 'pagos efectivo') {
                $metodo_pago = 10;
            }
            // se trea el id del municipio
            $municipalities = $this->municipalities->like('code', $json->Municipio . '' . $json->Ciudad, 'both')->get()->getResultObject();
            // se realiza transformacion de subtipo de trabajador
            if (!in_array($json->Numero_de_identificacion, $empleados)) {
                //valido si el trabajador existe
                $worker = $this->functions_payroll->worker_data($json->Numero_de_identificacion);
                $name = explode(' ', ltrim($json->Primer_nombre));
                if (is_null($worker)) {
                    // json datos del trabajador para realizar el guardado
                    $data_customer = [
                        'name' => $name[0],
                        'type_document_identifications_id' => $documento,
                        'identification_number' => $json->Numero_de_identificacion,
                        'address' => ($json->Direccion != '') ? $json->Direccion : 'N/A',
                        'email' => ($json->Correo_electronico ?? ''),
                        'type_customer_id' => 3,
                        'municipality_id' => $municipalities[0]->id,
                        'companies_id' => Auth::querys()->companies_id, //cambiar por empresa
                        'status' => 'Activo'
                    ];
                    $data_customer_worker = [
                        'type_worker_id' => $this->functions_payroll->id_type_and_subtype_worker($json->Tipo_trabajador),
                        'sub_type_worker_id' => $this->functions_payroll->id_type_and_subtype_worker($json->Subtipo_trabajador, true),
                        'bank_id' => ($bank != '') ? $bank : null,
                        'bank_account_type_id' => ($bank_account_type != 0) ? $bank_account_type : null,
                        'type_contract_id' => $type_contract_id,
                        'payment_method_id' => ($metodo_pago == 0) ? $json->metodo_de_pago : $metodo_pago,
                        'account_number' => ($json->Numero_de_cuenta != 0) ? $json->Numero_de_cuenta : null,
                        'surname' => $json->Apellido,
                        'second_name' => (!empty($name[1])) ? (count($name) > 2) ? $name[1] . ' ' . $name[2] : $name[1] : '',
                        'second_surname' => ($json->Segundo_apellido ?? ''),
                        'high_risk_pension' => ($json->Pension_de_alto_riesgo ?? 'false'),
                        'integral_salary' => ($json->Salario_integral == 'FALSO') ? 'false' : 'true',
                        'salary' => $json->Salario,
                        'admision_date' => $this->functions_payroll->split_date($json->Fecha_ingreso),
                        'retirement_date' => (isset($json->Fecha_retiro) && $json->Fecha_retiro != '0000-00-00' && $json->Fecha_retiro != '-   -') ? $this->functions_payroll->split_date($json->Fecha_retiro) : null,
                        'payroll_period_id' => $payroll_period
                    ];
                    $customer_id = $this->functions_payroll->add_customer($data_customer, $data_customer_worker);
                    if (isset($json->Correo_electronico) && $json->Correo_electronico != '') {
                        $data_user = [
                            'name' => $json->Primer_nombre,
                            'username' => $json->Correo_electronico,
                            'email' => $json->Correo_electronico,
                            'password' => password_hash($json->Numero_de_identificacion, PASSWORD_DEFAULT),
                            'status' => 'active',
                            'role_id' => 7,
                            'companies_id' => Auth::querys()->companies_id
                        ];
                        $this->functions_payroll->add_user($data_user, $customer_id);
                    }
                } else {
                    //valido si trabajador tiene correo
                    if (isset($json->Correo_electronico) && $json->Correo_electronico != '') {
                        // valida si el trabajador tiene usuario en el sistema
                        if (empty($worker->user_id) || is_null($worker->user_id)) {
                            $data_user = [
                                'name' => $json->Primer_nombre,
                                'username' => $json->Correo_electronico,
                                'email' => $json->Correo_electronico,
                                'password' => password_hash($json->Numero_de_identificacion, PASSWORD_DEFAULT),
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
                        'payment_method_id' => ($metodo_pago == 0) ? $json->metodo_de_pago : $metodo_pago
                    ];
                    if (in_array($json->metodo_de_pago, $this->code_method_payment)) {
                        $data_update['bank'] = ($bank != '') ? $bank : null;
                        $data_update['bank_account_type'] = ($bank_account_type != 0) ? $bank_account_type : null;
                        $data_update['account_number'] = ($json->Numero_de_cuenta != 0) ? $json->Numero_de_cuenta : null;
                        $data_update['retirement_date'] = (isset($json->Fecha_retiro) && $json->Fecha_retiro != '0000-00-00' && $json->Fecha_retiro != '-   -') ? $this->functions_payroll->split_date($json->Fecha_retiro) : null;
                    }
                    $this->functions_payroll->update_customer($worker, $data_update);
                }
                array_push($empleados, $json->Numero_de_identificacion);
            }
        }
    }

    public function base_invoice($info)
    {
        $cedulas = [];
        $dates = $this->functions_payroll->settlement_dates($info);
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if (!in_array($json->Numero_de_identificacion, $cedulas)) {
                if ($data->type_document_payroll == 10) {
                    try {
                        $ajuste = $this->invoices
                            ->join('customers', 'customers.id = invoices.customers_id')
                            ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                            ->where(['invoices.companies_id' => company()->id,
                                'invoices.invoice_status_id' => 14,
                                'invoices.type_documents_id' => 9,
                                'customers.identification_number' => $json->Numero_de_identificacion,
                                'payrolls.period_id' => $data->period_id])
                            ->get()->getResult()[0];
                    } catch (\Exception $e) {
                        //return redirect()->to(base_url() . '/import/payroll')->with('errors', $e->getMessage());
                    }
                }
                $id_customer = $this->functions_payroll->customer_id($json->Numero_de_identificacion);
                try {
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
                } catch (\Exception $e) {
                    return redirect()->to(base_url() . '/import/payroll')->with('errors', 'Inconveniente al guardar la información de invoices');
                }
                try {
                    $this->payroll->insert([
                        'settlement_start_date' => $dates['settlement_start_date'],
                        'settlement_end_date' => $dates['settlement_end_date'],
                        'worked_time' => $this->functions_payroll->worker_days($info, $json->Numero_de_identificacion),
                        'invoice_id' => $id_invoice,
                        'period_id' => $data->period_id,
                        'sub_period_id' => ($data->type_document_payroll == 10) ? $data->load_number : null,
                        'type_payroll_adjust_note_id' => ($data->type_document_payroll == 10) ? 1 : null
                    ]);
                } catch (\Exception $e) {
                    return redirect()->to(base_url() . '/import/payroll')->with('errors', 'Inconveniente al guardar la información de payroll');
                }
                $json_dates = json_decode($data->payment_dates);
                $dates_payment = explode(',', $json_dates[0]);
                foreach ($dates_payment as $json_date) {
                    try {
                        $this->payrollDate->insert([
                            'invoice_id' => $id_invoice,
                            'payroll_date' => $json_date
                        ]);
                    } catch (\Exception $e) {
                        return redirect()->to(base_url() . '/import/payroll')->with('errors', 'Inconveniente al guardar la información de pago');
                    }
                }
                array_push($cedulas, $json->Numero_de_identificacion);
            }
        }
    }

}