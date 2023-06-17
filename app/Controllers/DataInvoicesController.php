<?php

namespace App\Controllers;


use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\PayrollDate;
use App\Models\Resolution;
use App\Controllers\Api\Auth;

class DataInvoicesController extends BaseController
{
    public $invoices;
    public $customers;
    public $resolutions;
    public $payroll;
    public $accrueds;
    public $deduction;
    public $payrollDate;

    public function __construct()
    {
        $this->accrueds = new DataAccruedsController();
        $this->deduction = new DataDeductionsController();
        $this->invoices = new Invoice();
        $this->customers = new Customer();
        $this->resolutions = new Resolution();
        $this->payroll = new Payroll();
        $this->payrollDate = new PayrollDate();
    }

    public function Asnomina($info)
    {
        $cedulas = [];
        $resolution = $this->resolutions->where(['id' => 1])->get()->getResult()[0];
        $dates = $this->settlement_dates($info);
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if (!in_array($json->Numero_de_identificacion, $cedulas)) {
                $id_customer = $this->customer_id($json->Numero_de_identificacion);
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
                    'worked_time' => $this->worker_days($info, $json->Numero_de_identificacion),
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
        $this->accrueds->accrueds_Asnomina($info);
        $this->deduction->deductions_Asnomina($info);
    }

    public function tycnomina($info)
    {
        //$dates = $this->settlement_dates($info);
        foreach ($info as $data) {
            $json = json_decode($data->data);
            $id_customer = $this->customer_id($json->Numero_de_Identificacion);
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
                'settlement_start_date' => $json->Fecha_inicio_de_liquidacion,
                'settlement_end_date' => $json->Fecha_fin_liquidacion,
                'worked_time' => $json->Dias_Laborados,
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
        }
        $this->accrueds->accrueds_tyc($info);
        $this->deduction->deductions_tyc($info);
    }

    private function customer_id($identification)
    {
        $customer = $this->customers->where(['identification_number' => $identification])->get()->getResult();
        return $customer[0]->id;
    }

    public function split_date($fecha)
    {
        return date("Y-m-d", strtotime($fecha));
    }

    public function settlement_dates($info)
    {
        $settlement_start_date = [];
        $settlement_end_date = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            array_push($settlement_start_date, $this->split_date($json->Fecha_inicio_liquidacion));
            array_push($settlement_end_date, $json->fecha_fin_liquidacion);
        }
        $response = [
            'settlement_start_date' => min($settlement_start_date),
            'settlement_end_date' => max($settlement_end_date)
        ];
        return $response;

    }

    private function worker_days($info, $identification)
    {
        $days = 0;
        $periodo = [];
        foreach ($info as $data) {
            $json = json_decode($data->data);
            if (!in_array($data->load_number, $periodo)) {
                if ($json->Numero_de_identificacion == $identification) {
                    $days += $json->dias_trabajados;
                    array_push($periodo, $data->load_number);
                }
            }
        }
        return $days;
    }
}
