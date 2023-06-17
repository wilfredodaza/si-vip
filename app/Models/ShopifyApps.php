<?php


namespace App\Models;


use CodeIgniter\Model;

class ShopifyApps extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'shopify_apps';


    protected $allowedFields = [
        'id',
        'name',
        'name_app',
        'client_id',
        'secret_id',
        'redirect_url',
        'type_app',
        'status'
    ];







}
