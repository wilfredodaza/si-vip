<?php


namespace App\Controllers\companies;

use App\Controllers\Api\Auth;
use App\Controllers\BaseController;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Invoice;
use App\Models\OtherConcepts;
use App\Models\Payroll;
use App\Models\PayrollDate;


class Payroll_altara extends BaseController
{
    public $functions_payroll;
    public $base_payroll_controller;
    public $invoices;
    public $payroll;
    public $payrollDate;
    public $accrued;
    public $deduction;
    public $other_concepts;

    public function __construct()
    {
        $this->functions_payroll = new Functions_Payroll();
        $this->base_payroll_controller = new BasePayrollController();
        $this->invoices = new Invoice();
        $this->payroll = new Payroll();
        $this->payrollDate = new PayrollDate();
        $this->accrued = new Accrued();
        $this->deduction = new Deduction();
        $this->other_concepts = new OtherConcepts();
    }

    public function init_altara($nit, $month_payroll)
    {
        $info = $this->functions_payroll->data($nit, $month_payroll);
        $this->base_payroll_controller->base_workers($info);
        $this->base_payroll_controller->base_invoice($info);
        $this->functions_payroll->save_data_complete($info);
        $this->functions_payroll->update_status_upload($nit,$month_payroll);
    }
}


