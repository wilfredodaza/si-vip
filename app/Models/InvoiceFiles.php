<?php

namespace App\Models;


use \CodeIgniter\Model;

class InvoiceFiles extends Model
{
    protected $table = "invoices_files";
    protected $primaryKey = "id";
    protected $allowedFields = [
        'invoices_id',
        'invoices_type_files_id',
        'name',
        'number',
        'observation',
        'users_id',
        'status',
        'created_at',
        'updated_at'
      ];
}