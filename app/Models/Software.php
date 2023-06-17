<?php

namespace App\Models;

use CodeIgniter\Model;

class Software extends Model
{
    protected $table = 'software';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'companies_id',
        'identifier',
        'pin',
        'identifier_payroll',
        'pin_payroll'
    ];
}
