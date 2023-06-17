<?php


namespace App\Models;


use CodeIgniter\Model;

class AssociateDocument extends Model
{
    protected $table        = 'associate_document';
    protected $primaryKey   = 'id';
    protected $allowedFields = [
        'id',
        'documents_id',
        'extension',
        'name',
        'new_name'
    ];
}