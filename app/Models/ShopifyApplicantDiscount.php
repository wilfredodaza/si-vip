<?php


namespace App\Models;


use CodeIgniter\Model;

class ShopifyApplicantDiscount extends Model
{
    protected $primaryKey = 'shopify_applicant_discount_id';
    protected $table = 'shopify_applicant_discount';


    protected $allowedFields = [
        'shopify_applicant_discount_id',
        'companies_id',
        'order_number_shopify',
        'percentage'
    ];







}