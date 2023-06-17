<?php


namespace App\Models;


use CodeIgniter\Model;

class Tracing extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'tracing';
    protected $allowedFields = [
        'id',
        'date',
        'applicant_id',
        'log',
        'user_id'
    ];
}