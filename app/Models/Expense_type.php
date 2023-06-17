<?php


namespace App\Models;


use CodeIgniter\Model;

class Expense_type extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'expense_type';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'id',
        'name',
        'status',
        'solution'
    ];

}
