<?php


namespace App\Traits;


use App\Models\Invoice;
use App\Models\PayrollDate;
use App\Models\Payroll;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Resolution;
use App\Controllers\Api\Auth;



trait PayrollTrait
{

    private $payroll = [];


    public function general($data) 
    {
        $this->payroll['period_month']          = $data->month;
        $this->payroll['type_document_id']      = $data->type_documents_id;
        $this->payroll['payroll_period_id']     = $data->payroll_period_id;
        $this->payroll['notes']                 = $data->notes;
        if($data->type_payroll_adjust_note_id == null || $data->type_payroll_adjust_note_id == 1) {
            $this->payroll['worker_code']           = is_null($data->worker_code) || empty($data->worker_code) ?  $data->identification_number : $data->worker_code;
        }
        $this->payroll['customer_id']           = $data->customers_id;
    }

    public function resolution($resolutions, $companyId, $id, $type)
    {

        if($resolutions != 0) {
            $model      = new Resolution();
            $resolutionModel = $model->asObject()->find($resolutions);

            $this->payroll['resolution_number']     = $resolutionModel->resolution;

            $invoice = new Invoice();
            $model = $invoice->select(['resolution'])
            ->whereIn('type_documents_id', [9, 10])
            ->where([
                'resolution_id'         => $this->payroll['resolution_number'],
                'companies_id'          => $companyId,
                'resolution !='         => null,
                'invoice_status_id !='  => 15
            ])
            ->orderBy('CAST(resolution as UNSIGNED)', 'DESC')
            ->asObject()
            ->first();



            $this->payroll['prefix']                = $resolutionModel->prefix;
            if(!$model) {
                $model = new Resolution();
                $resolutionInit = $model
                    ->whereIn('type_documents_id', [9, 10])
                    ->where([
                    'resolution'            => $this->payroll['resolution_number'], 
                    'companies_id'          => $companyId
                    ])
                    ->asObject()
                    ->first();

                $this->payroll['consecutive']  =  (int) $resolutionInit->from;
            }else {
                $this->payroll['consecutive']  =   $model->resolution + 1;
            }
        }else {
            $this->payroll['consecutive'] = $id;
        }
    }


    public function predecessor($invoice)
    {
        $this->payroll['predecessor']['predecessor_number']         = $invoice->resolution_credit;
        $this->payroll['predecessor']['predecessor_cune']           = $invoice->uuid;
        $this->payroll['predecessor']['predecessor_issue_date']     = $invoice->issue_date;
        $this->payroll['type_note']                                 = $invoice->type_payroll_adjust_note_id;
    }

    public function novelty() 
    {

    }


    public function period($data) 
    {
        $this->payroll['customer_id']                                               = $data->customer_id;
        $this->payroll['period']['admision_date']                                   = $data->admision_date;
        $this->payroll['period']['retirement_date']                                 = $data->retirement_date;
        $this->payroll['period']['settlement_start_date']                           = $data->settlement_start_date;
        $this->payroll['period']['settlement_end_date']                             = $data->settlement_end_date;
        $this->payroll['period']['issue_date']                                      = date('Y-m-d');
        $this->payroll['period']['worked_time']                                     = $this->workerTime($data->admision_date, $data->settlement_end_date);
        $this->payroll['accrued']['worked_days']                                    = $data->worked_time;
 	    $this->payroll['worker']['type_worker_id']                                  = $data->type_worker_id;
        $this->payroll['worker']['sub_type_worker_id']                              = $data->sub_type_worker_id;
        $this->payroll['worker']['payroll_type_document_identification_id']         = $data->type_document_identification_id;
        $this->payroll['worker']['municipality_id']                                 = $data->municipality_id;
        $this->payroll['worker']['type_contract_id']                                = $data->type_contract_id;
        $this->payroll['worker']['high_risk_pension']                               = $data->high_risk_pension  == 'true' ? true : false;
        $this->payroll['worker']['identification_number']                           = $data->identification_number;
        $this->payroll['worker']['surname']                                         = $data->surname;
        $this->payroll['worker']['second_surname']                                  = is_null($data->second_surname) ? ' ' : $data->second_surname;
        $this->payroll['worker']['first_name']                                      = $data->first_name;
        $this->payroll['worker']['middle_name']                                     = $data->second_name;
        $this->payroll['worker']['address']                                         = $data->address;
        $this->payroll['worker']['integral_salarary']                               = $data->integral_salary == 'true' ? true : false;
        $this->payroll['worker']['salary']                                          = $data->salary;
        $this->payroll['worker']['work']                                            = $data->work;

    }


    public function worker($data)
    {
       
    }

    public function workerTime($data)
    {
        $hiringDate      = new \DateTime($data);
        $currentDate     = new \DateTime(date('Y-m-d'));
        $difference      = $hiringDate->diff($currentDate);
        return ((int)$difference->y * 360) + ((int)$difference->m  * 30 ) + ($difference->d);
    }

   
    public function payment($data)
    {
        $this->payroll['payment']['payment_method_id']          = $data->payment_method_id;
        $this->payroll['payment']['bank_name']                  = $data->bank_name;
        $this->payroll['payment']['account_type']               = $data->account_type;
        $this->payroll['payment']['account_number']             = $data->account_number;
    }

    public function paymentDates($id)
    {
        $this->payroll['payment_dates'] = [];

        $model = new PayrollDate();
        $paymentDates = $model->where(['invoice_id' => $id])
        ->get()
        ->getResult();

        $i = 0;
        foreach($paymentDates as $item) {
            $this->payroll['payment_dates'][$i]['payment_date'] = $item->payroll_date;
            $i++;
        }
    }

    public function accrued($id)
    {
        $model = new Accrued();
        $accrueds = $model
        ->select([
            'type_accrueds.domain as type_accrued_domain',
            'type_accrueds.group as type_accrued_group',
            'type_accrueds.element as type_accrued_element',
            'type_accrueds.id as type_accrued_id',
            'accrueds.payment',
            'accrueds.start_time',
            'accrueds.end_time',
            'accrueds.quantity',
            'accrueds.percentage',
            'accrueds.payment',
            'accrueds.description',
            'accrueds.other_payments',
            'type_overtime_surcharge_id',
            'accrueds.type_disability_id'
        ])
        ->join('type_accrueds', 'type_accrueds.id = accrueds.type_accrued_id')
        ->where(['payroll_id' => $id])
        ->get()
        ->getResult();

        $this->payroll['accrued']['accrued_total'] = 0;

        $existSalary = false;
        foreach($accrueds as $item)
        {
            if($item->type_accrued_id == 1) {
                $existSalary = true;
            }

            switch($item->type_accrued_domain) {
                case 'atributo':
                    if($item->type_accrued_id == 1) {
                        $item->description ?    $this->payroll['accrued']['salary_concept']  =  $item->description : '';
                    }
                    $this->attribute($item->type_accrued_group, $item->payment);
                    break;
                case 'array':
                    $this->array($item->type_accrued_group, $item);
                    break;
                case 'array_no_percentage':
                    $this->arrayNoPercentage($item->type_accrued_group, $item);
                    break;
                case 'array_atributos':
                    $this->arrayAttribute($item->type_accrued_group, $item);
                    break;
                case 'service_bonus':
                        $this->serviceBonus($item->type_accrued_group, $item);
                    break;
                case 'severance':
                    if( $this->payroll['type_document_id'] != 109 ) {
                        $this->severance($item->type_accrued_group, $item);
                    } else if( $this->payroll['type_document_id'] == 109 &&  $this->payroll['period_month'] != 'Enero') {
                       $this->severance($item->type_accrued_group, $item);
                    }


                break;
            }

            if($this->payroll['type_document_id'] == 109){
                if($item->payment != null && $item->type_accrued_id != 16) {
                    $this->payroll['accrued']['accrued_total'] += (double) $item->payment;
                }

		if($item->payment != null && $item->type_accrued_id == 16 &&  $this->payroll['period_month'] != 'Enero') {
                    $this->payroll['accrued']['accrued_total'] += (double) $item->payment;
                }
    
                if($item->other_payments != null && $item->type_accrued_id != 16) {
                    $this->payroll['accrued']['accrued_total'] += (double) $item->other_payments;
                }else if($this->payroll['type_document_id'] == 109 &&  $this->payroll['period_month'] != 'Enero'){
                    $this->payroll['accrued']['accrued_total'] += (double) $item->other_payments;
                }
            }  else {
                if($item->payment != null) {
                    $this->payroll['accrued']['accrued_total'] += (double) $item->payment;
                }
    
                if($item->other_payments != null) {
                    $this->payroll['accrued']['accrued_total'] += (double) $item->other_payments;
                }
            }
          
          
           
        }
        if($existSalary ==  false) {
            $this->payroll['accrued']['salary'] = 0;
            if($item->type_accrued_id == 1) {
                $value->description ?    $this->payroll['accrued']['salary_concept']  =  $item->description : '';
            }
         
        }
    }

    public function attribute($attribute, $value) {
        $this->payroll['accrued'][$attribute] = $value;
    }

    public function array($attribute, $value) {

        if(isset($this->payroll['accrued'][$attribute])) {
            $count = count($this->payroll['accrued'][$attribute]) + 1;
            $this->payroll['accrued'][$attribute][$count]['start_time']     =  $value->start_time == null ? null : str_replace(' ' , 'T', $value->start_time);
            $this->payroll['accrued'][$attribute][$count]['end_time']       =  $value->end_time == null ? null :  str_replace(' ' , 'T',$value->end_time);
            $this->payroll['accrued'][$attribute][$count]['quantity']       =  (int) $value->quantity;
            $this->payroll['accrued'][$attribute][$count]['percentage']     =  (int) $value->type_overtime_surcharge_id;
            $this->payroll['accrued'][$attribute][$count]['payment']        =  $value->payment;
            
        }else {
            $this->payroll['accrued'][$attribute][0]['start_time']          =  $value->start_time == null ? null :  str_replace(' ' , 'T', $value->start_time);
            $this->payroll['accrued'][$attribute][0]['end_time']            =  $value->end_time == null ? null :  str_replace(' ' , 'T',$value->end_time);
            $this->payroll['accrued'][$attribute][0]['quantity']            =  (int) $value->quantity;
            $this->payroll['accrued'][$attribute][0]['percentage']          =  (int) $value->type_overtime_surcharge_id;
            $this->payroll['accrued'][$attribute][0]['payment']             =  $value->payment;
        }
    }

    public function arrayNoPercentage($attribute, $value) {

        $start = new \DateTime($value->start_time);
        $end   = new \DateTime($value->end_time);

     
        if(isset($this->payroll['accrued'][$attribute])) {
            $count = count($this->payroll['accrued'][$attribute]) + 1;
            $this->payroll['accrued'][$attribute][$count]['start_date']     =  $value->start_time == null ? null :  $start->format('Y-m-d') ;
            $this->payroll['accrued'][$attribute][$count]['end_date']       =  $value->end_time == null ? null : $end->format('Y-m-d');
            $this->payroll['accrued'][$attribute][$count]['quantity']       =  $value->quantity;
            $this->payroll['accrued'][$attribute][$count]['payment']        =  $value->payment;
            $value->type_disability_id ? $this->payroll['accrued'][$attribute][$count]['type'] =  $value->type_disability_id: '';    
        }else {
            $this->payroll['accrued'][$attribute][0]['start_date']          =  $value->start_time == null ? null :  $start->format('Y-m-d') ;
            $this->payroll['accrued'][$attribute][0]['end_date']            =  $value->end_time == null ? null : $end->format('Y-m-d');
            $this->payroll['accrued'][$attribute][0]['quantity']            =  $value->quantity;
            $value->payment ? $this->payroll['accrued'][$attribute][0]['payment'] =  $value->payment : '';
            $value->type_disability_id ? $this->payroll['accrued'][$attribute][0]['type'] =  $value->type_disability_id : '';   
       
        }

    
    }

    public function arrayAttribute($attribute, $value) {
        if(isset($this->payroll['accrued'][$attribute])) {
            $count = count($this->payroll['accrued'][$attribute]);
            $this->payroll['accrued'][$attribute][$count][$value->type_accrued_element]        =  $value->payment;
            $value->description ? $this->payroll['accrued'][$attribute][$count]['description_concept']  =  $value->description : '';
        }else {
            $this->payroll['accrued'][$attribute][0][$value->type_accrued_element]               =  $value->payment;
            $value->description ? $this->payroll['accrued'][$attribute][0]['description_concept']  =  $value->description : '';
        }
    }

    public function serviceBonus($attribute, $value)
    {
        if(isset($this->payroll['accrued'][$attribute])) {
            $count = count($this->payroll['accrued'][$attribute]) + 1;
            $this->payroll['accrued'][$attribute][$count]['quantity']       =  $value->quantity;
            $this->payroll['accrued'][$attribute][$count]['payment']        =  $value->payment;
            $this->payroll['accrued'][$attribute][$count]['paymentNS']      =  $value->other_payments;
            $value->description ? $this->payroll['accrued'][$attribute][$count]['description_concept']  =  $value->description : '';
        } else { 
            $this->payroll['accrued'][$attribute][0]['quantity']        =  $value->quantity;
            $this->payroll['accrued'][$attribute][0]['payment']         =  $value->payment;
            $this->payroll['accrued'][$attribute][0]['paymentNS']       =  $value->other_payments;
            $value->description ? $this->payroll['accrued'][$attribute][0]['description_concept']  =  $value->description : '';
        }
    }

    public function severance($attribute, $value)
    {
        if(isset($this->payroll['accrued'][$attribute])) {
            $count = count($this->payroll['accrued'][$attribute]) + 1;
            $this->payroll['accrued'][$attribute][$count]['percentage']             =   $this->payroll['type_document_id'] != 109 ? $value->percentage: 0;
            $this->payroll['accrued'][$attribute][$count]['payment']                =  $this->payroll['type_document_id'] != 109 ?  $value->payment : 0;
            $this->payroll['accrued'][$attribute][$count]['interest_payment']       =  $value->other_payments;
        } else { 
            $this->payroll['accrued'][$attribute][0]['percentage']                  =  $value->percentage;
            $this->payroll['accrued'][$attribute][0]['payment']                     =  $value->payment;
            $this->payroll['accrued'][$attribute][0]['interest_payment']            =  $value->other_payments;
        }
    }

    public function deduction($id)
    {
        $model = new Deduction();
        $deductions = $model
        ->select([
            'type_deductions.domain as type_deduction_domain',
            'type_deductions.group as type_deduction_group',
            'type_deductions.element as type_deduction_element',
            'type_deductions.id as type_deduction_id',
            'deductions.payment',
            'deductions.percentage',
            'deductions.description',
            'deductions.type_law_deduction_id',
        ])
        ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id')
        ->where(['payroll_id' => $id])
        ->get()
        ->getResult();



        $this->payroll['deductions']['deductions_total'] = 0;
        $existEPS = false;
        $existPension = false;

        foreach($deductions as $item)
        {

            if($item->type_deduction_id == 1) {
                $existEPS = true;
            }
            if($item->type_deduction_id == 2) {
                $existPension = true;
            }
            switch($item->type_deduction_domain) {
                case 'atributo':
                    $this->attributeDeduction($item->type_deduction_group, $item->payment);
                    break;
                case 'array':
                    $this->arrayDeduction($item->type_deduction_group, $item);
                    break;
                case 'array_atributos':
                    $this->arrayAttributeDeduction($item->type_deduction_group, $item);
                    break;
                case 'eps':
                        $this->eps($item);
                    break;
                case 'pension':
                    $this->pension($item);
                    break;
                case 'fondossp':
                    $this->fondosSP($item);
                    break;
                case 'fondossp_sub':
                    $this->fondosSPSub($item);
                    break;
                case 'sanction':
                    $this->sanction($item);
                    break;
            }

            if($item->payment != null) {
                $this->payroll['deductions']['deductions_total'] += (double) $item->payment;
            }

          
        }
        if($existEPS == false) {
            $this->payroll['deductions']['eps_deduction']                                 =  0;
            $this->payroll['deductions']['eps_type_law_deductions_id']                    =  3;
        }
        if($existPension == false) {
            $this->payroll['deductions']['pension_deduction']                             = 0;
            $this->payroll['deductions']['pension_type_law_deductions_id']                = 5;
        }
    }

    public function eps( $value)
    {
        $this->payroll['deductions']['eps_deduction']                                 =  $value->payment;
        $this->payroll['deductions']['eps_type_law_deductions_id']                    =  $value->type_law_deduction_id;
    }

    public function pension($value)
    {
        $this->payroll['deductions']['pension_deduction']                             =  $value->payment;
        $this->payroll['deductions']['pension_type_law_deductions_id']                =  $value->type_law_deduction_id;
    }

    public function fondosSP($value) 
    {
        $this->payroll['deductions']['fondosp_deduction_SP']                           =  $value->payment;
        $this->payroll['deductions']['fondossp_type_law_deductions_id']                =  $value->type_law_deduction_id;
    }

    public function fondosSPSub($value) 
    {
        $this->payroll['deductions']['fondosp_deduction_sub']                          =  $value->payment;
        $this->payroll['deductions']['fondossp_sub_type_law_deductions_id']            =  $value->type_law_deduction_id;
    }

    public function sanction($value)
    {
        if(!isset($this->payroll['deductions']['sanctions'])) {
            $i = 0;
        }else {
            $i = count($this->payroll['deductions']['sanctions']) - 1;
            $i++;
        }
        if($value->type_deduction_element == 'public_sanction') {
            $this->payroll['deductions']['sanctions'][$i]['public_sanction']  =   $value->payment;
            $this->payroll['deductions']['sanctions'][$i]['private_sanction'] =  0;
            $value->description ? $this->payroll['deductions']['sanctions'][$i]['description_concept']  =  $value->description : '';
        } else if($value->type_deduction_element == 'private_sanction') {
            $this->payroll['deductions']['sanctions'][$i]['private_sanction'] = $value->payment;
            $this->payroll['deductions']['sanctions'][$i]['public_sanction']  =  0;
            $value->description ? $this->payroll['accrued']['sanctions'][$i]['description_concept']  =  $value->description : '';
        }


    }

    public function arrayDeduction($attribute, $value) 
    {
        if(isset($this->payroll['deductions'][$attribute])) {
            $count = count($this->payroll['deductions'][$attribute]) + 1;
            $value->percentage ?  $this->payroll['deductions'][$attribute][$count]['percentage']         =  $value->percentage : '';
            $value->payment ? $this->payroll['deductions'][$attribute][$count]['deduction']              =  $value->payment : '';
            $value->description ?$this->payroll['deductions'][$attribute][$count]['description']         =  $value->description : '';
            
        }else {
            $value->percentage ? $this->payroll['deductions'][$attribute][0]['percentage']          =  $value->percentage : '';
            $value->payment ? $this->payroll['deductions'][$attribute][0]['deduction']                 =  $value->payment: '';
            $value->description ? $this->payroll['deductions'][$attribute][0]['description']         =  $value->description : '';
        }
    }

    public function arrayAttributeDeduction($attribute, $value) { 
        if(isset($this->payroll['deductions'][$attribute])) {
            $count = count($this->payroll['deductions'][$attribute]);
            $this->payroll['deductions'][$attribute][$count][$value->type_deduction_element]        =  $value->payment;
            $value->description ? $this->payroll['deductions'][$attribute][$count]['description_concept']  =  $value->description : '';
        }else {
            $this->payroll['deductions'][$attribute][0][$value->type_deduction_element]             =  $value->payment;
            $value->description ? $this->payroll['deductions'][$attribute][0]['description_concept']  =  $value->description : '';
        }
    }


    public function attributeDeduction($attribute, $value) {
        $this->payroll['deductions'][$attribute] = $value;
    }

    public function group($id, $resolution, $company_id = null)
    {
  
        $model = new Invoice();
        $invoice = $model->select([
            'periods.month',
            'invoices.id',
            'payrolls.id as payroll_id',
            'invoices.created_at',
            'invoices.resolution',
            'invoices.resolution_id',
            'invoices.type_documents_id',
            'invoices.invoice_status_id',
            'invoices.prefix',
            'invoices.notes',
            'invoices.uuid',
            'invoices.issue_date',
            'invoices.customers_id',
            'invoices.resolution_credit',
            'type_documents.name as type_document_name',
            'customers.id as customer_id',
            'customers.name',
            'customers.municipality_id',
            'customer_worker.second_name',
            'customer_worker.surname',
            'customer_worker.second_surname',
            'customer_worker.worker_code',
            'customer_worker.type_worker_id',
            'customer_worker.sub_type_worker_id',
            'customer_worker.admision_date',
            'customer_worker.retirement_date',
            'customer_worker.type_contract_id',
            'customer_worker.high_risk_pension',
            'customers.address',
            'customer_worker.integral_salary',
            'customer_worker.salary',
            'customer_worker.work',
            'customer_worker.account_number',
            'customers.name as first_name',
            'customer_worker.second_name',
            'customers.identification_number',
            'type_document_identifications.id as type_document_identification_id',
            'invoice_status.name as invoice_status',
            'customer_worker.payroll_period_id',
            'payrolls.settlement_start_date',
            'payrolls.settlement_end_date',
            'payrolls.worked_time',
            'customer_worker.payment_method_id',
            'banks.name as bank_name',
            'customer_worker.account_number',
            'bank_account_types.name as account_type',
            'payrolls.type_payroll_adjust_note_id'

        ])
        ->join('type_documents', 'type_documents.id = invoices.type_documents_id')
        ->join('payrolls', 'invoices.id = payrolls.invoice_id')
        ->join('periods', 'periods.id = payrolls.period_id')
        ->join('customers', 'customers.id = invoices.customers_id')
        ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
        ->join('customer_worker', 'customer_worker.customer_id = customers.id', 'left')
        ->join('banks', 'customer_worker.bank_id = banks.id', 'left')
        ->join('bank_account_types', 'customer_worker.bank_account_type_id = bank_account_types.id', 'left')
        ->join('invoice_status', 'invoice_status.id = invoices.invoice_status_id')
        ->whereIn('invoices.type_documents_id', [9, 10, 109, 110])
        ->where(['invoices.id' => $id])
        ->asObject()
        ->first();

    

        if($invoice->type_documents_id != '109') {
            $this->resolution($resolution, $company_id, $id, $invoice->type_documents_id);
        }
       
        $this->general($invoice);
        if($invoice->type_documents_id == '10' ) {
  	        if($invoice->type_payroll_adjust_note_id == 2) {
                $this->general($invoice);
                $this->predecessor($invoice);
      		    $this->period($invoice);
   		        $this->accrued($invoice->payroll_id);
 		        $this->deduction($invoice->payroll_id);
       		    $this->payment($invoice);
                $this->paymentDates($invoice->id);
            }else {
                $this->predecessor($invoice);
                $this->period($invoice);
                $this->worker($invoice);
                $this->accrued($invoice->payroll_id);
                $this->deduction($invoice->payroll_id);
                $this->payment($invoice);
                $this->paymentDates($invoice->id);
            }       
	    } else {
            $this->period($invoice);
            $this->payment($invoice);
            $this->paymentDates($invoice->id);
            $this->accrued($invoice->payroll_id);
            $this->deduction($invoice->payroll_id);
          
        }
     
        return $this->payroll;
    }


}