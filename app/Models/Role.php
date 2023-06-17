<?php


namespace App\Models;


use CodeIgniter\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $allowedFields = [
        'name',
        'description',
        'type',
        'companies_id',
        'status'
    ];
}