<?php


namespace App\Models;


use CodeIgniter\Model;

class LineInvoice extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'line_invoices';
    protected $allowedFields = [
        'invoices_id',
        'discount_amount',
        'discounts_id',
        'quantity',
        'line_extension_amount',
        'price_amount',
        'cost_amount',
        'products_id',
        'description',
        'provider_id',
        'code',
        'upload',
        'type_generation_transmition_id',
        'start_date',
        'cost_center_id',
        'payroll'
    ];

    public function Rentas($id){
        $product = $this->builder('line_invoice_taxs')
            ->join('taxes', 'line_invoice_taxs.taxes_id = taxes.id')
            ->where(["line_invoices_id" => $id])->get()->getResult();
        return $product; 
    }
}