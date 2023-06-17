<?php


namespace App\Models;



use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'id',
        'name',
        'username',
        'email',
        'password',
        'status',
        'role_id',
        'photo',
        'companies_id',
        'user_id'
    ];
}