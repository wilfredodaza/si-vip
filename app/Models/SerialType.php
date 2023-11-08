<?php


namespace App\Models;


use CodeIgniter\Model;

class SerialType extends Model
{

    protected $table            = 'serial_type';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'name',
    ];

}