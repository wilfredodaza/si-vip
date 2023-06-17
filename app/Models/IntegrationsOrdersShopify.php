<?php
namespace App\Models;

use CodeIgniter\Model;

class IntegrationsOrdersShopify extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'integrations_orders_shopify';
    protected $allowedFields = [
        'id',
        'companies_id',
        'shopify_number',
        'integration_shopify_id',
        'shopify_id',
        'status',
        'create_at_shopify'
    ];







}