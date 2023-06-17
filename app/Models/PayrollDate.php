<?php

namespace App\Models;

use CodeIgniter\Model;

class PayrollDate extends Model
{
    protected $table = 'payroll_dates';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'invoice_id',
        'payroll_date'
    ];
}