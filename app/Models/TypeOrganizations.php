<?php


namespace App\Models;


use CodeIgniter\Model;

class TypeOrganizations extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'type_organizations';
    protected $allowedFields = [
        'id',
        'name',
        'code'
        ];
}