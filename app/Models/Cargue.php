<?php

namespace App\Models;

use CodeIgniter\Model;

class Cargue extends Model
{
    protected $table = 'cargue';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'date',
        'payroll_period',
        'month_payroll',
        'load_number',
        'nit',
        'data',
        'payment_dates',
        'status',
        'period_id',
        'year',
        'type_document_payroll'
    ];
}
