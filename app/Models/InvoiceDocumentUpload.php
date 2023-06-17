<?php


namespace App\Models;


use CodeIgniter\Model;

class InvoiceDocumentUpload extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'invoice_document_upload';
    protected $allowedFields = [
        'title',
        'file',
        'invoice_id'
    ];
}