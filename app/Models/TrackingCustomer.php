<?php


namespace App\Models;


use CodeIgniter\Model;

class TrackingCustomer extends Model
{
    protected $table = 'tracking_customer';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'type_tracking_id',
        'companies_id',
        'table_id',
        'created_at',
        'message',
        'username',
        'file'
    ];
}