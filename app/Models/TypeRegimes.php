<?php


namespace App\Models;


use CodeIgniter\Model;

class TypeRegimes extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'type_regimes';
    protected $allowedFields = [
        'id',
        'name',
        'code'
        ];
}