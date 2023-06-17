<?php


namespace App\Models;


use CodeIgniter\Model;

class Accrued extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'accrueds';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'id',
        'type_accrued_id',
        'payroll_id',
        'type_overtime_surcharge_id',
        'type_disability_id',
        'payment',
        'start_time',
        'end_time',
        'quantity',
        'percentage',
        'description',
        'other_payments'
    ];





}