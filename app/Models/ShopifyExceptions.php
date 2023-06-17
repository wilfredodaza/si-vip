<?php


namespace App\Models;


use CodeIgniter\Model;

class ShopifyExceptions extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'shopify_exceptions';


    protected $allowedFields = [
        'id',
        'companies_id',
        'shopify_app_id',
        'shop'
    ];







}
