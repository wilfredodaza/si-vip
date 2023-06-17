<?php


namespace App\Models;


use CodeIgniter\Model;

class ShoppingFiles extends Model
{
    protected $table = 'shopping_files';
    protected $primaryKey = 'id';
    protected $allowedFields = [
    'shopping_email_id',
    'name',
    'created_at',
    'update_at',
    ];
}