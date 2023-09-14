<?php


namespace App\Models;


use CodeIgniter\Model;

class WalletLineInvoice extends Model
{
    protected $table            = 'wallet_line_invoice';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'value',
        'line_invoice_id',
        'wallet_id',
    ];
}