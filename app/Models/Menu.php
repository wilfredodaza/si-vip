<?php


namespace App\Models;


use CodeIgniter\Model;

class Menu extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'menus';
    protected $allowedFields = [
        'id',
        'option',
        'url',
        'icon',
        'position',
        'type',
        'references',
        'status',
        'component',
        'title',
        'description',
        'table',
        'to_list'
    ];

}