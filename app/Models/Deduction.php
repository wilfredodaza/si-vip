<?php


namespace App\Models;


use CodeIgniter\Model;

class Deduction extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'deductions';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'type_deduction_id',
        'payroll_id',
        'type_law_deduction_id',
        'payment',
        'percentage',
        'description'
    ];





}