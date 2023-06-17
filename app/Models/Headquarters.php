<?php


namespace App\Models;


use CodeIgniter\Model;

class Headquarters extends Model
{
    protected $table = 'headquarters';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'id',
        'name',
    ];
}
