<?php


namespace App\Models;


use CodeIgniter\Model;

class Payroll extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'payrolls';
    protected $allowedFields = [
        'invoice_id',
        'payroll_period_id',
        'settlement_start_date',
        'settlement_end_date',
        'worked_time',
        'period_id',
        'sub_period_id',
        'type_payroll_adjust_note_id'
    ];





}