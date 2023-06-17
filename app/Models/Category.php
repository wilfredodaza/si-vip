<?php

namespace App\Models;


use \CodeIgniter\Model;

class Category extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'category';

    protected $allowedFields = [
        'name',
        'expenses',
        'payroll'

    ];
}