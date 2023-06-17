<?php


namespace App\Models;


use CodeIgniter\Model;

class AccountingAcount extends Model
{
    protected $table = 'accounting_account';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'companies_id',
        'type_accounting_account_id',
        'code',
        'name',
        'percent',
        'nature',
        'status',
        'created_at'
    ];
}