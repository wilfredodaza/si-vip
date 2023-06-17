<?php


namespace App\Models;


use CodeIgniter\Model;

class Resolution extends  Model
{
    protected $table = 'resolutions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'companies_id',
        'type_documents_id',
        'prefix',
        'resolution',
        'resolution_date',
        'technical_key',
        'from',
        'to',
        'date_from',
        'date_to'
    ];
}