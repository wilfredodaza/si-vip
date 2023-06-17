<?php


namespace App\Models;


use CodeIgniter\Model;

class ModuleShopping extends Model
{
    protected $table = 'module_shopping';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'section_shopping_id',
        'status_shopping_id',
        'invoices_id',
        'created_at',
        'updated_at'
    ];
}