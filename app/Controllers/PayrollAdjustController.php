<?php


namespace App\Controllers;


class PayrollAdjustController extends BaseController
{
    /**
     * 
     * 
     */
    public function index($id = null)
    {
        return view('payroll_adjust/index', ['id' => $id]);
    }

    public function edit($id = null)
    {
        return view('payroll_adjust/show', ['id' => $id]);
    }
}