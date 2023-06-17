<?php


namespace App\Models;


use CodeIgniter\Model;

class TypeCustomer extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'type_customer';
    protected $allowedFields = [
        'id',
        'name'
        ];
}