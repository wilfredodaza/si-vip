<?php


namespace App\Models;


use CodeIgniter\Model;

class Period extends Model
{
    protected $table            = 'periods';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $allowedFields    = [
        'month',
        'year'
    ];
}