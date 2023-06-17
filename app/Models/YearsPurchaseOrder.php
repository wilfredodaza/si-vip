<?php


namespace App\Models;

use CodeIgniter\Model;

class YearsPurchaseOrder extends Model
{

    protected $table            = 'years_purchase_order';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',	
        'year',
    ];

}