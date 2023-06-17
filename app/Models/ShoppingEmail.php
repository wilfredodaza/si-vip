<?php


namespace App\Models;


use CodeIgniter\Model;

class ShoppingEmail extends Model
{
    protected $table = 'shopping_emails';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'companies_id',
        'invoices_id',
        'subject',
        'body',
        'name',
        'from_address',
        'created_at',
        'updated_at'
    ];

    public function Files($id){
        $files = $this->builder('invoices_files')
            ->where(["invoices_id" => $id])->get()->getResult();
        return $files; 
    }
}