<?php


namespace App\Models;


use CodeIgniter\Model;

class Document extends Model
{
    protected $table        = 'documents';
    protected $primaryKey   = 'id';
    protected $allowedFields = [
        'name',
        'new_name',
        'extension',
        'created_at',
        'invoice_id',
        'companies_id',
        'document_status_id',
        'provider',
        'uuid',
        'zip',
        'payment_file',
        'description'
    ];
}