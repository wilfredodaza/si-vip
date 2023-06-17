<?php

namespace App\Models;

use CodeIgniter\Model;

class Module extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'modules';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'id',
        'name',
        'img',
        'position',
        'status'
    ];
}

