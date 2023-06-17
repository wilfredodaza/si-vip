<?php


namespace App\Models;

use CodeIgniter\Model;

class BudgetPurchaseOrder extends Model
{

    protected $table            = 'budgetpurchaseorder';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',	
        'year',
        'month',
        'value'
    ];

}