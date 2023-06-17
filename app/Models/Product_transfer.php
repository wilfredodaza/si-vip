<?php


namespace App\Models;


use CodeIgniter\Model;

class Product_transfer extends Model
{

    protected $table            = 'product_transfer';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'companies_id',
        'product_id',
        'destination_product_id',
        'quantity',
        'destination_headquarters',
        'user_id',
        'type_document_id',
        'created_at'
    ];

}
