<?php


namespace App\Models;


use CodeIgniter\Model;

class TypeDocumentIdentifications extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'type_document_identifications';
    protected $allowedFields = [
        'id',
        'name',
        'code'
        ];
}