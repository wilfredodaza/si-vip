<?php


namespace App\Models;


use CodeIgniter\Model;

class Wallet extends Model
{
    protected $table = 'wallet';
    protected $primaryKey = 'id';
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $allowedFields = [
        'value',
        'soport',
        'description',
        'invoices_id',
        'created_at',
        'payment_method_id',
        'user_id',
        'invoices_pay'
    ];
}