<?php


namespace App\Models;


use CodeIgniter\Model;

class Sellers extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'sellers';
    protected $allowedFields = [
        'id',
        'name',
        'identification_number',
        'phone',
        'status',
        'link'
    ];
}