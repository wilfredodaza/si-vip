<?php


namespace App\Models;


use CodeIgniter\Model;

class DocumentInvoice extends Model
{
    protected $DBGroup          = 'api';
    protected $table            = 'documents';
    protected $primaryKey       = 'id';
}