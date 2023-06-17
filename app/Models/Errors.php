<?php


namespace App\Models;


use CodeIgniter\Model;

class Errors extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'errors';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';


    protected $allowedFields = [
        'id',
        'code',
        'breakdown',
        'solution'
    ];







}