<?php


namespace App\Models;


use CodeIgniter\Model;

class CostCenter extends Model
{
    protected $table = 'cost_center';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'companies_id',
        'name',
        'code',
        'status',
        'created_at',
        'update_at'
    ];
}