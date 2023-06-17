<?php

namespace App\Controllers;

use App\Controllers\Api\Auth;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Payroll;
use App\Models\Accrued;
use App\Models\Deduction;
use App\Models\Invoice;
use App\Models\PayrollDate;
use App\Models\Period;
use App\Models\SubPeriod;
use App\Models\TypeDocumentIdentifications;
use App\Traits\PayrollTrait;
use App\Traits\RequestAPITrait;
use App\Traits\ValidateResponseAPITrait;
use CodeIgniter\API\ResponseTrait;


class PayrollRemovableController extends BaseController
{
    use PayrollTrait, ResponseTrait, RequestAPITrait, ValidateResponseAPITrait;

    private $mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre','Diciembre'];


    public function index()
    {

        $model = new SubPeriod();
        $data = $model
           ->select([
               'periods.month',
               'periods.year',
               'sub_periods.id',
               'sub_periods.name',
               '(SELECT count(*)  FROM  payrolls as pay2  where pay.sub_period_id = pay2.sub_period_id) as total',
               '(SELECT count(*)  FROM  payrolls as pay2 
               INNER JOIN invoices ON invoices.id = pay2.invoice_id 
               WHERE pay.sub_period_id = pay2.sub_period_id AND invoices.invoice_status_id = 17  AND invoices.companies_id = "'.Auth::querys()->companies_id.'") as emitir',
               '(SELECT count(*)  FROM  payrolls as pay2 
               INNER JOIN invoices ON invoices.id = pay2.invoice_id 
               WHERE pay.sub_period_id = pay2.sub_period_id AND invoices.invoice_status_id = 18  AND invoices.companies_id = "'.Auth::querys()->companies_id.'") as consolidado',
               '(SELECT count(*)  FROM  payrolls as pay2 
               INNER JOIN invoices ON invoices.id = pay2.invoice_id 
               WHERE pay.period_id = pay2.period_id AND invoices.invoice_status_id = 14 AND invoices.type_documents_id = 9  AND invoices.companies_id = "'.Auth::querys()->companies_id.'") as send_DIAN',
               '(SELECT count(*)  FROM  payrolls as pay2 
               INNER JOIN invoices ON invoices.id = pay2.invoice_id 
               WHERE pay.period_id = pay2.period_id AND invoices.type_documents_id = 9 AND invoices.companies_id = "'.Auth::querys()->companies_id.'") as por_emitir_DIAN',
               '(SELECT IF (periods.month != "Enero",
                sum((SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id  GROUP BY pay2.id)),
                sum((SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id NOT IN (16)  GROUP BY pay2.id)))
                ) as accrueds
               ',
                   
               'sum((SELECT IFNULL(sum(ded2.payment),0) FROM   payrolls pay2 LEFT JOIN  deductions ded2 ON  ded2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id)) AS deductions',
               ])
           ->join('payrolls as pay', 'pay.sub_period_id = sub_periods.id', 'left')
            ->join('periods', 'pay.period_id = periods.id', 'left')
           ->join('invoices', 'invoices.id = pay.invoice_id' , 'left')
            ->where([
                'invoices.companies_id'         => Auth::querys()->companies_id,
                'sub_periods.company_id'        => Auth::querys()->companies_id,
                'invoices.type_documents_id'    => 109
            ])->groupBy(['sub_periods.id','pay.sub_period_id','pay.period_id'])
            ->orderBy('id', 'desc')
            ->asObject();

        $model = new Period();
        $periods = $model
            ->where(['deleted_at' => null])
            ->get()
            ->getResult();

        return view('payroll_renovable/index', [
            'detachables'                   => $data->paginate(10),
            'pager'                         => $data->pager,
            'periods'                       => $periods
        ]);
    }

    public function show($id = null)
    {
        $model = new SubPeriod();
        $model =  $model
            ->select([
                'sub_periods.id',
                'pay.id as payroll_id',
                'invoices.id as invoice_id',
                'invoices.invoice_status_id',
                'type_document_identifications.name as type_document_identification_name',
                'cus.name as first_name',
                'cus.identification_number',
                'customer_worker.second_name',
                'customer_worker.surname',
                'customer_worker.second_surname',
                'sub_periods.name',
                '(SELECT IF(invoices.invoice_status_id = 14, "TRUE", "FALSE")  as validate FROM invoices
                    INNER JOIN customers as cus2 ON cus2.id = invoices.customers_id
                    INNER JOIN payrolls as pay2 ON invoices.id = pay2.invoice_id
                    where pay2.period_id = pay.period_id AND cus2.id = cus.id AND invoices.type_documents_id = 9  LIMIT 1
                ) as validate',
               '(SELECT IF (periods.month != "Enero",
                (SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id  GROUP BY pay2.id),
                (SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id NOT IN (16)  GROUP BY pay2.id)
               )) as accrueds',
                '(SELECT IFNULL(sum(ded2.payment),0)
                FROM   payrolls pay2 LEFT JOIN  deductions ded2 ON  ded2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id) AS deductions',
            ])
            ->join('payrolls as pay', 'pay.sub_period_id = sub_periods.id', 'left')
            ->join('periods', 'pay.period_id = periods.id', 'left')
            ->join('invoices', 'invoices.id = pay.invoice_id' , 'left')
            ->join('customers as cus', 'invoices.customers_id = cus.id' , 'left')
            ->join('type_document_identifications', 'type_document_identifications.id = cus.type_document_identifications_id' , 'left')
            ->join('customer_worker', 'cus.id = customer_worker.customer_id')
            ->where([
                'sub_periods.company_id'        => Auth::querys()->companies_id,
                'invoices.type_documents_id'    => 109,
                'sub_periods.id'                => $id
            ]);
        if(count($this->searchShow()) != 0) {
            $model = $model->where($this->searchShow());
        }
        $data = $model->groupBy([
                'sub_periods.id',
                'pay.id',
                'customer_worker.id'
            ])
            ->asObject();


        $model = new TypeDocumentIdentifications();
        $typeDocumentIdentifications = $model
            ->asObject()
            ->get()
            ->getResult();


      return view('payroll_renovable/show', [
          'detachables'                   => $data->paginate(10),
          'pager'                         => $data->pager,
          'typeDocumentIdentifications'   => $typeDocumentIdentifications,
          'searchShow'                    => $this->searchShow(),
          'id'                            => $id
      ]);
    }

    public function edit($id = null)
    {
        return view('payroll_renovable/edit', ['id' => $id]);
    }

    public function consolidate()
    {
        $db = db_connect();
        $db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

        $periodID  = $this->request->getPost('period_id');

        $model = new Period();
        $period = $model->where(['id' => $periodID])
        ->asObject()
        ->first();

        $mes = (array_search($period->month, $this->mes) + 1) < 10 ? '0'.(array_search($period->month, $this->mes) + 1) : (array_search($period->month, $this->mes) + 1);

        $date           = new \DateTime( $period->year.'-'.$mes.'-01' ); 
        $date_start     = $date->format( 'Y-m-d');
        $date_end       = $date->format( 'Y-m-t');

  

        $model = new Invoice();
        $existPeriod = $model
            ->join('payrolls', 'payrolls.invoice_id = invoices.id')
            ->where([
            'invoices.invoice_status_id'    => 18,
            'payrolls.period_id'            =>  $periodID,
            'invoices.companies_id'         => Auth::querys()->companies_id
        ])->countAllResults();


        if($existPeriod == 0) {
            $model = new Invoice();
            $customers = $model->select([
                'invoices.customers_id',
                'invoices.companies_id',
                'payrolls.period_id',
                'sum(payrolls.worked_time) as worked_time'
            ])
                ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                ->where([
                    'invoices.companies_id' => Auth::querys()->companies_id,
                    'invoices.invoice_status_id' => 17,
                    'payrolls.period_id' => $periodID,
                ])
                ->whereIn('invoices.type_documents_id', [109])
                ->groupBy('invoices.customers_id')
                ->distinct()
                ->asArray()
                ->get()
                ->getResult();

            $model = new Invoice();
            $invoiceUpdate = $model->select([
                'invoices.id',
            ])
                ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                ->where([
                    'invoices.companies_id' => Auth::querys()->companies_id,
                    'invoices.invoice_status_id' => 17,
                    'payrolls.period_id' => $periodID
                ])
                ->whereIn('invoices.type_documents_id', [109])
                ->asObject()
                ->get()
                ->getResult();

            foreach ($invoiceUpdate as $item) {
                $invoice = new Invoice();
                $invoice->update($item->id, ['invoice_status_id' => 18]);
            }

            if (count($invoiceUpdate) != 0) {
                foreach ($customers as $item) {
                    $invoice = new Invoice();
                    $invoiceId = $invoice->insert([
                        'type_documents_id' => 9,
                        'invoice_status_id' => 13,
                        'customers_id' => $item->customers_id,
                        'companies_id' => $item->companies_id,
                        'status_wallet' => 'Pendiente',
                        'send' => 'True'
                    ]);

                    $model = new Invoice();
                    $customerDates = $model->select([
                        'payroll_dates.payroll_date',
                    ])
                        ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                        ->join('payroll_dates', 'invoices.id = payroll_dates.invoice_id')
                        ->where([
                            'invoices.companies_id' => Auth::querys()->companies_id,
                            'invoices.customers_id' => $item->customers_id,
                            'payrolls.period_id' => $periodID,
                        ])->asObject()
                        ->groupBy('payroll_dates.payroll_date')
                        ->get()
                        ->getResult();

                    foreach ($customerDates as $date) {
                        $model = new PayrollDate();
                        $model->insert(['invoice_id' => $invoiceId, 'payroll_date' => $date->payroll_date]);
                    }

                    $payroll = new Payroll();
                    $payrollId          = $payroll->insert([
                        'invoice_id'                    => $invoiceId,
                        'period_id'                     => $item->period_id,
                        'type_payroll_adjust_note_id'   => NULL,
                        'settlement_start_date'         => $date_start,
                        'settlement_end_date'           => $date_end,
                        'worked_time'                   => $item->worked_time
                    ]);


                    $model = new Accrued();
                    $accrueds = $model
                        ->select([
                            'sum(accrueds.payment) as payment',
                            'sum(accrueds.other_payments) as other_payments',
                            'accrueds.description',
                            'accrueds.type_overtime_surcharge_id',
                            'accrueds.type_disability_id',
                            'accrueds.quantity',
                            'accrueds.percentage',
                            'accrueds.type_accrued_id'
                        ])
                        ->join('payrolls', 'payrolls.id = accrueds.payroll_id', 'right')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id', 'left')
                        ->join('type_accrueds', 'type_accrueds.id = accrueds.type_accrued_id', 'left')
                        ->where([
                            'invoices.customers_id' => $item->customers_id,
                            'invoices.type_documents_id' => 109,
                            'payrolls.period_id' => $periodID
                        ])
                        ->whereIn('type_accrueds.domain', ['atributo', 'severance'])
                        ->groupBy(['accrueds.type_accrued_id'])
                        ->get()
                        ->getResult();

                    foreach ($accrueds as $itemAccrued) {
                        $model = new Accrued();
                        $model->insert([
                            'payroll_id' => $payrollId,
                            'type_accrued_id' => $itemAccrued->type_accrued_id,
                            'type_overtime_surcharge_id' => NULL,
                            'type_disability_id' => NULL,
                            'start_time' => NULL,
                            'end_time' => NULL,
                            'payment' => $itemAccrued->payment,
                            'quantity' => $itemAccrued->quantity,
                            'percentage' => $itemAccrued->percentage,
                            'description' => $itemAccrued->description,
                            'other_payments' => $itemAccrued->other_payments
                        ]);
                    }

                    $model = new Accrued();
                    $accrueds = $model
                        ->select(['accrueds.*'])
                        ->join('payrolls', 'payrolls.id = accrueds.payroll_id', 'right')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id', 'left')
                        ->join('type_accrueds', 'type_accrueds.id = accrueds.type_accrued_id', 'left')
                        ->where([
                            'invoices.customers_id' => $item->customers_id,
                            'invoices.type_documents_id' => 109,
                            'payrolls.period_id' => $periodID
                        ])
                        ->whereNotIn('type_accrueds.domain', ['atributo', 'severance'])
                        ->get()
                        ->getResult();


                    foreach ($accrueds as $itemAccrued) {
                        $model = new Accrued();
                        $model->insert([
                            'payroll_id' => $payrollId,
                            'type_accrued_id' => $itemAccrued->type_accrued_id,
                            'type_overtime_surcharge_id' => $itemAccrued->type_overtime_surcharge_id,
                            'payment' => $itemAccrued->payment,
                            'type_disability_id' => $itemAccrued->type_disability_id,
                            'start_time' => $itemAccrued->start_time,
                            'end_time' => $itemAccrued->end_time,
                            'quantity' => $itemAccrued->quantity,
                            'percentage' => $itemAccrued->percentage,
                            'description' => $itemAccrued->description,
                            'other_payments' => $itemAccrued->other_payments,
                        ]);
                    }


                    $model = new Deduction();
                    $deductions = $model
                        ->select(['deductions.*', 'sum(deductions.payment) as payment', 'deductions.description', 'type_deductions.domain'])
                        ->join('payrolls', 'payrolls.id = deductions.payroll_id', 'left')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id', 'left')
                        ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id', 'left')
                   //     ->whereIn('type_deductions.group', [ 'eps', 'pension', 'voluntary_pension', 'withholding_at_source', 'afc', 'cooperative', 'tax_liens', 'supplementary_plan', 'education', 'refund', 'debt'])
                          ->whereIn('type_deductions.domain', ['eps', 'pension', 'fondossp', 'fondossp_sub', 'atributo'])
			->where([
                            'invoices.customers_id' => $item->customers_id,
                            'invoices.type_documents_id' => 109,
                            'payrolls.period_id' => $periodID
                        ])
                        ->groupBy(['type_deductions.domain'])
                        ->get()
                        ->getResult();

                    foreach ($deductions as $itemDeductions) {
                        $model = new Deduction();
                        $model->insert([
                            'payroll_id' => $payrollId,
                            'type_deduction_id' => $itemDeductions->type_deduction_id,
                            'type_law_deduction_id' => $itemDeductions->type_law_deduction_id,
                            'payment' => $itemDeductions->payment,
                            'percentage' => $itemDeductions->percentage,
                            'description' => $itemDeductions->description
                        ]);
                    }

                    $model = new Deduction();
                    $deductions = $model
                        ->select('deductions.*')
                        ->join('payrolls', 'payrolls.id = deductions.payroll_id')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id')
                        ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id', 'left')
                       	->whereNotIn('type_deductions.domain', ['eps', 'pension', 'fondossp', 'fondossp_sub', 'atributo'])
			->where([                            'invoices.customers_id' => $item->customers_id,
                            'invoices.type_documents_id' => 109,
                            'payrolls.period_id' => $periodID
                        ])
                        ->get()
                        ->getResult();


                    foreach ($deductions as $itemDeductions) {
                        $model = new Deduction();
                        $model->insert([
                            'payroll_id' => $payrollId,
                            'type_deduction_id' => $itemDeductions->type_deduction_id,
                            'type_law_deduction_id' => $itemDeductions->type_law_deduction_id,
                            'payment' => $itemDeductions->payment,
                            'percentage' => $itemDeductions->percentage,
                            'description' => $itemDeductions->description
                        ]);
                    }
                }

            }
        }else {
            return redirect()->to(base_url('payroll_removable'))->with('errors', 'El periodo ya se encuentra consolidado.');
        }
        return redirect()->to(base_url('payroll_removable'))->with('success', 'El periodo fue consolidado correctamente.');

    }

    public function consolidateReverse($periodID = null)
    {
        $db = db_connect();
        $db->query("SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

     

        $model = new Period();
        $period = $model->where(['id' => $periodID])
        ->asObject()
        ->first();


        $model = new SubPeriod();
        $idSuperiod = $model->insert(['name' =>  'Nueva', 'company_id' => Auth::querys()->companies_id ]);

        $mes = (array_search($period->month, $this->mes) + 1) < 10 ? '0'.(array_search($period->month, $this->mes) + 1) : (array_search($period->month, $this->mes) + 1);

        $date           = new \DateTime( $period->year.'-'.$mes.'-01' ); 
        $date_start     = $date->format( 'Y-m-d');
        $date_end       = $date->format( 'Y-m-t');

  

        $model = new Invoice();
        $existPeriod = $model
            ->join('payrolls', 'payrolls.invoice_id = invoices.id')
            ->where([
            'invoices.invoice_status_id'    => 14,
            'payrolls.period_id'            => $periodID,
            'invoices.companies_id'         => Auth::querys()->companies_id
        ])->countAllResults();


   
            $model = new Invoice();
            $customers = $model->select([
                'invoices.customers_id',
                'invoices.companies_id',
                'payrolls.period_id',
                'sum(payrolls.worked_time) as worked_time'
            ])
                ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                ->where([
                    'invoices.companies_id'         => Auth::querys()->companies_id,
                    'invoices.invoice_status_id'    => 14,
                    'payrolls.period_id'            => $periodID,
                ])
                ->whereIn('invoices.type_documents_id', [9])
                ->groupBy('invoices.customers_id')
                ->distinct()
                ->asArray()
                ->get()
                ->getResult();

            $model = new Invoice();
            $invoiceUpdate = $model->select([
                'invoices.id',
            ])
                ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                ->where([
                    'invoices.companies_id'         => Auth::querys()->companies_id,
                    'invoices.invoice_status_id'    => 14,
                    'payrolls.period_id'            => $periodID
                ])
                ->whereIn('invoices.type_documents_id', [9])
                ->asObject()
                ->get()
                ->getResult();

           /* foreach ($invoiceUpdate as $item) {
                $invoice = new Invoice();
                $invoice->update($item->id, ['invoice_status_id' => 18]);
            }*/
            //count($invoiceUpdate)
            if (count($invoiceUpdate) != 0) {
                foreach ($customers as $item) {
                    $invoice = new Invoice();
                    $invoiceId = $invoice->insert([
                        'type_documents_id'             => 109,
                        'invoice_status_id'             => 18,
                        'customers_id'                  => $item->customers_id,
                        'companies_id'                  => $item->companies_id,
                        'status_wallet'                 => 'Pendiente',
                        'send'                          => 'True'
                    ]);

                    $model = new Invoice();
                    $customerDates = $model->select([
                        'payroll_dates.payroll_date',
                    ])
                        ->join('payrolls', 'invoices.id = payrolls.invoice_id')
                        ->join('payroll_dates', 'invoices.id = payroll_dates.invoice_id')
                        ->where([
                            'invoices.companies_id'    => Auth::querys()->companies_id,
                            'invoices.customers_id'    => $item->customers_id,
                            'payrolls.period_id'       => $periodID,
                        ])->asObject()
                        ->groupBy('payroll_dates.payroll_date')
                        ->get()
                        ->getResult();

                    foreach ($customerDates as $date) {
                        $model = new PayrollDate();
                        $model->insert(['invoice_id' => $invoiceId, 'payroll_date' => $date->payroll_date]);
                    }

                    $payroll = new Payroll();
                    $payrollId          = $payroll->insert([
                        'invoice_id'                    => $invoiceId,
                        'period_id'                     => $item->period_id,
                        'type_payroll_adjust_note_id'   => NULL,
                        'settlement_start_date'         => $date_start,
                        'settlement_end_date'           => $date_end,
                        'worked_time'                   => $item->worked_time,
                        'sub_period_id'                 =>  $idSuperiod
                    ]);


                    $model = new Accrued();
                    $accrueds = $model
                        ->select([
                            'sum(accrueds.payment) as payment',
                            'sum(accrueds.other_payments) as other_payments',
                            'accrueds.description',
                            'accrueds.type_overtime_surcharge_id',
                            'accrueds.type_disability_id',
                            'accrueds.quantity',
                            'accrueds.percentage',
                            'accrueds.type_accrued_id'
                        ])
                        ->join('payrolls', 'payrolls.id = accrueds.payroll_id', 'left')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id', 'left')
                        ->join('type_accrueds', 'type_accrueds.id = accrueds.type_accrued_id')
                        ->where([
                            'invoices.customers_id'         => $item->customers_id,
                            'invoices.type_documents_id'    => 9,
                            'payrolls.period_id'            => $periodID
                        ])
                        ->whereIn('type_accrueds.domain', ['atributo',  'severance'])
                       ->groupBy(['accrueds.type_accrued_id'])
                        ->get()
                        ->getResult();

        

                    foreach ($accrueds as $itemAccrued) {
                        $model = new Accrued();
                        $model->insert([
                            'payroll_id'                    => $payrollId,
                            'type_accrued_id'               => $itemAccrued->type_accrued_id,
                            'type_overtime_surcharge_id'    => NULL,
                            'type_disability_id'            => NULL,
                            'start_time'                    => NULL,
                            'end_time'                      => NULL,
                            'payment'                       => $itemAccrued->payment,
                            'quantity'                      => $itemAccrued->quantity,
                            'percentage'                    => $itemAccrued->percentage,
                            'description'                   => $itemAccrued->description,
                            'other_payments'                => $itemAccrued->other_payments
                        ]);
                    }

                    $model = new Accrued();
                    $accrueds = $model
                        ->select(['accrueds.*'])
                        ->join('payrolls', 'payrolls.id = accrueds.payroll_id', 'right')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id', 'left')
                        ->join('type_accrueds', 'type_accrueds.id = accrueds.type_accrued_id', 'left')
                        ->where([
                            'invoices.customers_id'             => $item->customers_id,
                            'invoices.type_documents_id'        => 9,
                            'payrolls.period_id'                => $periodID
                        ])
                        ->whereNotIn('type_accrueds.domain', ['atributo', 'severance'])
                        ->get()
                        ->getResult();


                    foreach ($accrueds as $itemAccrued) {
                        $model = new Accrued();
                        $model->insert([
                            'payroll_id'                    => $payrollId,
                            'type_accrued_id'               => $itemAccrued->type_accrued_id,
                            'type_overtime_surcharge_id'    => $itemAccrued->type_overtime_surcharge_id,
                            'payment'                       => $itemAccrued->payment,
                            'type_disability_id' => $itemAccrued->type_disability_id,
                            'start_time' => $itemAccrued->start_time,
                            'end_time' => $itemAccrued->end_time,
                            'quantity' => $itemAccrued->quantity,
                            'percentage' => $itemAccrued->percentage,
                            'description' => $itemAccrued->description,
                            'other_payments' => $itemAccrued->other_payments,
                        ]);
                    }


                    $model = new Deduction();
                    $deductions = $model
                        ->select(['deductions.*', 'sum(deductions.payment) as payment', 'deductions.description', 'type_deductions.domain'])
                        ->join('payrolls', 'payrolls.id = deductions.payroll_id', 'left')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id', 'left')
                        ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id', 'left')
                        //      ->whereIn('type_deductions.domain', ['eps', 'pension', 'fondossp', 'fondossp_sub', 'atributo'])
                        ->whereIn('type_deductions.group', ['eps', 'pension', 'voluntary_pension', 'withholding_at_source', 'afc', 'cooperative', 'tax_liens', 'supplementary_plan', 'education', 'refund', 'debt'])
                        ->where([
                            'invoices.customers_id'         => $item->customers_id,
                            'invoices.type_documents_id'    => 9,
                            'payrolls.period_id'            => $periodID
                        ])
                        ->groupBy(['type_deductions.group'])
                        ->get()
                        ->getResult();

                    foreach ($deductions as $itemDeductions) {
                        $model = new Deduction();
                        $model->insert([
                            'payroll_id' => $payrollId,
                            'type_deduction_id' => $itemDeductions->type_deduction_id,
                            'type_law_deduction_id' => $itemDeductions->type_law_deduction_id,
                            'payment' => $itemDeductions->payment,
                            'percentage' => $itemDeductions->percentage,
                            'description' => $itemDeductions->description
                        ]);
                    }

                    $model = new Deduction();
                    $deductions = $model
                        ->select('deductions.*')
                        ->join('payrolls', 'payrolls.id = deductions.payroll_id')
                        ->join('invoices', 'invoices.id = payrolls.invoice_id')
                        ->join('type_deductions', 'type_deductions.id = deductions.type_deduction_id', 'left')
                        ->whereNotIn('type_deductions.group', [ 'eps', 'pension', 'voluntary_pension', 'withholding_at_source', 'afc', 'cooperative', 'tax_liens', 'supplementary_plan', 'education', 'refund', 'debt'])
                        //->whereNotIn('type_deductions.domain', ['eps', 'pension', 'fondossp', 'fondossp_sub', 'atributo'])
                        ->where([
                            'invoices.customers_id' => $item->customers_id,
                            'invoices.type_documents_id' => 9,
                            'payrolls.period_id' => $periodID
                        ])
                        ->get()
                        ->getResult();


                    foreach ($deductions as $itemDeductions) {
                        $model = new Deduction();
                        $model->insert([
                            'payroll_id' => $payrollId,
                            'type_deduction_id' => $itemDeductions->type_deduction_id,
                            'type_law_deduction_id' => $itemDeductions->type_law_deduction_id,
                            'payment' => $itemDeductions->payment,
                            'percentage' => $itemDeductions->percentage,
                            'description' => $itemDeductions->description
                        ]);
                    }
                }

            }
        
        return redirect()->to(base_url('payroll_removable'))->with('success', 'El periodo fue consolidado correctamente.');

    }


    public function previsualization($id)
    {
       
 
        $data = $this->group($id, null, Auth::querys()->companies_id);
        $model      = new Customer();
        $customer   = $model->select(['customers.id'])
        ->where(['customers.user_id' => Auth::querys()->id])
        ->asObject()
        ->first();

  

        if(Auth::querys()->role_id == 7) {
            if($customer) {
                if($data['customer_id'] != $customer->id) {
                    return view('errors/html/error_401');
                }
            }
        }
       
        $model = new Company();

        $company = $model->asObject()->find(Auth::querys()->companies_id);
        $res    = $this->sendRequest(getenv('API').'/ubl2.1/previsualization/removable-payroll', $data, 'post', $company->token);

        switch($res->status) {
            case '200':
                $name = 'DNM-'.$data['worker']['identification_number'].'-'.$data['period']['settlement_start_date'].'_hasta_'.$data['period']['settlement_end_date'];
                $this->downloadFile(getenv('API') . "/invoice/".$company->identification_number."/".$name.'.pdf', 'application/pdf',$name.'.pdf');
                break;
            case '422':

                $errorText = '';
                $errors = $res->data;
                foreach($errors->errors as $error => $key) {
                    $errorText .= '<p>'.lang('payroll_errors.payroll_errors.'.$error).'</p>';
                }
                $model = new Invoice();
                $model->update($id, ['errors' => $errorText ]);
                return redirect()->back()->with('errors', 'HTTP 500 - Error del Servidor');

                break;
            default:
                return redirect()->back()->with('errors', 'HTTP 500 - Error del Servidor');
        }
    }


    public function worker()
    {

        $model = new Customer();
        $customer = $model->select(['customers.*', 'customer_worker.*', 'customers.id as id'])
	    ->join('customer_worker', 'customers.id = customer_worker.customer_id')
        ->where(['user_id' => Auth::querys()->id])
        ->asObject()
        ->first();

        $model = new Payroll();
        $payrollCount = $model->join('invoices', 'invoices.id = payrolls.invoice_id')
        ->where([
            'invoices.customers_id'             => $customer->id, 
            'invoices.companies_id'             => Auth::querys()->companies_id,
            'invoices.type_documents_id'        => 109,
            ])
        ->countAllResults();


    
        if(!is_null($customer) && $payrollCount > 0) {
            $model = new SubPeriod();
            $data = $model
                ->select([
                    'sub_periods.id',
                    'sub_periods.name',
                    'pay.id as payroll_id',
                    'invoices.id as invoice_id',
                    'invoices.invoice_status_id',
                    'type_document_identifications.name as type_document_identification_name',
                    'cus.name as first_name',
                    'cus.identification_number',
                    'customer_worker.second_name',
                    'customer_worker.surname',
                    'customer_worker.second_surname',
                    'sub_periods.name',
                    '(SELECT IF(invoices.invoice_status_id = 14, "TRUE", "FALSE")  as validate FROM invoices
                    INNER JOIN customers as cus2 ON cus2.id = invoices.customers_id
                    INNER JOIN payrolls as pay2 ON invoices.id = pay2.invoice_id
                    where pay2.period_id = pay.period_id AND cus2.id = cus.id AND invoices.type_documents_id = 9  LIMIT 1
                ) as validate',
                    '(SELECT IF (periods.month = "Enero",
                    (SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id  GROUP BY pay2.id),
                    (SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id NOT IN (16)  GROUP BY pay2.id)
                   )) as accrueds',
                    '(SELECT IFNULL(sum(ded2.payment),0)
                FROM   payrolls pay2 LEFT JOIN  deductions ded2 ON  ded2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id) AS deductions',
                ])
                ->join('payrolls as pay', 'pay.sub_period_id = sub_periods.id', 'left')
                ->join('periods', 'pay.period_id = periods.id', 'left')
                ->join('invoices', 'invoices.id = pay.invoice_id', 'left')
                ->join('customers as cus', 'invoices.customers_id = cus.id', 'left')
                ->join('type_document_identifications', 'type_document_identifications.id = cus.type_document_identifications_id', 'left')
                ->join('customer_worker', 'cus.id = customer_worker.customer_id')
                ->where([
                    'sub_periods.company_id'            => Auth::querys()->companies_id,
                    'invoices.type_documents_id'        => 109,
                    'invoices.customers_id'             => $customer->id,
			        'invoices.companies_id !='		    => 1
                ])
                ->asObject();


            return view('payroll_renovable/worker', [
                'detachables'           => $data->paginate(10),
                'pager'                 => $data->pager,
                'customer'              => $customer,
                'payrollCount'          => $payrollCount
            ]);
        }else {
            
            $model = new Payroll();
            $invoices = $model->select([
                'pay.id',
                'periods.month',
                'periods.year',
                'invoices.id as invoice_id',
                'invoice_status.id as invoice_status_id',
                'invoice_status.name as invoice_status_name',
                '(SELECT IF (periods.month = "Enero",
                (SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id  GROUP BY pay2.id),
                (SELECT IFNULL(sum(acc2.payment + IFNULL( acc2.other_payments,0)),0) FROM   payrolls pay2 LEFT JOIN  accrueds acc2 ON  acc2.payroll_id = pay2.id WHERE pay2.id = pay.id and acc2.type_accrued_id NOT IN (16)  GROUP BY pay2.id)
               )) as accrueds',
                '(SELECT IFNULL(sum(ded2.payment),0) 
                FROM   payrolls pay2 LEFT JOIN  deductions ded2 ON  ded2.payroll_id = pay2.id WHERE pay2.id = pay.id GROUP BY pay2.id) AS deduction' 
                ])
            ->from('payrolls as pay')
            ->join('invoices', 'invoices.id = pay.invoice_id')
            ->join('periods', 'periods.id = pay.period_id')
            ->join('invoice_status', 'invoices.invoice_status_id = invoice_status.id')
            ->join('customers', 'customers.id = invoices.customers_id')
            ->join('type_document_identifications', 'customers.type_document_identifications_id = type_document_identifications.id')
            ->where([
                'customers.user_id'                         => Auth::querys()->id,
                'customers.companies_id'                    => Auth::querys()->companies_id,
                'customers.type_customer_id'                => 3,
                'customers.deleted_at'                      => null,
                'invoices.companies_id !='		            => 1
            ])->whereIn('invoices.type_documents_id', [9])
            ->groupBy([
                'pay.id', 
                'invoice_status.id'
                ])
            ->orderBy('periods.id', 'desc')
            ->asObject();

            return view('payroll_renovable/worker', [
                'invoices'              => $invoices->paginate(10),
                'pager'                 => $invoices->pager,
                'customer'              => $customer,
                'payrollCount'          => $payrollCount
            ]);

        }
    }


    public function searchShow()
    {
        $data = [];
        if(!empty($this->request->getGet('first_name'))) {
            $data['cus.name'] = $this->request->getGet('first_name');
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
            $data['cus.type_document_identifications_id'] = $this->request->getGet('type_document_id');
        }

        if(!empty($this->request->getGet('identification_number'))) {
            $data['cus.identification_number'] = $this->request->getGet('identification_number');
        }


        return $data;
    }
}