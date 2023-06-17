<?php

namespace App\Models;
use CodeIgniter\Model;

class SubPeriod extends Model
{
    protected $table = 'sub_periods';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'name',
        'company_id'
    ];
}