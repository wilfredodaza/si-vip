<?php
namespace App\Models;

use CodeIgniter\Model;

class ShopifyProductsVatExempt extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'shopify_products_vat_exempt';
    protected $allowedFields = [
        'id',
        'companies_id',
        'integration_shopify_id',
        'product_name',
        'id_product_shopify',
        'sku_shopify',
        'status'
    ];







}