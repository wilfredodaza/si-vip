<?php


namespace App\Models;


use CodeIgniter\Model;

class Packages extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'packages';
    protected $allowedFields = [
        'id',
        'name',
        'description',
        'quantity_document',
        'price'
    ];
}