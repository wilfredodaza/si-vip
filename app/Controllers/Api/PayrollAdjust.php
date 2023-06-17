<?php


namespace App\Controllers\Api;




use App\Controllers\PayrollController;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ValidationsTrait;
use App\Models\Customer;
use App\Models\PayrollDate;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Payroll as PayrollModel;



class PayrollAdjust extends ResourceController
{
    use ValidationsTrait;

    protected $format       = 'json';
    protected $modelName    =  'App\Models\Invoice';


    public function store()
    {

        try{
            $this->validateRequest(
                $this->request,
                [
                                  'payment_dates'                             => 'required'                ]
            );
        } catch(\Exception $e) {

        }

        if($this->validator->getErrors()) {
            return $this->respond([
                'status'    => 400,
                'errors'    => $this->validator->getErrors()
            ], 400);
        }


        $json = $this->request->getJSON();

        $model = new Customer();
        $worker = $model->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->where(['customers.id' => $json->worker_id])
            ->asObject()
            ->first();



        $invoice = $this->model->insert([
            'companies_id'      => Auth::querys()->companies_id,
            'pyment_methods_id'  => $worker->payment_method_id,
            'created_at'        => date('Y-m-d H:i:s'),
            'issue_date'        => date('Y-m-d'),
            'type_documents_id' => 10,
            'invoice_status_id' => 13,
            'user_id'           => Auth::querys()->id,
            'customers_id'      => $json->worker_id,
            'uuid'              => $json->predecessor->predecessor_cune,
            'resolution_credit' => $json->predecessor->predecessor_number,
            'notes'             => $json->notes
        ]);



        $payroll = new PayrollModel();
        $payrollId = $payroll->insert([
            'invoice_id'                     => $invoice,
            'settlement_start_date'          => $json->settlement_start_date,
            'settlement_end_date'            => $json->settlement_end_date,
            'period_id'                      => $json->period_id,
            'worked_time'                    => $json->worked_time,
            'type_payroll_adjust_note_id'    => $json->type_note
        ]);

        foreach($json->payment_dates as $item) {
            $payment = new PayrollDate();
            $payment->save([
                'payroll_date'  => $item,
                'invoice_id'    => $invoice
            ]);
        }


        foreach($json->accrued as $item) {
            $accrued = new Accrued();
            $accrued->insert([
                'type_accrued_id'               => $item->typeAccruedId,
                'payroll_id'                    => $payrollId,
                'type_overtime_surcharge_id'    => empty($item->percentage) ? null : $item->percentage,
                'type_disability_id'            => empty($item->type) ? null : $item->type,
                'payment'                       => $item->payment,
                'start_time'                    => $item->start_date,
                'end_time'                      => $item->end_date,
                'quantity'                      => $item->quantity,
                'percentage'                    => $item->percentageValue,
                'other_payments'                => $item->otherPayment,
                'description'                   => $item->description,

            ]);
        }


        foreach($json->deductions as $item) {
            $accrued = new Deduction();
            $accrued->insert([
                'type_deduction_id'     => $item->typeDeductionId,
                'payroll_id'            => $payrollId,
                'type_law_deduction_id' => $item->percentage,
                'payment'               => $item->payment,
                'percentage'            => $item->percentageValue,
                'description'           => $item->description,
            ]);
        }



        return $this->respondCreated(['status' => 201, 'data' => $this->request->getJSON()]);
    }

    public function edit($id = null)
    {
        $invoice = $this->model
            ->select(['invoices.*',  'payrolls.*','payrolls.id as payroll_id'])
            ->join('payrolls', 'payrolls.invoice_id = invoices.id')
            ->where(['invoices.id' => $id])
            ->asObject()
            ->first();



        $model = new PayrollDate();
        $payrollDate = $model->where(['invoice_id' => $id ])
            ->asObject()
            ->get()
            ->getResult();

        $arrayPayrollDates = [];
        $i = 0;
        foreach($payrollDate as $item) {
            $arrayPayrollDates[$i] = $item->payroll_date;
            $i++;
        }



        $model = new Accrued();
        $accrueds = $model
            ->select(['accrueds.*', 'type_accrueds.name'])
            ->join('type_accrueds', 'type_accrueds.id = accrueds.type_accrued_id')
            ->where(['payroll_id' => $invoice->payroll_id])
            ->orderBy('type_accrueds.id', 'ASC')
            ->asObject()
            ->get()
            ->getResult();




        $arrayAcrueds = [];
        $i = 0;
        foreach($accrueds  as $item ) {
            $arrayAcrueds[$i] = [
                'typeAccrued'                   => $item->name,
                'typeAccruedId'                 => $item->type_accrued_id,
                'percentage'                    => empty($item->type_overtime_surcharge_id) ? null : $item->type_overtime_surcharge_id,
                'type'                          => empty($item->type_disability_id) ? null : $item->type_disability_id,
                'payment'                       => (double) $item->payment,
                'start_date'                    => $item->start_time,
                'end_date'                      => $item->end_time,
                'quantity'                      => $item->quantity,
                'percentageValue'               => $item->percentage,
                'otherPayment'                  => $item->other_payments,
                'description'                   => $item->description
            ];
            $i++;
        }



        $model       = new Deduction();
        $deductions  = $model->select(['deductions.*', 'type_deductions.name'])
            ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id')
            ->where(['deductions.payroll_id' => $invoice->payroll_id])
            ->orderBy('type_deductions.id', 'ASC')
            ->asObject()
            ->get()
            ->getResult();




        $arrayDeductions = [];
        $i = 0;
        foreach($deductions  as $item ) {
            $arrayDeductions[$i] = [
                'typeDeduction'         => $item->name,
                'typeDeductionId'       => $item->type_deduction_id,
                'percentage'            => $item->type_law_deduction_id,
                'payment'               => $item->payment,
                'percentageValue'       => $item->percentage,
                'description'           => $item->description,
            ];
            $i++;
        }

        $predecessor = [
            'predecessor_number'     => $invoice->resolution,
            'predecessor_cune'       => $invoice->uuid,
            'predecessor_issue_date' => $invoice->issue_date
        ];

        $data = [
            'id'                    => $invoice->invoice_id,
            'type_document_id'      => $invoice->type_documents_id,
            'period_id'             => $invoice->period_id,
            'sub_period_id'         => $invoice->sub_period_id,
            'worker_id'             => $invoice->customers_id,
            'worked_time'           => $invoice->worked_time,
            'notes'                 => $invoice->notes,
            'settlement_start_date' => $invoice->settlement_start_date,
            'settlement_end_date'   => $invoice->settlement_end_date,
            'predecessor'           => $predecessor,
            'payment_dates'         => $arrayPayrollDates,
            'accrued'               => $arrayAcrueds,
            'deductions'            => $arrayDeductions
        ];

        return $this->respond(['status' => 200, 'data' => $data]);

    }

    public function update($id = null)
    {

        $json = $this->request->getJSON();
        

        $model = new Customer();
        $worker = $model->join('customer_worker', 'customers.id = customer_worker.customer_id')
            ->where(['customers.id' => $json->worker_id])
            ->asObject()
            ->first();


        $this->model->update($id, [
            'pyment_methods_id'     => $worker->payment_method_id,
            'issue_date'            => date('Y-m-d'),
            'user_id'               => Auth::querys()->id,
            'customers_id'          => $json->worker_id,
            'invoice_status_id'     => 13,
            'notes'                 => $json->notes
        ]);

        $payroll = new PayrollModel();
        $model = $payroll->select(['id'])
        ->where(['invoice_id' => $id])
        ->asObject()
        ->first();



        $payroll = new PayrollModel();
        $payroll->update($model->id,[
            'settlement_start_date'          => $json->settlement_start_date,
            'settlement_end_date'            => $json->settlement_end_date,
            'worked_time'                    => $json->worked_time,
            'type_payroll_adjust_note_id'    => $json->type_note
        ]);

        $payroll   = new PayrollModel();
        $payrollId = $payroll->select(['id'])
            ->where(['invoice_id' => $id])
            ->asObject()
            ->first();



        $model = new PayrollDate();
        $payrollDates = $model->select(['id'])
            ->where(['invoice_id' => $id])
            ->asObject()
            ->get()
            ->getResult();

        $l = 0;
        $i = 1;
         foreach($json->payment_dates as $item) {

             $payment = new PayrollDate();
             if(count($payrollDates) >= $i) {
                 $payment->update([
                     'payroll_date'  => $item,
                 ], ['invoice_id' => $id]);
             }else {
                 $payment->insert([
                     'payroll_date'  => $item,
                     'invoice_id'    => $id
                 ]);
             }
             $i++;
             $l++;
         }

       $accrued = new Accrued();
         $accrueds = $accrued->select(['id'])
            ->where(['payroll_id' => $payrollId->id])
            ->asObject()
            ->get()
            ->getResult();


        if(count($accrueds) > count($json->accrued)) {
            $arrayReverse = array_reverse($accrueds);
            for($i = 0; $i < (count($accrueds) - count($json->accrued)); $i++) {
                $accrued = new Accrued();
                $accrued->delete(['id' => $arrayReverse[$i]->id]);
            }
        }




        $l = 0;
        foreach($json->accrued as $item) {
            $accrued = new Accrued();
            if($l < count($accrueds)) {
                $accrued->update($accrueds[$l]->id, [
                    'type_accrued_id'               => $item->typeAccruedId,
                    'type_overtime_surcharge_id'    => empty($item->percentage) ? null : $item->percentage,
                    'type_disability_id'            => empty($item->type) ? null : $item->type,
                    'payment'                       => $item->payment,
                    'start_time'                    => $item->start_date,
                    'end_time'                      => $item->end_date,
                    'quantity'                      => $item->quantity,
                    'percentage'                    => $item->percentageValue,
                    'other_payments'                => $item->otherPayment,
                    'description'                   => $item->description
                ]);
            } else {
                $accrued->insert([
                    'payroll_id'                    => $payrollId->id,
                    'type_accrued_id'               => $item->typeAccruedId,
                    'type_overtime_surcharge_id'    => empty($item->percentage) ? null : $item->percentage,
                    'type_disability_id'            => empty($item->type) ? null : $item->type,
                    'payment'                       => $item->payment,
                    'start_time'                    => $item->start_date,
                    'end_time'                      => $item->end_date,
                    'quantity'                      => $item->quantity,
                    'percentage'                    => $item->percentageValue,
                    'other_payments'                => $item->otherPayment,
                    'description'                   => $item->description
                ]);
            }
            $l++;
        }





        $deduction = new Deduction();
        $deductions = $deduction->select(['id'])
            ->where(['payroll_id' => $payrollId->id])
            ->asObject()
            ->get()
            ->getResult();




        if(count($deductions) > count($json->deductions)) {
            $arrayReverse = array_reverse($deductions);
            for($i = 0; $i < (count($deductions) - count($json->deduction)); $i++) {
                $deduction = new Deduction();
                $deduction->delete(['id' => $arrayReverse[$i]->id]);
            }
        }


        $l = 0;
        foreach($json->deductions as $item) {
            $deduction = new Deduction();
            if($l < count($deductions)) {
                $deduction->update($deductions[$l]->id,[
                    'type_deduction_id'     => $item->typeDeductionId,
                    'type_law_deduction_id' => $item->percentage,
                    'payment'               => $item->payment,
                    'percentage'            => $item->percentageValue,
                    'description'           => $item->description,
                ]);
            }else {
                $deduction->insert([
                    'type_deduction_id'     => $item->typeDeductionId,
                    'payroll_id'            => $payrollId->id,
                    'type_law_deduction_id' => $item->percentage,
                    'payment'               => $item->payment,
                    'percentage'            => $item->percentageValue,
                    'description'           => $item->description,
                ]);
            }

            $l++;
        }


        $payroll = new PayrollController();
   


        return $this->respond([ 'status' => 200,  'data' => $this->request->getJSON()]);

    }

}