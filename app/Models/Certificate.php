<?php

namespace App\Models;

use CodeIgniter\Model;

class Certificate extends Model
{
    protected $table = "certificates";
    protected $primaryKey = "id";
    protected $useTimestamps    = true;
    protected $allowedFields = [
        'companies_id',
        'name',
        'password'
    ];
}