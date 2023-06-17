<?php


namespace App\Models;

use CodeIgniter\Model;

class Customer extends Model
{

    protected $table            = 'customers';
    protected $primaryKey       = 'id';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'id',	
        'name',
        'type_document_identifications_id',
        'identification_number',
        'dv',
        'phone',
        'address',
        'email',
        'email2',
        'email3',
        'merchant_registration',
        'type_customer_id',
        'type_regime_id',
        'municipality_id',
        'companies_id',
        'type_organization_id',
        'bank_certificate',
        'status',
        'user_id',
        'firm',
        'rut',
        'status',
        'type_liability_id',
        'postal_code',
        'headquarters_id',
        'quota',
        'payment_policies',
        'type_client_status',
        'frequency',
        'neighborhood'
    ];

}