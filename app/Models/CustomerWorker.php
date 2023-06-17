<?php


namespace App\Models;


use CodeIgniter\Model;

class CustomerWorker extends Model
{
    protected $table            = 'customer_worker';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $useSoftDeletes   = true;
    protected $allowedFields = [
        'type_worker_id',
        'sub_type_worker_id',
        'municipality_id',
        'type_contract_id',
        'payment_method_id',
        'customer_id',
        'high_risk_pension',
        'surname',
        'payroll_period_id',
        'second_surname',
        'second_name',
        'integral_salary',
        'salary',
        'worker_code',
        'bank_id',
        'bank_account_type_id',
        'account_number',
        'admision_date',
        'retirement_date',
        'work',
        'transportation_assistance',
        'non_salary_payment',
        'other_payments',
        'birthday',
        'withdrawal_reason',
        'number_people'
    ];
}