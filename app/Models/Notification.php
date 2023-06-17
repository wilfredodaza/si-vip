<?php

namespace App\Models;


use CodeIgniter\Model;

class Notification extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'notifications';
    protected $allowedFields = [
        'view',
        'id',
        'body',
        'status',
        'title',
        'icon',
        'color',
        'created_at',
        'companies_id',
        'type_document_id',
        'url'
        ];
}
