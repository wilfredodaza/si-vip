<?php


namespace App\Models;


use CodeIgniter\Model;

class IntegrationShopifyConsolidation extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'integration_shopify_consolidation';

    protected $allowedFields = [
        'id',
        'companies_id',
        'integration_shopify_id',
        'integrationTraffic',
        'note'
    ];

}
