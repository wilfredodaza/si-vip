<?php


namespace App\Models;


use CodeIgniter\Model;

class Applicant extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'applicant';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $allowedFields = [
        'id',
        'application_date',
        'company_name',
        'email_confirmation',
        'nit',
        'phone',
        'direccion',
        'adress',
        'legal_representative',
        'type_document',
        'num_documento',
        'email',
        'email_confirmation',
        'contract',
        'autorizacion',
        'status',
        'seller'
        ];
}