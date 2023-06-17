<?php


namespace App\Models;


use CodeIgniter\Model;

class IntegrationShopify extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'integration_shopify';


    protected $allowedFields = [
        'id',
        'companies_id',
        'resolucion_id',
        'name_shopify',
        'token',
        'status_invoice',
        'status'
    ];







}
