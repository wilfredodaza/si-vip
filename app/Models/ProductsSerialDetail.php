<?php


namespace App\Models;


use CodeIgniter\Model;

class ProductsSerialDetail extends Model
{

    protected $table            = 'products_serial_detail';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'products_serial_id',
        'invoices_id',
    ];

}