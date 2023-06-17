<?php

namespace App\Models;


use CodeIgniter\Model;

class OtherBank extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'other_bank';
    protected $allowedFields = [
        'id',
        'companies_id',
        'name',
        'bank_id',
        'status',
    ];
}
