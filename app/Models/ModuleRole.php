<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleRole extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'module_role';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id',
        'module_id',
        'role_id'
    ];
}

