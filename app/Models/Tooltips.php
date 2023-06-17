<?php


namespace App\Models;


use CodeIgniter\Model;

class Tooltips extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'tooltips';
    protected $allowedFields = [
        'id',
        'documento',
        'ayuda'
        ];
}