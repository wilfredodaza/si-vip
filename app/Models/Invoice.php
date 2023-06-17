<?php


namespace App\Models;


use CodeIgniter\Model;

class Invoice extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'invoices';
    protected $useTimestamps    = true;
    protected $useSoftDeletes   = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    
    protected $allowedFields = [
        'resolution',
        'resolution_id',
        'allowance_total_amount',
        'charge_total_amount',
        'created_at',
        'prefix',
        'customers_id',
        'duration_measure',
        'invoice_status_id',
        'line_extesion_amount',
        'notes',
        'payable_amount',
        'payment_due_date',
        'payment_forms_id',
        'payment_methods_id',
        'tax_exclusive_amount',
        'tax_inclusive_amount',
        'type_documents_id',
        'pre_paid_amount',
        'companies_id',
        'uuid',
        'resolution_credit',
        'issue_date',
        'status_wallet',
        'idcurrency',
        'calculationrate',
        'calculationratedate',
        'user_id',
        'seller_id',
        'send',
        'zipkey',
        'delevery_term_id',
        'errors',
        'response',
        'headquarters_id',
        'discrepancy_response_id'
        ];


        public function getLineInvoices($id){
            $product = $this->builder('line_invoices')
                ->select(['line_invoices.*', 'products.name', 'category.payroll as nomina'])
                ->join('products', 'products.id = line_invoices.products_id', 'left')
                ->join('category', 'category.id = products.category_id', 'left')
                ->where(["invoices_id" => $id, 'line_invoices.payroll' => 'false'])->get()->getResult();
            return $product; 
        }




}