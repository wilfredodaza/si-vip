<?php


namespace App\Models;


use CodeIgniter\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'token',
        'company',
        'identification_number',
        'dv',
        'merchant_registration',
        'address',
        'email',
        'phone',
        'taxes_id',
        'type_currencies_id',
        'type_liabilities_id',
        'type_organizations_id',
        'type_document_identifications_id',
        'countries_id',
        'departments_id',
        'municipalities_id',
        'languages_id',
        'type_operations_id',
        'type_regimes_id',
        'type_environments_id',
        'headquarters_id',
        'created_at'
    ];
}