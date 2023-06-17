<?php


namespace App\Models;


use CodeIgniter\Model;

class Integrations extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'integrations';


    protected $allowedFields = [
        'id',
        'name',
        'icon',
        'description',
        'status'
    ];







}
