<?php

namespace App\Models;

use CodeIgniter\Model;

class WithholdingInvoice extends Model
{
    protected $table        = 'withholding_invoices';
    protected $primarykey   = 'id';
    protected $allowedFields    = [
        'id',
        'accounting_account_id',
        'percent',
        'invoice_id'   
    ];
}