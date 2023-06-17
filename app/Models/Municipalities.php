<?php


namespace App\Models;


use CodeIgniter\Model;

class Municipalities extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'municipalities';
    protected $allowedFields = [
        'id',
        'name',
        'code'
        ];
}