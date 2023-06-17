<?php


namespace App\Models;


use CodeIgniter\Model;

class ProductsDetails extends Model
{

    protected $table = 'products_details';
    protected $primaryKey = 'id_products_details';
    protected $allowedFields = [
        'id_products_detailsPrimaria',
        'id_product',
        'id_invoices',
        'created_at',
        'policy_type',
        'cost_value',
        'observations',
        'status'
    ];

}
