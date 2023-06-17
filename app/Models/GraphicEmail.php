<?php


namespace App\Models;


use CodeIgniter\Model;

class GraphicEmail extends Model
{
    protected $table        = 'graphic_email';
    protected $primaryKey   = 'id';
    protected $allowedFields = ['setting_json', 'companies_id'];

}