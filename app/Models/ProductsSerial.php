<?php


namespace App\Models;


use CodeIgniter\Model;

class ProductsSerial extends Model
{

    protected $table            = 'products_serial';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'products_id',
        'serial',
        'status',
    ];

}