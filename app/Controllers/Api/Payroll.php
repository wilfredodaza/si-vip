<?php 

namespace App\Controllers\Api;

use App\Controllers\PayrollController;
use CodeIgniter\RESTful\ResourceController;
use App\Traits\ValidationsTrait;
use App\Models\Customer;
use App\Models\Resolution;
use App\Models\PayrollDate;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Payroll as PayrollModel;
use phpDocumentor\Reflection\Types\Object_;

class Payroll extends ResourceController
{
    use ValidationsTrait;

    protected $format       = 'json';
    protected $modelName    =  'App\Models\Invoice';




    public function edit($id = null) 
    {
       $invoice = $this->model
       ->select(['invoices.*',  'payrolls.*','payrolls.id as payroll_id', 'invoices.id as invoices_id'])
       ->join('payrolls', 'payrolls.invoice_id = invoices.id')
       ->where(['payrolls.invoice_id' => $id])
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


        $arrayAccrueds = [];

        $model = new Accrued();
        $accrued = $model->select(['accrueds.*',  'type_accrueds.name'])
            ->join('type_accrueds', 'type_accrueds.id = accrueds.type_accrued_id')
            ->where(['payroll_id' => $invoice->payroll_id, 'accrueds.type_accrued_id' => 1])
            ->asObject()
            ->first();



        if(!is_null($accrued)) {
            $arrayAccrueds[0] = [
                'typeAccrued'                   => $accrued->name,
                'typeAccruedId'                 => $accrued->type_accrued_id,
                'percentage'                    => empty($accrued->type_overtime_surcharge_id) ? null :  $accrued->type_overtime_surcharge_id,
                'type'                          => empty($accrued->type_disability_id) ? null :  $accrued->type_disability_id,
                'payment'                       => (double)  $accrued->payment,
                'start_date'                    =>  $accrued->start_time == '0000-00-00 00:00:00' ? null :  $accrued->start_time,
                'end_date'                      =>  $accrued->end_time  == '0000-00-00 00:00:00' ? null :  $accrued->end_time,
                'quantity'                      =>  $accrued->quantity,
                'percentageValue'               =>  $accrued->percentage,
                'otherPayment'                  =>  $accrued->other_payments,
                'description'                   =>  $accrued->description
            ];
        } else {
            $arrayAccrueds[0] = [
                'typeAccrued'                   => 'Salario',
                'typeAccruedId'                 =>  1,
                'percentage'                    =>  null,
                'type'                          =>  null,
                'payment'                       =>  0,
                'start_date'                    =>  null,
                'end_date'                      =>  null,
                'quantity'                      =>  null,
                'percentageValue'               =>  null,
                'otherPayment'                  =>  null,
                'description'                   =>  null,
            ];
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

        $i = 1;
       foreach($accrueds  as $item ) {
           if($item->type_accrued_id > 1) {
               $arrayAccrueds[$i] = [
                   'type_document_id'              => $invoice->type_documents_id,
                   'typeAccrued'                   => $item->name,
                   'typeAccruedId'                 => $item->type_accrued_id,
                   'percentage'                    => empty($item->type_overtime_surcharge_id) ? null : $item->type_overtime_surcharge_id,
                   'type'                          => empty($item->type_disability_id) ? null : $item->type_disability_id,
                   'payment'                       => (double) $item->payment,
                   'start_date'                    => $item->start_time == '0000-00-00 00:00:00' ? null : $item->start_time,
                   'end_date'                      => $item->end_time == '0000-00-00 00:00:00' ? null : $item->end_time,
                   'quantity'                      => $item->quantity,
                   'percentageValue'               => $item->percentage,
                   'otherPayment'                  => $item->other_payments,
                   'description'                   => $item->description
               ];
               $i++;
           }

       }


       $arrayDeductions = [];
       $model       = new Deduction();
       $deductionEPS  = $model->select(['deductions.*', 'type_deductions.name'])
       ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id')
        ->where(['deductions.payroll_id' => $invoice->payroll_id, 'deductions.type_deduction_id' => 1])
       ->asObject()
       ->first();




        if(!is_null($deductionEPS)) {
            $arrayDeductions[0] = [
                'typeDeduction'         => $deductionEPS->name,
                'typeDeductionId'       => $deductionEPS->type_deduction_id,
                'percentage'            => $deductionEPS->type_law_deduction_id,
                'payment'               => $deductionEPS->payment,
                'percentageValue'       => $deductionEPS->percentage,
                'description'           => $deductionEPS->description,
            ];
        } else {
            $arrayDeductions[0] = [
                'typeDeduction'         => 'EPS',
                'typeDeductionId'       => 1,
                'percentage'            => '3',
                'payment'               => 0,
                'percentageValue'       => '4.00',
                'description'           => NULL,
            ];
        }


        $model       = new Deduction();
        $deductionPension  = $model->select(['deductions.*', 'type_deductions.name'])
            ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id')
            ->where(['deductions.payroll_id' => $invoice->payroll_id, 'deductions.type_deduction_id' => 2])
            ->asObject()
           ->first();

        if(!is_null($deductionPension )) {
            $arrayDeductions[1] = [
                'typeDeduction'         => $deductionPension->name,
                'typeDeductionId'       => $deductionPension->type_deduction_id,
                'percentage'            => $deductionPension->type_law_deduction_id,
                'payment'               => $deductionPension->payment,
                'percentageValue'       => $deductionPension->percentage,
                'description'           => $deductionPension->description,
            ];
        } else {
            $arrayDeductions[1] = [
                'typeDeduction'         => 'Pension',
                'typeDeductionId'       => 2,
                'percentage'            => '5',
                'payment'               => 0,
                'percentageValue'       => '4.00',
                'description'           => NULL,
            ];
        }


        $model       = new Deduction();
        $deductions  = $model->select(['deductions.*', 'type_deductions.name'])
            ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id')
            ->where(['deductions.payroll_id' => $invoice->payroll_id])
            ->orderBy('type_deductions.id', 'ASC')
            ->asObject()
            ->get()
            ->getResult();


        $i = 2;
       foreach($deductions  as $item ) {
           if($item->type_deduction_id > 2) {
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
       }

        $predecessor = [
            'predecessor_number'     => $invoice->resolution,
            'predecessor_cune'       => $invoice->uuid,
            'predecessor_issue_date' => $invoice->issue_date
        ];

       $data = [
           'id'                     => $invoice->invoices_id,
            'type_document_id'      => $invoice->type_documents_id,
            'sub_period_id'         => $invoice->sub_period_id,
            'period_id'             => $invoice->period_id,
            'worker_id'             => $invoice->customers_id,
            'worked_time'           => $invoice->worked_time,
            'notes'                 => $invoice->notes,
            'settlement_start_date' => $invoice->settlement_start_date,
            'settlement_end_date'   => $invoice->settlement_end_date,
            'predecessor'           => $predecessor,
            'payment_dates'         => $arrayPayrollDates,
            'accrued'               => $arrayAccrueds,
            'deductions'            => $arrayDeductions
       ];



      return $this->respond(['status' => 200, 'data' => $data]);
    
    }


    public function update($id = null) 
    {

        try{
            $this->validateRequest(
                $this->request,
                [   
                    'period'                                    => 'required',
                    'period.retirement_date'                    => 'if_exist|valid_date[Y-m-d]',
                    'period.settlement_start_date'              => 'required|valid_date[Y-m-d]',
                    'period.settlement_end_date'                => 'required|valid_date[Y-m-d]',
                    'worker_id'                                 => 'required|numeric',
                    'notes'                                     => 'if_exist',
                    'accrued'                                   => 'required|trim',
                    'accrued.salary'                            => 'required',
                    'deduction'                                 => 'required'
                ]
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

      


        $this->model->update($id, [
            'pyment_methods_id'     => $worker->payment_method_id,
            'issue_date'            => date('Y-m-d'),
            'user_id'               => Auth::querys()->id,
            'customers_id'          => $json->worker_id,
            'invoice_status_id'     => 13,
            'notes'                 => $json->notes
        ]);

      
        $payroll = new PayrollModel();
        $payroll->set('settlement_start_date', $json->period->settlement_start_date)
        ->set('settlement_end_date', $json->period->settlement_end_date)
        ->set('worked_time', $json->period->worked_time)
        ->where(['invoice_id' => $id])
        ->update();

     
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
                    'start_time'                    => $item->start_date  == '0000-00-00 00:00:00' ? null : $item->start_date,
                    'end_time'                      => $item->end_date == '0000-00-00 00:00:00' ? null : $item->end_date,
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
                    'start_time'                    => $item->start_date  == '0000-00-00 00:00:00' ? null : $item->start_date,
                    'end_time'                      => $item->end_date == '0000-00-00 00:00:00' ? null : $item->end_date,
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


        if(count($deductions) > count($json->deduction)) {
            $arrayReverse = array_reverse($deductions);
  
            for($i = 0; $i < (count($deductions) - count($json->deduction)); $i++) {
                $deduction = new Deduction();
                $deduction->delete(['id' => $arrayReverse[$i]->id]);
            }
        }
        

        $l = 0;
        foreach($json->deduction as $item) {
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
        $payroll->previsualization($id);


        return $this->respond([ 'status' => 200,  'data' => $this->request->getJSON()]);
    }

}