<?php


namespace App\Models;


use CodeIgniter\Model;

class LineInvoiceTax extends Model
{
    protected $primaryKey   = 'id';
    protected $table        = 'line_invoice_taxs';
    protected $allowedFields = [
        'taxes_id',
        'tax_amount',
        'percent',
        'taxable_amount',
        'line_invoices_id'
    ];

}