<?php
namespace App\Models;

use CodeIgniter\Model;

class ShopifyLog extends Model
{
    protected $primaryKey = 'shopify_log_id';
    protected $table = 'shopify_log';
    protected $allowedFields = [
        'shopify_log_id',
        'companies_id',
        'traffic_id',
        'order_number',
        'message'
    ];







}
