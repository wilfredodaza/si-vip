<?php

namespace App\Controllers;

use App\Models\SubPeriod;
use App\Models\Period;
use App\Models\Customer;
use App\Models\Invoice;
use App\Controllers\Api\Auth;
use App\Models\Accrued;
use App\Models\Cargue;
use App\Models\Company;
use App\Models\Payroll;
use App\Models\PayrollDate;
use App\Models\Resolution;
use App\Models\TypeDocumentIdentifications;


class PeriodAdjustController extends BaseController 
{

    private $months = [
        'Enero'             => '01', 
        'Febrero'           => '02', 
        'Marzo'             => '03', 
        'Abril'             => '04', 
        'Mayo'              => '05', 
        'Junio'             => '06', 
        'Julio'             => '07', 
        'Agosto'            => '08', 
        'Septiembre'        => '09',
        'Octubre'           => '10',
        'Noviembre'         => '11',
        'Diciembre'         => '12'
    ];

     /**
     * View de create payroll adjust
     * @return string
     */
    public function index() 
    {
        $model = new SubPeriod();
        $model->select(['sub_periods.id', 
            'periods.id as period_id',
            'periods.month', 
            'periods.year'
            ])
            ->join('payrolls', 'sub_periods.id = payrolls.sub_period_id')
            ->join('periods', 'periods.id = payrolls.period_id')
            ->join('invoices', 'invoices.id = payrolls.invoice_id')
            ->where([
            'periods.deleted_at ='              => null,
            'invoices.companies_id'             => Auth::querys()->companies_id,
            'invoices.type_documents_id'        => 10
        ])
        ->groupBy(['periods.id'])
        ->orderBy('periods.id', 'DESC');
        if(count($this->search()) != 0) {
            $model->where($this->search());
        }

        $model->groupBy(['sub_periods.id'])
        ->orderBy('periods.id', 'DESC');

        $periods = $model->asObject();
        $data    = $model->asObject();

        
   

 
        $dataPeriod = [];
        $i= 0;
        foreach($data->paginate(10) as $item) {
          
            $workerModel = new Payroll();
            $workers = $workerModel
            ->select('count(payrolls.id) as workers')
            ->join('invoices', 'payrolls.invoice_id = invoices.id')
            ->join('periods', 'periods.id = payrolls.period_id')
            ->where([
                'payrolls.sub_period_id'            => $item->id,
                'invoices.companies_id'             => Auth::querys()->companies_id,
                'invoices.type_documents_id'        => 10
            ])
            ->asObject()
            ->first();


            $emiterModel = new  Payroll();
            $emiter = $emiterModel
            ->select('count(invoices.invoice_status_id) as emiter')
            ->join('invoices', 'payrolls.invoice_id = invoices.id')
            ->where([
                'payrolls.sub_period_id'            => $item->id,
                'invoices.invoice_status_id'        => 14,
                'invoices.type_documents_id'        => 10,
                'invoices.companies_id'             => Auth::querys()->companies_id
            ])
            ->asObject()
            ->first();

            if(is_null($emiter)) {
               $emiter = (Object) $emiter['emiter'] = 0;
            }


            $errorsModel = new  Payroll();
            $errors = $errorsModel
            ->select('count(invoices.invoice_status_id) as errors')
            ->join('invoices', 'payrolls.invoice_id = invoices.id')
            ->where([
                'payrolls.sub_period_id'            => $item->id,
                'invoices.invoice_status_id'        => 15,
                'invoices.type_documents_id'        => 10,
                'invoices.companies_id'             => Auth::querys()->companies_id
            ])
            ->asObject()
            ->first();

            if(is_null($errors)) {
                $errors = (Object) $errors['errors'] = 0;
            }

            $forEmiterModel = new  Payroll();
            $forEmiter = $forEmiterModel
            ->select('count(invoices.invoice_status_id) as for_emiter')
            ->join('invoices', 'payrolls.invoice_id = invoices.id')
            ->where([
                'payrolls.sub_period_id'                => $item->id,
                'invoices.companies_id'                 => Auth::querys()->companies_id,
                'invoices.type_documents_id'            => 10
            ])
            ->whereIn('invoices.invoice_status_id', [12, 13])
            ->asObject()
            ->first();


            $dataPeriod[$i] = (Object) [
                'workers'           => $workers->workers,
                'emiter'            => $emiter->emiter,
                'errors'            => $errors->errors,
                'for_emiter'        => $forEmiter->for_emiter,
                'month'             => $item->month,
                'year'              => $item->year,
                'id'                => $item->id

            ];
            $i++;
           
        }

        $model = new Payroll();
        $payrolls = $model->select(['period_id'])->groupBy('period_id')->asObject()->get()->getResult();

        $periodDue = [];
        foreach ($payrolls as $item) {
            array_push($periodDue, $item->period_id);
        }
        $model          = new Period();
        $selectPeriods  =   $model->get()->getResult();
    

      
    
        return view('period_adjust/index', [
            'periods' => $dataPeriod,
            'pager'   => $periods->pager,
            'search'  => $this->search(),
            'select'  => $selectPeriods,
            'periodDue' => $periodDue
            ]);
    }

   

    public function show($id = null)
    {
        $model = new Payroll();
        $model->select([
            'pay.id',
            'type_document_identifications.name as type_document_identification_name', 
            'customers.name',
            'customers.id as customer_id',
            'customers.identification_number',
            'customer_worker.second_name', 
            'customer_worker.surname',
            'customer_worker.second_surname',
            'invoices.prefix',
            'invoices.resolution',
            'invoices.id as invoice_id',
            'invoices.invoice_status_id',
            'invoice_status.name as invoice_status_name',
            'invoices.errors',
            'invoices.uuid',
            'pay.period_id'
            ])
        ->from('payrolls as pay')
        ->join('invoices', 'invoices.id = pay.invoice_id')
        ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
        ->join('customers', 'customers.id = invoices.customers_id')
        ->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
        ->where([
            'pay.sub_period_id'                         => $id,
            'customers.companies_id'                    => Auth::querys()->companies_id,
            'customers.type_customer_id'                => 3,
            'customers.deleted_at'                      => null,
            'type_documents_id'                         => 10
        ]);
        if(count($this->searchShow()) != 0) {
            $model->where($this->searchShow());
        }
        $workerData = $model->groupBy([
            'pay.id', 
            'customer_worker.id',
            'invoice_status.id'
            ])
            ->asObject()
            ->get()
            ->getResult();

        if(count($workerData) == 0) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }


    

    
        $model = new Period();
        $period = $model->select([
            'per.id', 
            'per.month', 
            'per.year'
        ])
        ->from('periods as per')
        ->where(['per.id' => $workerData[0]->period_id])
        ->groupBy(['per.id'])
        ->asObject()
        ->get()
        ->getResult();
        

        $periodData = [
            'accrueds'      => 0,
            'deductions'    => 0,
            'workers'       => 0,
            'emiter'        => 0,
            'errors'        => 0,
            'for_emiter'    => 0,
            'total'         => 0,
        ];
        $i = 0;
        $workers = [];
        foreach ($workerData as $worker) {
            $payroll = new Payroll();
            $accrued = $payroll->select('sum(accrueds.payment + IFNULL( accrueds.other_payments,0))  AS accrued')
                ->join('accrueds', 'accrueds.payroll_id = payrolls.id' , 'left')
                ->where(['payrolls.id' => $worker->id])
                ->groupBy('payrolls.id')
                ->asObject()
                ->first();

            $payroll = new Payroll();
            $deduction = $payroll->select('sum(deductions.payment) AS deduction')
                ->join('deductions', 'deductions.payroll_id = payrolls.id', 'left' )
                ->where(['payrolls.id' => $worker->id])
                ->groupBy('payrolls.id')
                ->asObject()
                ->first();

            $workers[$i] = (Array) $worker;
            $workers[$i]['accrued'] = isset($accrued->accrued) ? $accrued->accrued : 0;
            $workers[$i]['deduction'] = isset($deduction->deduction) ? $deduction->deduction : 0;

            $periodData['accrueds']     +=  isset($accrued->accrued) ? $accrued->accrued : 0;
            $periodData['deductions']   += isset($deduction->deduction) ? $deduction->deduction : 0;
            if($worker->invoice_status_id == 12 || $worker->invoice_status_id == 13 || $worker->invoice_status_id == 16) {
                $periodData['for_emiter'] = $periodData['for_emiter'] + 1;
            }
            if($worker->invoice_status_id == 14) {
                $periodData['emiter'] = $periodData['emiter'] + 1;
            }

            if($worker->invoice_status_id == 15) {
                $periodData['errors'] = $periodData['errors'] + 1;
            }
            $periodData['workers'] = $periodData['workers'] + 1;

            $i++;


        }

        $periodData['total'] = $periodData['accrueds'] - $periodData['deductions'];
        $model = new Resolution();
        $resolutions = $model->where([
            'companies_id'          => Auth::querys()->companies_id, 
            'type_documents_id'     => 9
            ])
            ->get()
            ->getResult();

        
        $model = new TypeDocumentIdentifications();
        $typeDocumentIdentifications = $model
            ->asObject()
            ->get()
            ->getResult();


  

        $customerId = [];
        foreach($workerData as $item){
            array_push($customerId, $item->customer_id);
        }
        
     

        if(count($customerId) != 0) {
            $model = new Customer();
            $customers = $model
            ->select(['customers.*', 'customer_worker.*', 'customers.id as customer_id'])
            ->join('customer_worker', 'customer_worker.customer_id = customers.id')
            ->where([
                'customers.type_customer_id'            => 3,
                'customers.status'                      => 'Activo',
      		    'customers.companies_id'                => Auth::querys()->companies_id,
            ])
            ->whereNotIn('customers.id', $customerId)
            ->get()
            ->getResult();
        }else {
            $customers = [];
        }

        return view('period_adjust/show', [
            'workers'                       => $workers,
            'resolutions'                   => $resolutions,
            'period'                        => $period,
            'id'                            => $id,
            'typeDocumentIdentifications'   => $typeDocumentIdentifications,
            'searchShow'                    => $this->searchShow(),
            'customers'                     => $customers,
            'periodData'                    => $periodData
        ]);
    }


    private function dataLastMonthDay($month, $year) 
    { 
        
        $month  = $this->months[$month];
        $year   = $year;
        $day    = date("d", mktime(0,0,0, $month+1, 0, $year));
        return date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    }


    private function dataFirstMonthDay($month, $year) 
    {
        $month  = $this->months[$month];
        $year   = $year;
        return date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    }



    public function search()
    {
        $data = [];
        if(!empty($this->request->getGet('period_id'))) {
            $data['per.id'] = $this->request->getGet('period_id');
        }


        return $data;
    }


    public function searchShow()
    {
        $data = [];
        if(!empty($this->request->getGet('first_name'))) {
            $data['customers.name'] = $this->request->getGet('first_name');
        }

        if(!empty($this->request->getGet('second_name'))) {
            $data['customer_worker.second_name'] = $this->request->getGet('second_name');
        }
        if(!empty($this->request->getGet('surname'))) {
            $data['customer_worker.surname'] = $this->request->getGet('surname');
        }
        if(!empty($this->request->getGet('second_surname'))) {
            $data['customer_worker.second_surname'] = $this->request->getGet('second_surname');
        }

        if(!empty($this->request->getGet('type_document_id'))) { 
            $data['customers.type_document_identifications_id'] = $this->request->getGet('type_document_id');
        }

        if(!empty($this->request->getGet('identification_number'))) { 
            $data['customers.identification_number'] = $this->request->getGet('identification_number');
        }


        return $data;
    }

    public function addWorker($id = null) 
    {

        $model  = new Period();
        $period = $model->select([
            'invoices.id',
            'payrolls.settlement_start_date',
            'payrolls.settlement_end_date'
        ])->join('payrolls', 'payrolls.period_id = periods.id')
        ->join('invoices', 'payrolls.invoice_id = invoices.id')
        ->where(['periods.id' => $id])
        ->asObject()
        ->first();

	

        

        $model = new PayrollDate();
        $payrollDates = $model
	    ->select(['payroll_date'])
	    ->join('invoices', 'invoices.id = payroll_dates.invoice_id')
        ->join('payrolls', 'invoices.id = payrolls.invoice_id')
        ->where(['payrolls.period_id' => $id, 'companies_id' => Auth::querys()->companies_id])
        ->get(1)
        ->getResult();


        $customer = $this->request->getPost('customer_id');
    

      

        $model = new Invoice();
        $invoice = $model->insert([
            'companies_id'      => Auth::querys()->companies_id,
            'created_at'        => date('Y-m-d H:i:s'),
            'issue_date'        => date('Y-m-d'),
            'type_documents_id' => 10,
            'invoice_status_id' => 12,
            'user_id'           => Auth::querys()->id,
            'customers_id'      => $customer
        ]);

        foreach($payrollDates as $item) {
            $model = new PayrollDate();
            $model->insert([
                'invoice_id'    => $invoice,
                'payroll_date'  => $item->payroll_date
            ]);
        }
 


        $model = new Payroll();
        $model->insert([
            'invoice_id'            => $invoice,
            'settlement_start_date' => $period->settlement_start_date,
            'settlement_end_date'   => $period->settlement_end_date,
            'worked_time'           => '30',
            'period_id'             => $id,
	        'worked_time'	        => 0
        ]);


        return redirect()->to(base_url('periods/'. $id))->with('success', 'El empleado fue agregado exitosamente.');

    }

    public function delete($id) 
    {

        $emiterModel = new  Payroll();
        $emiter = $emiterModel->select('count(invoices.invoice_status_id) as emiter')
        ->join('invoices', 'payrolls.invoice_id = invoices.id')
        ->where([
            'payrolls.period_id'                => $id,
            'invoices.invoice_status_id'        => 14,
            'invoices.type_documents_id'        => 10,
            'invoices.companies_id'             => Auth::querys()->companies_id
        ])
        ->asObject()
        ->first();

            

        if(isset($emiter->emiter) &&  $emiter->emiter == '0') {
            try{

                $model = new Company();
                $company = $model->select(['identification_number'])
                ->asObject()
                ->find(Auth::querys()->companies_id);
                
                $sql = "UPDATE `invoices` INNER JOIN `payrolls` ON `payrolls`.`invoice_id` = `invoices`.`id` SET `invoices`.`companies_id` = NULL, `payrolls`.`period_id` = NULL,   `invoices`.`deleted_at` = '".date('Y-m-d H:s:i')."' WHERE `invoices`.`type_documents_id` = 10 AND `invoices`.`companies_id` = ".Auth::querys()->companies_id." AND `invoices`.`invoice_status_id` IN  (12, 13) AND `payrolls`.`period_id` = ".$id.";";
                $db = db_connect();
                $db->reconnect();
                $db->query($sql);
                $db->close();

                $model = new Cargue();
                $model->where(['period_id' => $id, 'nit' => $company->identification_number])
                ->delete();

                return redirect()->to(base_url('period_adjusts'))->with('success', 'La nomina de ajuste fue eliminada correctamente.');
            }catch(\mysqli_sql_exception $e) {
               return redirect()->to(base_url('period_adjusts'))->with('errors', 'no se puede eliminar la  nomina de ajuste  ya se encuentra en proceso.');
            }
        } else {
            return redirect()->to(base_url('period_adjusts'))->with('errors', 'no se puede eliminar la  nomina de ajuste  ya se encuentra en proceso.');
        }
    }


}