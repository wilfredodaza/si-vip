<?php


namespace App\Models;


use CodeIgniter\Model;

class IntegrationTrafficLight extends Model
{
    protected $primaryKey = 'id';
    protected $table = 'integration_traffic_light';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';


    protected $allowedFields = [
        'id',
        'companies_id',
        'id_shopify',
        'number_mfl',
        'type_document_id',
        'integration_shopify_id',
        'number_app',
        'observations',
        'uuid',
        'status',
        'check_return',
        'created_at',
        'updated_at',
        'deleted_at'
    ];







}